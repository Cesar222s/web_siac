<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SupervisedKNNService
{
    protected string $filePath;
    protected int $limit;
    protected int $k;

    public function __construct(string $filePath, int $limit = 3000, int $k = 5)
    {
        $this->filePath = $filePath;
        $this->limit = $limit;
        $this->k = $k;
    }

    public function evaluate(): array
    {
        $cacheKey = 'supervised_home_v1';
        return Cache::remember($cacheKey, now()->addHours(6), function () {
            if (!file_exists($this->filePath)) {
                return ['available' => false, 'message' => 'CSV no encontrado en storage/app/data/atus_2017.csv'];
            }

            $fh = fopen($this->filePath, 'r');
            $header = fgetcsv($fh);
            if (!$header) {
                fclose($fh);
                return ['available' => false, 'message' => 'CSV vacío'];
            }

            $idx = array_flip($header);
            // Detect objective automatically: prefer injury; else tipo_accidente
            $labelCol = null;
            if (isset($idx['injury'])) $labelCol = 'injury';
            elseif (isset($idx['tipo_accidente'])) $labelCol = 'tipo_accidente';
            if (!$labelCol) {
                fclose($fh);
                return ['available' => false, 'message' => 'No se encontró columna objetivo (injury o tipo_accidente)'];
            }

            $featuresCols = [];
            foreach (['lat','lon','hora','dia'] as $c) {
                if (isset($idx[$c])) $featuresCols[] = $c;
            }
            if (isset($idx['tipo_accidente'])) $featuresCols[] = 'tipo_accidente'; // como categoría
            if (count($featuresCols) === 0) {
                fclose($fh);
                return ['available' => false, 'message' => 'No se encontraron columnas de features esperadas'];
            }

            $rows = [];
            $n = 0;
            while (($row = fgetcsv($fh)) !== false && $n < $this->limit) {
                $label = $row[$idx[$labelCol]] ?? null;
                if ($label === null || $label === '') continue;
                $feat = [];
                foreach ($featuresCols as $fc) {
                    $val = $row[$idx[$fc]] ?? null;
                    if ($val === null || $val === '') { $feat[] = null; continue; }
                    // encode categorical tipo_accidente as hash bucket
                    if ($fc === 'tipo_accidente' && !is_numeric($val)) {
                        $feat[] = (crc32($val) % 1000) / 1000.0;
                    } else {
                        $feat[] = is_numeric($val) ? floatval($val) : null;
                    }
                }
                if (in_array(null, $feat, true)) continue;
                $rows[] = ['x' => $feat, 'y' => $label];
                $n++;
            }
            fclose($fh);
            if (count($rows) < 50) {
                return ['available' => false, 'message' => 'Muy pocos registros válidos para evaluar'];
            }

            // Split train/test 80/20
            $split = (int)(count($rows) * 0.8);
            $train = array_slice($rows, 0, $split);
            $test = array_slice($rows, $split);

            // KNN predict
            $labels = [];
            foreach ($train as $t) { $labels[$t['y']] = true; }
            $labels = array_keys($labels);
            $labelIdx = array_flip($labels);
            $cm = array_fill(0, count($labels), array_fill(0, count($labels), 0));

            $correct = 0;
            foreach ($test as $t) {
                $pred = $this->knnPredict($t['x'], $train, $this->k);
                if ($pred === $t['y']) $correct++;
                $cm[$labelIdx[$t['y']]][$labelIdx[$pred]]++;
            }

            $accuracy = round($correct / max(1, count($test)) * 100, 2);
            return [
                'available' => true,
                'records' => count($rows),
                'accuracy' => $accuracy,
                'labels' => $labels,
                'cm' => $cm,
                'objective' => $labelCol,
            ];
        });
    }

    protected function knnPredict(array $x, array $train, int $k): string
    {
        $top = []; // [dist, label]
        foreach ($train as $t) {
            $d = 0.0;
            for ($i=0; $i<count($x); $i++) {
                $diff = $x[$i] - $t['x'][$i];
                $d += $diff * $diff;
            }
            $entry = [$d, $t['y']];
            if (count($top) < $k) { $top[] = $entry; usort($top, fn($a,$b)=>$a[0]<=>$b[0]); }
            else {
                if ($d < $top[$k-1][0]) { $top[$k-1] = $entry; usort($top, fn($a,$b)=>$a[0]<=>$b[0]); }
            }
        }
        $votes = [];
        foreach ($top as $e) { $votes[$e[1]] = ($votes[$e[1]] ?? 0) + 1; }
        arsort($votes);
        return array_key_first($votes);
    }
}
