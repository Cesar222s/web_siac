<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BigQueryNhtsaService;

class RiskController extends Controller
{
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
