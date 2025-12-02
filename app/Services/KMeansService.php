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

        // Cache incluyendo k para evitar mezclar resultados
        $cacheKey = 'kmeans_atus_2017_v1_k_' . $this->k;
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $rows = $this->readCsv($this->filePath, 5000); // limitar por memoria
            if (count($rows) < $this->k) {
                return [
                    'available' => false,
                    'message' => 'Datos insuficientes para clustering',
                ];
            }
            // Priorizar columnas geográficas si existen
            $numericColumns = $this->detectGeoColumns($rows);
            if (empty($numericColumns)) {
                return [
                    'available' => false,
                    'message' => 'No se detectaron columnas numéricas',
                ];
            }
            $vectors = $this->extractVectors($rows, $numericColumns);
            // extraer puntos lat/lon si corresponde
            $points = [];
            if (count($numericColumns) >= 2) {
                $latCol = $numericColumns[0];
                $lonCol = $numericColumns[1];
                foreach ($rows as $r) {
                    $lat = trim((string)$r[$latCol] ?? '');
                    $lon = trim((string)$r[$lonCol] ?? '');
                    if ($lat === '' || $lon === '') continue;
                    if (preg_match('/^\d+,\d+$/', $lat)) { $lat = str_replace(',', '.', $lat); }
                    if (preg_match('/^\d+,\d+$/', $lon)) { $lon = str_replace(',', '.', $lon); }
                    if (is_numeric($lat) && is_numeric($lon)) {
                        $points[] = [ (float)$lat, (float)$lon ];
                    }
                }
            }
            $result = $this->runKMeans($vectors);
            return [
                'available' => true,
                'columns' => $numericColumns,
                'centroids' => $result['centroids'],
                'counts' => $result['counts'],
                'total' => count($vectors),
                'points' => $points,
            ];
        });
    }

    protected function readCsv(string $path, int $limit): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) return [];
        // Detectar delimitador (coma o punto y coma)
        $firstLine = fgets($handle);
        if ($firstLine === false) { fclose($handle); return []; }
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        // Retroceder al inicio y leer con el delimitador detectado
        rewind($handle);
        $header = fgetcsv($handle, 0, $delimiter);
        $rows = [];
        $count = 0;
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($header && count($data) === count($header)) {
                // Normalizar decimales con coma a punto
                $normalized = [];
                foreach ($header as $i => $col) {
                    $val = $data[$i];
                    // Reemplazar coma decimal por punto si aplica
                    if (is_string($val) && preg_match('/^\d+,\d+$/', trim($val))) {
                        $val = str_replace(',', '.', $val);
                    }
                    $normalized[$col] = $val;
                }
                $rows[] = $normalized;
            }
            if (++$count >= $limit) break;
        }
        fclose($handle);
        return $rows;
    }

    protected function detectGeoColumns(array $rows): array
    {
        // Si existen columnas típicas de coordenadas, usarlas exclusivamente
        $candidates = [
            ['lat', 'lon'],
            ['latitude', 'longitude'],
            ['Latitud', 'Longitud'],
            ['LATITUD', 'LONGITUD'],
        ];
        $columns = array_keys($rows[0] ?? []);
        foreach ($candidates as $pair) {
            if (in_array($pair[0], $columns, true) && in_array($pair[1], $columns, true)) {
                // Validar que ambas sean numéricas en al menos algunas filas
                $valid = 0; $checks = 0;
                foreach ($rows as $r) {
                    $a = trim((string)$r[$pair[0]]);
                    $b = trim((string)$r[$pair[1]]);
                    if ($a !== '' && $b !== '') {
                        $a = preg_replace('/,/', '.', $a);
                        $b = preg_replace('/,/', '.', $b);
                        if (is_numeric($a) && is_numeric($b)) { $valid++; }
                    }
                    if (++$checks >= 100) break;
                }
                if ($valid > 10) {
                    return $pair; // usar sólo lat/lon
                }
            }
        }
        // Si no hay lat/lon, caer al detector genérico de numéricas
        return $this->detectNumericColumns($rows);
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
                $val = trim((string)$r[$c]);
                if (preg_match('/^\d+,\d+$/', $val)) { $val = str_replace(',', '.', $val); }
                $vec[] = (float)$val;
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
        $counts = $this->countAssignments($assignments, $k);
        // ordenar clusters por tamaño para destacar Top 5
        $order = range(0, $k-1);
        usort($order, function($a,$b) use ($counts){ return $counts[$b] <=> $counts[$a]; });
        $sortedCentroids = [];
        $sortedCounts = [];
        foreach ($order as $cid) { $sortedCentroids[] = $centroids[$cid]; $sortedCounts[] = $counts[$cid]; }
        return [
            'centroids' => $sortedCentroids,
            'counts' => $sortedCounts,
            'top5' => array_slice(array_map(function($c,$n){ return ['centroid'=>$c,'count'=>$n]; }, $sortedCentroids, $sortedCounts), 0, 5),
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
