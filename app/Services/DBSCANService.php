<?php

namespace App\Services;

class DBSCANService
{
    protected float $eps; // distance in degrees (~0.01 ~ 1.1km at equator)
    protected int $minPts;

    public function __construct(float $eps = 0.01, int $minPts = 20)
    {
        $this->eps = $eps;
        $this->minPts = $minPts;
    }

    /**
     * Run DBSCAN over lat/lon points.
     * @param array $points [[lat, lon], ...]
     * @return array {clusters: [[[lat,lon],...], ...], centroids: [[lat,lon],...], counts: [..]}
     */
    public function cluster(array $points): array
    {
        $n = count($points);
        if ($n === 0) return ['clusters' => [], 'centroids' => [], 'counts' => []];
        $labels = array_fill(0, $n, -1); // -1: unvisited, -2: noise, >=0 cluster id
        $cid = 0;
        for ($i = 0; $i < $n; $i++) {
            if ($labels[$i] !== -1) continue;
            $neighbors = $this->regionQuery($points, $i);
            if (count($neighbors) < $this->minPts) { $labels[$i] = -2; continue; }
            // expand cluster
            $labels[$i] = $cid;
            $seed = $neighbors;
            for ($k = 0; $k < count($seed); $k++) {
                $j = $seed[$k];
                if ($labels[$j] === -2) { $labels[$j] = $cid; }
                if ($labels[$j] !== -1) continue;
                $labels[$j] = $cid;
                $n2 = $this->regionQuery($points, $j);
                if (count($n2) >= $this->minPts) {
                    $seed = array_merge($seed, $n2);
                }
            }
            $cid++;
        }
        // build clusters
        $clusters = [];
        for ($i = 0; $i < $n; $i++) {
            if ($labels[$i] >= 0) { $clusters[$labels[$i]][] = $points[$i]; }
        }
        // compute centroids and counts
        $centroids = []; $counts = [];
        foreach ($clusters as $cpts) {
            $sumLat = 0.0; $sumLon = 0.0; $m = count($cpts);
            foreach ($cpts as $p) { $sumLat += $p[0]; $sumLon += $p[1]; }
            $centroids[] = [$m ? $sumLat/$m : 0.0, $m ? $sumLon/$m : 0.0];
            $counts[] = $m;
        }
        // sort by size
        $order = range(0, count($centroids)-1);
        usort($order, function($a,$b) use ($counts){ return ($counts[$b] ?? 0) <=> ($counts[$a] ?? 0); });
        $sortedCentroids=[]; $sortedCounts=[];
        foreach ($order as $o) { $sortedCentroids[] = $centroids[$o]; $sortedCounts[] = $counts[$o]; }
        $top5 = array_slice(array_map(function($c,$n){ return ['centroid'=>$c,'count'=>$n]; }, $sortedCentroids, $sortedCounts), 0, 5);
        return [
            'clusters' => $clusters,
            'centroids' => $sortedCentroids,
            'counts' => $sortedCounts,
            'top5' => $top5,
        ];
    }

    protected function regionQuery(array $points, int $idx): array
    {
        $neighbors = [];
        $p = $points[$idx];
        foreach ($points as $j => $q) {
            if ($this->distance2($p, $q) <= $this->eps*$this->eps) { $neighbors[] = $j; }
        }
        return $neighbors;
    }

    protected function distance2(array $a, array $b): float
    {
        $dl = $a[0] - $b[0];
        $dn = $a[1] - $b[1];
        return $dl*$dl + $dn*$dn;
    }
}
