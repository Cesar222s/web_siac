<?php

namespace App\Services;

class MachineLearningService
{
    /**
     * K-Means Clustering
     * Agrupa conductores en 3 clusters según comportamiento de conducción
     */
    public function kMeansClustering($surveys, $k = 3, $maxIterations = 100)
    {
        if ($surveys->isEmpty()) {
            return ['clusters' => [], 'centroids' => []];
        }

        // Normalizar datos para K-Means
        $data = $surveys->map(function($survey) {
            return [
                'id' => $survey->_id,
                'speed' => $survey->avg_speed,
                'incidents' => $survey->incidents_count,
                'stress' => $survey->stress_level,
                'satisfaction' => $survey->satisfaction_score,
                'experience' => $survey->driving_experience,
            ];
        })->toArray();

        // Normalizar features (0-1)
        $normalized = $this->normalizeData($data);

        // Inicializar centroides aleatoriamente
        $centroids = $this->initializeCentroids($normalized, $k);

        // Iterar hasta convergencia
        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            // Asignar cada punto al centroide más cercano
            $clusters = $this->assignToClusters($normalized, $centroids);

            // Calcular nuevos centroides
            $newCentroids = $this->calculateCentroids($normalized, $clusters, $k);

            // Verificar convergencia
            if ($this->hasConverged($centroids, $newCentroids)) {
                break;
            }

            $centroids = $newCentroids;
        }

        // Interpretar clusters
        $interpretation = $this->interpretClusters($centroids, $clusters, $data);

        return [
            'clusters' => $clusters,
            'centroids' => $centroids,
            'interpretation' => $interpretation,
            'iterations' => $iteration + 1,
        ];
    }

    /**
     * K-Nearest Neighbors (k-NN)
     * Predice el nivel de riesgo de un conductor basado en sus vecinos más cercanos
     */
    public function kNearestNeighbors($surveys, $newDriver, $k = 5)
    {
        if ($surveys->isEmpty()) {
            return ['prediction' => 'unknown', 'neighbors' => []];
        }

        // Preparar datos de entrenamiento
        $trainingData = $surveys->map(function($survey) {
            return [
                'features' => [
                    'speed' => $survey->avg_speed,
                    'incidents' => $survey->incidents_count,
                    'stress' => $survey->stress_level,
                    'experience' => $survey->driving_experience,
                ],
                'label' => $this->getRiskLabel($survey),
            ];
        })->toArray();

        // Normalizar features
        $normalized = $this->normalizeFeatures($trainingData);
        $normalizedNew = $this->normalizeNewDriver($newDriver, $trainingData);

        // Calcular distancias euclidiana
        $distances = [];
        foreach ($normalized as $idx => $train) {
            $distance = $this->euclideanDistance($normalizedNew, $train['features']);
            $distances[] = [
                'index' => $idx,
                'distance' => $distance,
                'label' => $train['label'],
            ];
        }

        // Ordenar por distancia y tomar k vecinos más cercanos
        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $neighbors = array_slice($distances, 0, $k);

        // Votación mayoritaria
        $votes = array_count_values(array_column($neighbors, 'label'));
        arsort($votes);
        $prediction = array_key_first($votes);

        // Calcular confianza
        $confidence = ($votes[$prediction] / $k) * 100;

        return [
            'prediction' => $prediction,
            'confidence' => round($confidence, 2),
            'neighbors' => $neighbors,
            'votes' => $votes,
        ];
    }

    /**
     * Análisis de características importantes (Feature Importance)
     * Similar a Random Forest feature importance
     */
    public function featureImportance($surveys)
    {
        if ($surveys->isEmpty()) {
            return [];
        }

        $correlations = [];

        // Calcular correlación de cada feature con satisfacción
        $satisfaction = $surveys->pluck('satisfaction_score')->toArray();

        $features = [
            'Velocidad Promedio' => $surveys->pluck('avg_speed')->toArray(),
            'Incidentes' => $surveys->pluck('incidents_count')->toArray(),
            'Nivel de Estrés' => $surveys->pluck('stress_level')->toArray(),
            'Experiencia' => $surveys->pluck('driving_experience')->toArray(),
            'Distancia Diaria' => $surveys->pluck('daily_distance')->toArray(),
            'Frecuencia Alertas' => $surveys->pluck('alerts_frequency')->toArray(),
        ];

        foreach ($features as $name => $values) {
            $corr = $this->pearsonCorrelation($values, $satisfaction);
            $correlations[$name] = [
                'correlation' => round($corr, 4),
                'importance' => round(abs($corr) * 100, 2),
            ];
        }

        // Ordenar por importancia
        uasort($correlations, fn($a, $b) => $b['importance'] <=> $a['importance']);

        return $correlations;
    }

    // ============= Métodos Auxiliares =============

    private function normalizeData($data)
    {
        $features = ['speed', 'incidents', 'stress', 'satisfaction', 'experience'];
        $mins = $maxs = [];

        foreach ($features as $feature) {
            $values = array_column($data, $feature);
            $mins[$feature] = min($values);
            $maxs[$feature] = max($values);
        }

        return array_map(function($row) use ($features, $mins, $maxs) {
            $normalized = ['id' => $row['id']];
            foreach ($features as $feature) {
                $range = $maxs[$feature] - $mins[$feature];
                $normalized[$feature] = $range > 0 
                    ? ($row[$feature] - $mins[$feature]) / $range 
                    : 0;
            }
            return $normalized;
        }, $data);
    }

    private function initializeCentroids($data, $k)
    {
        $shuffled = $data;
        shuffle($shuffled);
        return array_slice($shuffled, 0, $k);
    }

    private function assignToClusters($data, $centroids)
    {
        $clusters = [];
        foreach ($data as $point) {
            $minDist = PHP_FLOAT_MAX;
            $clusterIdx = 0;

            foreach ($centroids as $idx => $centroid) {
                $dist = $this->euclideanDistance($point, $centroid);
                if ($dist < $minDist) {
                    $minDist = $dist;
                    $clusterIdx = $idx;
                }
            }

            $clusters[$point['id']] = $clusterIdx;
        }
        return $clusters;
    }

    private function calculateCentroids($data, $clusters, $k)
    {
        $newCentroids = [];
        $features = ['speed', 'incidents', 'stress', 'satisfaction', 'experience'];

        for ($i = 0; $i < $k; $i++) {
            $clusterPoints = array_filter($data, fn($point) => $clusters[$point['id']] === $i);
            
            if (empty($clusterPoints)) {
                $newCentroids[$i] = $this->initializeCentroids($data, 1)[0];
                continue;
            }

            $centroid = ['id' => "centroid_$i"];
            foreach ($features as $feature) {
                $values = array_column($clusterPoints, $feature);
                $centroid[$feature] = array_sum($values) / count($values);
            }
            $newCentroids[$i] = $centroid;
        }

        return $newCentroids;
    }

    private function euclideanDistance($p1, $p2)
    {
        $features = ['speed', 'incidents', 'stress', 'satisfaction', 'experience'];
        $sum = 0;

        foreach ($features as $feature) {
            $diff = ($p1[$feature] ?? 0) - ($p2[$feature] ?? 0);
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    private function hasConverged($old, $new, $threshold = 0.0001)
    {
        foreach ($old as $idx => $centroid) {
            $dist = $this->euclideanDistance($centroid, $new[$idx]);
            if ($dist > $threshold) {
                return false;
            }
        }
        return true;
    }

    private function interpretClusters($centroids, $clusters, $originalData)
    {
        $interpretation = [];

        foreach ($centroids as $idx => $centroid) {
            $clusterData = array_filter($originalData, fn($point) => $clusters[$point['id']] === $idx);
            
            $avgSpeed = array_sum(array_column($clusterData, 'speed')) / count($clusterData);
            $avgIncidents = array_sum(array_column($clusterData, 'incidents')) / count($clusterData);
            $avgStress = array_sum(array_column($clusterData, 'stress')) / count($clusterData);
            $avgSatisfaction = array_sum(array_column($clusterData, 'satisfaction')) / count($clusterData);

            // Clasificar cluster
            if ($avgSpeed < 85 && $avgIncidents < 5 && $avgStress < 5) {
                $label = 'Conductor Seguro';
                $color = '#4caf50';
            } elseif ($avgSpeed > 100 || $avgIncidents > 8 || $avgStress > 7) {
                $label = 'Conductor de Alto Riesgo';
                $color = '#ff5faa';
            } else {
                $label = 'Conductor Moderado';
                $color = '#ffb74d';
            }

            $interpretation[$idx] = [
                'label' => $label,
                'color' => $color,
                'count' => count($clusterData),
                'avgSpeed' => round($avgSpeed, 1),
                'avgIncidents' => round($avgIncidents, 1),
                'avgStress' => round($avgStress, 1),
                'avgSatisfaction' => round($avgSatisfaction, 1),
            ];
        }

        return $interpretation;
    }

    private function getRiskLabel($survey)
    {
        if ($survey->avg_speed > 100 || $survey->incidents_count > 8 || $survey->stress_level > 7) {
            return 'Alto Riesgo';
        } elseif ($survey->avg_speed < 85 && $survey->incidents_count < 5 && $survey->stress_level < 5) {
            return 'Bajo Riesgo';
        }
        return 'Riesgo Moderado';
    }

    private function normalizeFeatures($trainingData)
    {
        $features = ['speed', 'incidents', 'stress', 'experience'];
        $mins = $maxs = [];

        foreach ($features as $feature) {
            $values = array_column(array_column($trainingData, 'features'), $feature);
            $mins[$feature] = min($values);
            $maxs[$feature] = max($values);
        }

        return array_map(function($item) use ($features, $mins, $maxs) {
            $normalized = ['label' => $item['label'], 'features' => []];
            foreach ($features as $feature) {
                $range = $maxs[$feature] - $mins[$feature];
                $normalized['features'][$feature] = $range > 0 
                    ? ($item['features'][$feature] - $mins[$feature]) / $range 
                    : 0;
            }
            return $normalized;
        }, $trainingData);
    }

    private function normalizeNewDriver($driver, $trainingData)
    {
        $features = ['speed', 'incidents', 'stress', 'experience'];
        $mins = $maxs = [];

        foreach ($features as $feature) {
            $values = array_column(array_column($trainingData, 'features'), $feature);
            $mins[$feature] = min($values);
            $maxs[$feature] = max($values);
        }

        $normalized = [];
        foreach ($features as $feature) {
            $range = $maxs[$feature] - $mins[$feature];
            $normalized[$feature] = $range > 0 
                ? ($driver[$feature] - $mins[$feature]) / $range 
                : 0;
        }

        return $normalized;
    }

    private function pearsonCorrelation($x, $y)
    {
        $n = count($x);
        if ($n === 0 || $n !== count($y)) return 0;

        $meanX = array_sum($x) / $n;
        $meanY = array_sum($y) / $n;

        $numerator = 0;
        $sumSqX = 0;
        $sumSqY = 0;

        for ($i = 0; $i < $n; $i++) {
            $diffX = $x[$i] - $meanX;
            $diffY = $y[$i] - $meanY;
            $numerator += $diffX * $diffY;
            $sumSqX += $diffX * $diffX;
            $sumSqY += $diffY * $diffY;
        }

        $denominator = sqrt($sumSqX * $sumSqY);
        return $denominator == 0 ? 0 : $numerator / $denominator;
    }
}
