<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BigQueryNhtsaService;

class RiskController extends Controller
{
    public function analysis(Request $request)
    {
        // Map inputs from profile fields
        $driverAge = $request->input('driver_age');
        $expYears = $request->input('experience_years');
        $usualLocation = trim((string)$request->input('usual_location', ''));
        $state = null; $city = null; $edo = null; $mpio = null;
        if ($usualLocation) {
            $parts = array_map('trim', explode(',', $usualLocation));
            if (count($parts) >= 2) { $state = $parts[0]; $city = $parts[1]; $edo = $parts[0]; $mpio = $parts[1]; }
            else { $state = $usualLocation; $edo = $usualLocation; }
        }
        $usualHours = $request->input('usual_hours');

        $python = env('PYTHON_EXE', 'python');
        $script = base_path('python/risk_analysis.py');
        $projectId = env('BQP_PROJECT_ID', 'bigquery-public-data');
        $dataset = env('BQP_DATASET', 'nhtsa_traffic_fatalities');
        $table = env('BQP_TABLE', 'fatalities');
        $keyfile = env('BIGQUERY_KEYFILE', '');

        $cmd = sprintf('%s %s --driver_age %s --experience_years %s --state_name %s --city %s --usual_hours %s --project_id %s --dataset %s --table %s %s %s %s',
            escapeshellcmd($python),
            escapeshellarg($script),
            escapeshellarg((string)$driverAge),
            escapeshellarg((string)$expYears),
            escapeshellarg((string)$state),
            escapeshellarg((string)$city),
            escapeshellarg((string)$usualHours),
            escapeshellarg($projectId),
            escapeshellarg($dataset),
            escapeshellarg($table),
            $keyfile ? ('--keyfile ' . escapeshellarg($keyfile)) : '',
            $edo ? ('--edo ' . escapeshellarg($edo)) : '',
            $mpio ? ('--mpio ' . escapeshellarg($mpio)) : ''
        );

        $output = [];
        $exitCode = 0;
        @exec($cmd, $output, $exitCode);
        if ($exitCode !== 0 || empty($output)) {
            return response()->json([
                'error' => 'No se pudo ejecutar el análisis en Python.',
                'details' => $exitCode,
            ], 500);
        }
        $json = json_decode(implode("\n", $output), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'error' => 'Respuesta inválida del análisis en Python.',
                'raw' => implode("\n", $output),
            ], 500);
        }
        return response()->json($json);
    }
    public function score(Request $request)
    {
        $svc = new BigQueryNhtsaService();
        $profile = [
            'state_name' => $request->input('state_name'),
            'city' => $request->input('city'),
            'age' => $request->input('age') ? (int)$request->input('age') : null,
            'hours' => $this->parseHours($request->input('hours')), // e.g. "7,8,9,18,19"
        ];
        $rows = $svc->fetchForProfile($profile, 10000);
        $risk = $svc->score($rows, $profile);
        return response()->json([
            'profile' => $profile,
            'risk' => $risk,
        ]);
    }

    public function hotspots(Request $request)
    {
        $svc = new BigQueryNhtsaService();
        $profile = [
            'state_name' => $request->input('state_name'),
            'city' => $request->input('city'),
            'hours' => $this->parseHours($request->input('hours')),
        ];
        $rows = $svc->fetchForProfile($profile, 15000);
        $hot = $svc->aggregateHotspots($rows);
        return response()->json(['hotspots' => $hot]);
    }

    public function times(Request $request)
    {
        $svc = new BigQueryNhtsaService();
        $profile = [
            'state_name' => $request->input('state_name'),
            'city' => $request->input('city'),
        ];
        $rows = $svc->fetchForProfile($profile, 20000);
        $times = $svc->riskyTimes($rows);
        return response()->json(['hours' => $times]);
    }

    private function parseHours($hours)
    {
        if (!$hours) return [];
        if (is_array($hours)) return array_map('intval', $hours);
        return array_map('intval', array_filter(array_map('trim', explode(',', (string)$hours))));
    }
}
