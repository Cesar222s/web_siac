<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SupervisedModelService
{
    protected string $filePath;
    protected int $sampleLimit;
    protected int $k;

    public function __construct(string $filePath, int $sampleLimit = 10000, int $k = 5)
    {
        $this->filePath = $filePath;
        $this->sampleLimit = $sampleLimit;
        $this->k = $k;
    }

    public function evaluate(): array
    {
        if (!file_exists($this->filePath)) {
            return ['available' => false, 'message' => 'Archivo no encontrado para modelo supervisado.'];
        }
        return Cache::remember('supervised_accidentes_v1', now()->addMinutes(30), function () {
            [$features, $labels, $columns] = $this->loadData();
            if (count($features) < 100) {
                return ['available' => false, 'message' => 'Datos insuficientes para entrenar.'];
            }
            // train-test split 80/20
            $total = count($features);
            $split = (int)($total * 0.8);
            $trainX = array_slice($features, 0, $split);
            $trainY = array_slice($labels, 0, $split);
            $testX = array_slice($features, $split);
            $testY = array_slice($labels, $split);

            $predY = [];
            foreach ($testX as $vec) {
                $predY[] = $this->predictKNN($vec, $trainX, $trainY, $this->k);
            }

            $metrics = $this->metrics($testY, $predY);
            return [
                'available' => true,
                'target_description' => 'Heridos en el accidente (1 = al menos una persona herida, 0 = sin heridos).',
                'features_used' => $columns,
                'sample_size' => $total,
                'train_size' => $split,
                'test_size' => $total - $split,
                'k' => $this->k,
                'accuracy' => $metrics['accuracy'],
                'confusion' => $metrics['confusion'],
            ];
        });
    }

    protected function loadData(): array
    {
        $handle = fopen($this->filePath, 'r');
        if (!$handle) return [[], [], []];
        $header = fgetcsv($handle);
        if (!$header) return [[], [], []];
        // outcome injury columns
        $injuryCols = ['CONDHERIDO','PASAHERIDO','PEATHERIDO','CICLHERIDO','OTROHERIDO','NEHERIDO'];
        $exclude = array_merge($injuryCols, [
            // exclude direct death columns & label creation columns
            'CONDMUERTO','PASAMUERTO','PEATMUERTO','CICLMUERTO','OTROMUERTO','NEMUERTO'
        ]);
        // Features: time + context + vehicle presence + sexo + edad
        $candidateFeatures = [
            'HORA','DIASEMANA','URBANA','SUBURBANA','TIPACCID','AUTOMOVIL','CAMPASAJ','MICROBUS','CAMIONETA','CAMION','MOTOCICLET','BICICLETA','SEXO','EDAD'
        ];
        $features = [];
        $labels = [];
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) continue;
            $assoc = array_combine($header, $row);
            // build label: any injury column > 0
            $injured = 0;
            foreach ($injuryCols as $c) {
                if (isset($assoc[$c]) && (int)$assoc[$c] > 0) { $injured = 1; break; }
            }
            $vec = [];
            foreach ($candidateFeatures as $c) {
                $val = isset($assoc[$c]) && is_numeric($assoc[$c]) ? (float)$assoc[$c] : 0.0;
                $vec[] = $val;
            }
            $features[] = $vec;
            $labels[] = $injured;
            if (++$count >= $this->sampleLimit) break;
        }
        fclose($handle);
        return [$features, $labels, $candidateFeatures];
    }

    protected function predictKNN(array $vec, array $trainX, array $trainY, int $k): int
    {
        $distances = [];
        foreach ($trainX as $i => $t) {
            $dist = 0.0;
            foreach ($vec as $d => $v) {
                $dv = $v - $t[$d];
                $dist += $dv * $dv;
            }
            $distances[] = [$dist, $trainY[$i]];
        }
        // sort by distance
        usort($distances, function ($a,$b) { return $a[0] <=> $b[0]; });
        $votes = [0 => 0, 1 => 0];
        for ($i=0; $i < min($k, count($distances)); $i++) {
            $votes[$distances[$i][1]]++;
        }
        return $votes[1] >= $votes[0] ? 1 : 0;
    }

    protected function metrics(array $true, array $pred): array
    {
        $tp=$tn=$fp=$fn=0; $n = count($true);
        for ($i=0;$i<$n;$i++) {
            $t = $true[$i]; $p = $pred[$i];
            if ($t===1 && $p===1) $tp++; elseif ($t===0 && $p===0) $tn++; elseif ($t===0 && $p===1) $fp++; elseif ($t===1 && $p===0) $fn++;
        }
        $accuracy = $n>0 ? ($tp+$tn)/$n : 0.0;
        return [
            'accuracy' => round($accuracy,4),
            'confusion' => [
                'TP' => $tp,
                'TN' => $tn,
                'FP' => $fp,
                'FN' => $fn,
            ]
        ];
    }
}
