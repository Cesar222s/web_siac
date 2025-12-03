<?php

namespace App\Services;

use Google\Cloud\BigQuery\BigQueryClient;

class BigQueryNhtsaService
{
    protected BigQueryClient $client;
    protected string $projectId;
    protected string $dataset;
    protected string $table;

    public function __construct()
    {
        // Defaults to public dataset bigquery-public-data.nhtsa_traffic_fatalities
        $this->projectId = env('BQP_PROJECT_ID', 'bigquery-public-data');
        $this->dataset   = env('BQP_DATASET', 'nhtsa_traffic_fatalities');
        $this->table     = env('BQP_TABLE', 'fatalities');
        $keyFilePath     = env('BIGQUERY_KEYFILE', ''); // optional for authenticated projects

        $config = ['projectId' => $this->projectId];
        if ($keyFilePath) { $config['keyFilePath'] = $keyFilePath; }
        $this->client = new BigQueryClient($config);
    }

    public function fetchForProfile(array $profile, int $limit = 10000): array
    {
        // Profile: age, experience_years, usual_location (state or city), usual_hours (array of ints)
        $state = $profile['state_name'] ?? null;
        $city  = $profile['city'] ?? null;
        $age   = $profile['age'] ?? null;
        $hours = $profile['hours'] ?? [];
        $hourFilter = '';
        if (is_array($hours) && count($hours)) {
            $join = implode(',', array_map('intval', $hours));
            $hourFilter = "AND hour_of_crash IN (".$join.")";
        }
        $locFilter = '';
        if ($state) { $locFilter .= "AND state_name = '".addslashes($state)."' "; }
        if ($city)  { $locFilter .= "AND city = '".addslashes($city)."' "; }

        $query = sprintf(
            "SELECT state_name, city, hour_of_crash, day_of_week, number_of_fatalities, number_of_drunk_drivers, weather_condition, light_condition, driver_age, driver_sex
             FROM `%s.%s.%s`
             WHERE 1=1 %s %s
             LIMIT %d",
            $this->projectId, $this->dataset, $this->table,
            $locFilter, $hourFilter, $limit
        );
        $job = $this->client->runQuery($this->client->query($query));
        $rows = [];
        foreach ($job->rows() as $row) {
            $rows[] = [
                'state_name' => (string)($row['state_name'] ?? ''),
                'city' => (string)($row['city'] ?? ''),
                'hour_of_crash' => (int)($row['hour_of_crash'] ?? -1),
                'day_of_week' => (string)($row['day_of_week'] ?? ''),
                'number_of_fatalities' => (int)($row['number_of_fatalities'] ?? 0),
                'number_of_drunk_drivers' => (int)($row['number_of_drunk_drivers'] ?? 0),
                'weather_condition' => (string)($row['weather_condition'] ?? ''),
                'light_condition' => (string)($row['light_condition'] ?? ''),
                'driver_age' => isset($row['driver_age']) ? (int)$row['driver_age'] : null,
                'driver_sex' => (string)($row['driver_sex'] ?? ''),
            ];
        }
        return $rows;
    }

    public function aggregateHotspots(array $rows): array
    {
        // Aggregate by state/city/hour for counts and fatalities
        $agg = [];
        foreach ($rows as $r) {
            $key = $r['state_name'].'|'.$r['city'].'|'.$r['hour_of_crash'];
            if (!isset($agg[$key])) {
                $agg[$key] = ['state_name'=>$r['state_name'],'city'=>$r['city'],'hour_of_crash'=>$r['hour_of_crash'],'count'=>0,'fatalities'=>0];
            }
            $agg[$key]['count']++;
            $agg[$key]['fatalities'] += $r['number_of_fatalities'];
        }
        // sort by fatalities then count
        usort($agg, function($a,$b){
            return ($b['fatalities'] <=> $a['fatalities']) ?: ($b['count'] <=> $a['count']);
        });
        return array_slice($agg, 0, 50);
    }

    public function riskyTimes(array $rows): array
    {
        $byHour = array_fill(0, 24, ['count'=>0,'fatalities'=>0,'drunk'=>0]);
        foreach ($rows as $r) {
            $h = $r['hour_of_crash'];
            if ($h >= 0 && $h < 24) {
                $byHour[$h]['count']++;
                $byHour[$h]['fatalities'] += $r['number_of_fatalities'];
                $byHour[$h]['drunk'] += $r['number_of_drunk_drivers'];
            }
        }
        $out = [];
        for ($h=0;$h<24;$h++) { $out[] = ['hour'=>$h] + $byHour[$h]; }
        usort($out, function($a,$b){ return ($b['fatalities'] <=> $a['fatalities']) ?: ($b['count'] <=> $a['count']); });
        return $out;
    }

    public function score(array $rows, array $profile): array
    {
        // Simple risk score combining fatalities density, drunk drivers ratio, and profile alignment
        $total = count($rows);
        if ($total === 0) return ['score'=>0,'factors'=>[]];
        $fatal = array_sum(array_map(fn($r)=>$r['number_of_fatalities'], $rows));
        $drunk = array_sum(array_map(fn($r)=>$r['number_of_drunk_drivers'], $rows));
        $avgFatal = $fatal / max(1,$total);
        $avgDrunk = $drunk / max(1,$total);
        // Profile alignment: if driver age near dataset driver_age median
        $ages = array_values(array_filter(array_map(fn($r)=>$r['driver_age'], $rows), fn($a)=>$a!==null));
        sort($ages); $medianAge = $ages ? $ages[intdiv(count($ages),2)] : null;
        $ageScore = 0.0;
        if (isset($profile['age']) && $medianAge !== null) {
            $diff = abs($profile['age'] - $medianAge);
            $ageScore = max(0, 1.0 - min(1.0, $diff/20.0)); // closer to median -> higher risk alignment
        }
        // Compose risk score (0-100)
        $score = min(100, round( (
            (min(1.0, $avgFatal/2.0) * 40) +
            (min(1.0, $avgDrunk/1.5) * 40) +
            ($ageScore * 20)
        ), 2));
        return [
            'score' => $score,
            'factors' => [
                'avgFatalPerEvent' => round($avgFatal,2),
                'avgDrunkDriversPerEvent' => round($avgDrunk,2),
                'medianDriverAge' => $medianAge,
            ],
        ];
    }
}
