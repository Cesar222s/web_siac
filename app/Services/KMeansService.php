<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class KMeansService
{
    protected int $k;
    protected int $maxIterations;
    protected string $filePath;

    public function __construct(string $filePath, int $k = 4, int $maxIterations = 25)
    {
        $this->filePath = $filePath;
        $this->k = $k;
        $this->maxIterations = $maxIterations;
    }

    public function cluster(): array
    {
        if (!file_exists($this->filePath)) {
            return [
                'available' => false,
                'message' => 'Archivo CSV no encontrado. Mueva atus_2017.csv a storage/app/data/atus_2017.csv',
            ];
        }

        // Cache original sin incluir k (simplificado)
        $cacheKey = 'kmeans_atus_2017_v1';
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $rows = $this->readCsv($this->filePath, 5000); // limitar por memoria
            if (count($rows) < $this->k) {
                return [
                    'available' => false,
                    'message' => 'Datos insuficientes para clustering',
                ];
            }
            $numericColumns = $this->detectNumericColumns($rows);
            if (empty($numericColumns)) {
                return [
                    'available' => false,
                    'message' => 'No se detectaron columnas numéricas',
                ];
            }
            $vectors = $this->extractVectors($rows, $numericColumns);
            $result = $this->runKMeans($vectors);
            return [
                'available' => true,
                'columns' => $numericColumns,
                'centroids' => $result['centroids'],
                'counts' => $result['counts'],
                'total' => count($vectors),
            ];
        });
    }

    protected function readCsv(string $path, int $limit): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) return [];
        $header = fgetcsv($handle);
        $rows = [];
        $count = 0;
        while (($data = fgetcsv($handle)) !== false) {
            if ($header && count($data) === count($header)) {
                $rows[] = array_combine($header, $data);
            }
            if (++$count >= $limit) break;
        }
        fclose($handle);
        return $rows;
    }

    protected function detectNumericColumns(array $rows): array
    {
        if (empty($rows)) return [];
        $first = $rows[0];
        $numeric = [];
        foreach ($first as $col => $val) {
            $isNumeric = true;
            $checks = 0;
            foreach ($rows as $r) {
                $v = trim($r[$col]);
                if ($v === '' || !is_numeric($v)) { $isNumeric = false; break; }
                if (++$checks >= 50) break; // muestras suficientes
            }
            if ($isNumeric) $numeric[] = $col;
        }
        // limitar a máximo 6 para rendimiento
        return array_slice($numeric, 0, 6);
    }

    protected function extractVectors(array $rows, array $numericColumns): array
    {
        $vectors = [];
        foreach ($rows as $r) {
            $vec = [];
            foreach ($numericColumns as $c) {
                $vec[] = (float)$r[$c];
            }
            $vectors[] = $vec;
        }
        return $vectors;
    }

    protected function runKMeans(array $vectors): array
    {
        $k = $this->k;
        $centroids = $this->initializeCentroids($vectors, $k);
        $assignments = [];
        for ($iter = 0; $iter < $this->maxIterations; $iter++) {
            $changed = false;
            // asignar
            foreach ($vectors as $i => $vec) {
                $closest = $this->closestCentroid($vec, $centroids);
                if (!isset($assignments[$i]) || $assignments[$i] !== $closest) {
                    $assignments[$i] = $closest;
                    $changed = true;
                }
            }
            // recalcular
            $sums = array_fill(0, $k, []);
            $counts = array_fill(0, $k, 0);
            foreach ($assignments as $i => $cid) {
                foreach ($vectors[$i] as $dim => $val) {
                    if (!isset($sums[$cid][$dim])) $sums[$cid][$dim] = 0.0;
                    $sums[$cid][$dim] += $val;
                }
                $counts[$cid]++;
            }
            foreach ($centroids as $cid => &$cent) {
                if ($counts[$cid] === 0) continue; // evitar división por cero
                foreach ($cent as $dim => $oldVal) {
                    $cent[$dim] = $sums[$cid][$dim] / $counts[$cid];
                }
            }
            unset($cent);
            if (!$changed) break;
        }
        return [
            'centroids' => $centroids,
            'counts' => $this->countAssignments($assignments, $k),
        ];
    }

    protected function initializeCentroids(array $vectors, int $k): array
    {
        $centroids = [];
        $used = [];
        $n = count($vectors);
        for ($i = 0; $i < $k; $i++) {
            $idx = rand(0, $n - 1);
            // evitar duplicados básicos
            $attempts = 0;
            while (isset($used[$idx]) && $attempts < 10) { $idx = rand(0, $n - 1); $attempts++; }
            $used[$idx] = true;
            $centroids[$i] = $vectors[$idx];
        }
        return $centroids;
    }

    protected function closestCentroid(array $vec, array $centroids): int
    {
        $best = 0; $bestDist = PHP_FLOAT_MAX;
        foreach ($centroids as $cid => $c) {
            $dist = 0.0;
            foreach ($vec as $i => $v) {
                $d = $v - $c[$i];
                $dist += $d * $d;
            }
            if ($dist < $bestDist) { $bestDist = $dist; $best = $cid; }
        }
        return $best;
    }

    protected function countAssignments(array $assignments, int $k): array
    {
        $counts = array_fill(0, $k, 0);
        foreach ($assignments as $cid) { $counts[$cid]++; }
        return $counts;
    }

}
