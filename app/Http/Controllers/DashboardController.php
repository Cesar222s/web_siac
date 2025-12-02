<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KMeansService;
use App\Services\BigQueryProvider;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Parámetros (extensibles): tipo, hora, día
        $k = (int)($request->input('k', 5));
        $useBigQuery = (bool)env('BIGQUERY_ENABLED', false);
        if ($useBigQuery) {
            $provider = new BigQueryProvider();
            $pointsRows = $provider->fetchPoints(15000);
            // if we have lat/lon, build a temporary CSV-like structure in-memory for KMeansService
            $columns = ['lat','lon'];
            $rows = [];
            foreach ($pointsRows as $r) { $rows[] = ['lat' => $r[0], 'lon' => $r[1]]; }
            // Run simple k-means on lat/lon
            $result = $this->clusterFromRows($rows, $columns, $k, 25);
        } else {
            $filePath = storage_path('app/data/atus_2017.csv');
            $service = new KMeansService($filePath, $k, 25);
            $result = $service->cluster();
        }

        $zonesCount = is_array($result['centroids'] ?? null) ? count($result['centroids']) : 0;

        return view('dashboard', [
            'k' => $k,
            'available' => $result['available'] ?? true,
            'message' => $result['message'] ?? null,
            'zonesCount' => $zonesCount,
            'clusters' => $result['centroids'] ?? [],
            'counts' => $result['counts'] ?? [],
            'points' => $result['points'] ?? [],
            'columns' => $result['columns'] ?? [],
            'top5' => $result['top5'] ?? [],
        ]);
    }

    private function clusterFromRows(array $rows, array $columns, int $k, int $maxIterations): array
    {
        // Build vectors
        $vectors = [];
        $points = [];
        foreach ($rows as $r) {
            $vec = [];
            foreach ($columns as $c) { $vec[] = (float)$r[$c]; }
            $vectors[] = $vec;
            $points[] = [$vec[0], $vec[1]];
        }
        if (count($vectors) < $k) {
            return ['available' => false, 'message' => 'Datos insuficientes para clustering'];
        }
        // Simple k-means inline
        $centroids = [];
        $n = count($vectors);
        $used = [];
        for ($i=0;$i<$k;$i++){ $idx = rand(0,$n-1); $attempts=0; while(isset($used[$idx]) && $attempts<10){ $idx=rand(0,$n-1); $attempts++; } $used[$idx]=true; $centroids[$i]=$vectors[$idx]; }
        $assign = [];
        for ($iter=0;$iter<$maxIterations;$iter++){
            $changed=false;
            foreach ($vectors as $i=>$v){
                $best=0;$bestDist=PHP_FLOAT_MAX;
                foreach($centroids as $cid=>$c){ $d=0.0; foreach($v as $j=>$val){ $diff=$val-$c[$j]; $d+= $diff*$diff; } if ($d<$bestDist){ $bestDist=$d; $best=$cid; } }
                if (!isset($assign[$i]) || $assign[$i]!==$best){ $assign[$i]=$best; $changed=true; }
            }
            $sums=array_fill(0,$k,[]); $counts=array_fill(0,$k,0);
            foreach($assign as $i=>$cid){ foreach($vectors[$i] as $dim=>$val){ if(!isset($sums[$cid][$dim])) $sums[$cid][$dim]=0.0; $sums[$cid][$dim]+=$val; } $counts[$cid]++; }
            foreach($centroids as $cid=>&$c){ if($counts[$cid]===0) continue; foreach($c as $dim=>$val){ $c[$dim]=$sums[$cid][$dim]/$counts[$cid]; } } unset($c);
            if(!$changed) break;
        }
        $counts = array_fill(0,$k,0); foreach($assign as $cid){ $counts[$cid]++; }
        $order = range(0,$k-1); usort($order,function($a,$b) use($counts){return $counts[$b] <=> $counts[$a];});
        $sortedCentroids=[]; $sortedCounts=[]; foreach($order as $cid){ $sortedCentroids[]=$centroids[$cid]; $sortedCounts[]=$counts[$cid]; }
        $top5 = array_slice(array_map(function($c,$n){ return ['centroid'=>$c,'count'=>$n]; }, $sortedCentroids, $sortedCounts), 0, 5);
        return [
            'available' => true,
            'columns' => $columns,
            'centroids' => $sortedCentroids,
            'counts' => $sortedCounts,
            'points' => $points,
            'top5' => $top5,
        ];
    }
}