<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Survey;
use App\Services\MachineLearningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected $mlService;

    public function __construct(MachineLearningService $mlService)
    {
        $this->mlService = $mlService;
    }
    public function dashboard()
    {
        // Análisis basado en encuestas reales
        $totalUsers = User::count();
        $totalSurveys = Survey::count();
        
        // Métricas principales de encuestas
        $metrics = $this->getSurveyMetrics();
        
        // Datos para gráficas
        $chartData = $this->getSurveyChartData();
        
        // Análisis de segmentación
        $segmentation = $this->segmentUsers(Survey::all());
        
        // Recomendaciones
        $recommendations = $this->generateRecommendations([
            'avgSatisfaction' => $metrics['avgSatisfaction'],
            'avgStress' => $metrics['avgStress'],
            'segments' => $segmentation,
        ]);
        
        return view('admin.dashboard', compact('totalUsers', 'totalSurveys', 'metrics', 'chartData', 'segmentation', 'recommendations'));
    }
    
    private function getSurveyMetrics()
    {
        $surveys = Survey::all();
        
        if ($surveys->count() === 0) {
            return [
                'avgSatisfaction' => 0,
                'avgStress' => 0,
                'avgSpeed' => 0,
                'totalIncidents' => 0,
                'avgExperience' => 0,
                'avgDistance' => 0,
            ];
        }
        
        return [
            'avgSatisfaction' => round($surveys->avg('satisfaction_score'), 1),
            'avgStress' => round($surveys->avg('stress_level'), 1),
            'avgSpeed' => round($surveys->avg('avg_speed'), 1),
            'totalIncidents' => $surveys->sum('incidents_count'),
            'avgExperience' => round($surveys->avg('driving_experience'), 1),
            'avgDistance' => round($surveys->avg('daily_distance'), 1),
        ];
    }
    
    private function getSurveyChartData()
    {
        $surveys = Survey::all();
        
        // Datos por edad
        $ageGroups = ['18-25', '26-35', '36-45', '46-55', '56+'];
        $satisfactionByAge = [];
        $stressByAge = [];
        
        foreach ($ageGroups as $group) {
            list($min, $max) = $this->parseAgeGroup($group);
            $groupSurveys = $surveys->filter(function($s) use ($min, $max) {
                return $s->age >= $min && $s->age <= $max;
            });
            
            $satisfactionByAge[] = $groupSurveys->count() > 0 ? round($groupSurveys->avg('satisfaction_score'), 1) : 0;
            $stressByAge[] = $groupSurveys->count() > 0 ? round($groupSurveys->avg('stress_level'), 1) : 0;
        }
        
        // Incidentes por velocidad
        $speedRanges = ['60-80', '81-100', '101-120', '121+'];
        $incidentsBySpeed = [];
        
        foreach ($speedRanges as $range) {
            if ($range === '121+') {
                $rangeSurveys = $surveys->filter(fn($s) => $s->avg_speed >= 121);
            } else {
                list($min, $max) = explode('-', $range);
                $rangeSurveys = $surveys->filter(fn($s) => $s->avg_speed >= $min && $s->avg_speed <= $max);
            }
            $incidentsBySpeed[] = $rangeSurveys->sum('incidents_count');
        }
        
        return [
            'ageGroups' => $ageGroups,
            'satisfactionByAge' => $satisfactionByAge,
            'stressByAge' => $stressByAge,
            'speedRanges' => $speedRanges,
            'incidentsBySpeed' => $incidentsBySpeed,
        ];
    }
    
    private function parseAgeGroup($group)
    {
        if ($group === '56+') return [56, 100];
        list($min, $max) = explode('-', $group);
        return [(int)$min, (int)$max];
    }

    public function analytics()
    {
        $surveys = Survey::all();
        
        // K-Means Clustering
        $kmeansResults = $this->mlService->kMeansClustering($surveys, 3);
        
        // Feature Importance
        $featureImportance = $this->mlService->featureImportance($surveys);
        
        // k-NN: Predecir riesgo de un conductor ejemplo
        $exampleDriver = [
            'speed' => 95,
            'incidents' => 6,
            'stress' => 6,
            'experience' => 5,
        ];
        $knnPrediction = $this->mlService->kNearestNeighbors($surveys, $exampleDriver, 5);
        
        // Análisis tradicional para gráficas
        $monthlyData = $this->generateMonthlyData();
        $riskAnalysis = $this->performRiskAnalysis();
        $surveyAnalysis = $this->analyzeSurveys();
        
        return view('admin.analytics', compact(
            'monthlyData', 
            'riskAnalysis', 
            'surveyAnalysis',
            'kmeansResults',
            'featureImportance',
            'knnPrediction'
        ));
    }
    
    private function analyzeSurveys()
    {
        $surveys = Survey::all();
        
        if ($surveys->isEmpty()) {
            return [
                'total' => 0,
                'avgSatisfaction' => 0,
                'avgStress' => 0,
                'correlations' => [],
                'segments' => [],
                'recommendations' => ['No hay datos de encuestas disponibles'],
            ];
        }
        
        // Análisis estadístico básico
        $total = $surveys->count();
        $avgSatisfaction = round($surveys->avg('satisfaction_score'), 2);
        $avgStress = round($surveys->avg('stress_level'), 2);
        $avgSpeed = round($surveys->avg('avg_speed'), 2);
        $avgIncidents = round($surveys->avg('incidents_count'), 2);
        
        // Análisis de correlación (Velocidad vs Incidentes)
        $speedIncidentCorr = $this->calculateCorrelation(
            $surveys->pluck('avg_speed')->toArray(),
            $surveys->pluck('incidents_count')->toArray()
        );
        
        // Análisis de correlación (Estrés vs Satisfacción)
        $stressSatisfactionCorr = $this->calculateCorrelation(
            $surveys->pluck('stress_level')->toArray(),
            $surveys->pluck('satisfaction_score')->toArray()
        );
        
        // Segmentación de usuarios (K-Means simplificado)
        $segments = $this->segmentUsers($surveys);
        
        // Análisis por tipo de vehículo
        $vehicleStats = $surveys->groupBy('vehicle_type')->map(function($group) {
            return [
                'count' => $group->count(),
                'avgSatisfaction' => round($group->avg('satisfaction_score'), 2),
                'avgIncidents' => round($group->avg('incidents_count'), 2),
            ];
        });
        
        // Preferencias de alertas
        $alertPreferences = $surveys->groupBy('alert_preference')->map(function($group) {
            return $group->count();
        });
        
        // Características más valoradas
        $topFeatures = $surveys->groupBy('most_useful_feature')->map(function($group) {
            return $group->count();
        })->sortDesc()->take(5);
        
        // Recomendaciones basadas en análisis
        $recommendations = $this->generateRecommendations([
            'avgSatisfaction' => $avgSatisfaction,
            'avgStress' => $avgStress,
            'speedIncidentCorr' => $speedIncidentCorr,
            'segments' => $segments,
        ]);
        
        return [
            'total' => $total,
            'avgSatisfaction' => $avgSatisfaction,
            'avgStress' => $avgStress,
            'avgSpeed' => $avgSpeed,
            'avgIncidents' => $avgIncidents,
            'correlations' => [
                'speedIncident' => round($speedIncidentCorr, 3),
                'stressSatisfaction' => round($stressSatisfactionCorr, 3),
            ],
            'segments' => $segments,
            'vehicleStats' => $vehicleStats,
            'alertPreferences' => $alertPreferences,
            'topFeatures' => $topFeatures,
            'recommendations' => $recommendations,
            'ageDistribution' => $this->getAgeDistribution($surveys),
            'satisfactionByAge' => $this->getSatisfactionByAge($surveys),
        ];
    }
    
    private function calculateCorrelation($x, $y)
    {
        $n = count($x);
        if ($n === 0) return 0;
        
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
    
    private function segmentUsers($surveys)
    {
        if ($surveys->isEmpty()) {
            return [
                'safe' => 0,
                'moderate' => 0,
                'risky' => 0,
            ];
        }
        
        // Segmentación simple basada en comportamiento de conducción
        $safe = $surveys->filter(function($s) {
            return $s->avg_speed < 80 && $s->incidents_count < 5;
        })->count();
        
        $moderate = $surveys->filter(function($s) {
            return $s->avg_speed >= 80 && $s->avg_speed < 100 && $s->incidents_count >= 5 && $s->incidents_count < 10;
        })->count();
        
        $risky = $surveys->filter(function($s) {
            return $s->avg_speed >= 100 || $s->incidents_count >= 10;
        })->count();
        
        return [
            'safe' => $safe,
            'moderate' => $moderate,
            'risky' => $risky,
        ];
    }
    
    private function generateRecommendations($data)
    {
        $recs = [];
        
        if ($data['avgSatisfaction'] < 7) {
            $recs[] = [
                'title' => 'Mejorar Experiencia de Usuario',
                'description' => 'La satisfacción promedio está por debajo del objetivo (7/10). Se recomienda mejorar las características más valoradas por los usuarios.',
            ];
        }
        
        if ($data['avgStress'] > 6) {
            $recs[] = [
                'title' => 'Reducir Niveles de Estrés',
                'description' => 'Los conductores reportan niveles altos de estrés. Implementar funciones de asistencia y reducción de estrés durante la conducción.',
            ];
        }
        
        if (isset($data['segments']['risky']) && isset($data['segments']['safe'])) {
            if ($data['segments']['risky'] > $data['segments']['safe']) {
                $recs[] = [
                    'title' => 'Capacitación para Conductores de Riesgo',
                    'description' => 'Alto porcentaje de conductores en segmento de riesgo. Implementar programas de capacitación y alertas personalizadas.',
                ];
            }
        }
        
        if (empty($recs)) {
            $recs[] = [
                'title' => 'Sistema Operando Correctamente',
                'description' => 'Todos los parámetros dentro de rangos normales. Continuar con el monitoreo regular.',
            ];
        }
        
        return $recs;
    }
    
    private function getAgeDistribution($surveys)
    {
        return [
            '18-25' => $surveys->whereBetween('age', [18, 25])->count(),
            '26-35' => $surveys->whereBetween('age', [26, 35])->count(),
            '36-50' => $surveys->whereBetween('age', [36, 50])->count(),
            '51+' => $surveys->where('age', '>', 50)->count(),
        ];
    }
    
    private function getSatisfactionByAge($surveys)
    {
        return [
            '18-25' => round($surveys->whereBetween('age', [18, 25])->avg('satisfaction_score'), 2),
            '26-35' => round($surveys->whereBetween('age', [26, 35])->avg('satisfaction_score'), 2),
            '36-50' => round($surveys->whereBetween('age', [36, 50])->avg('satisfaction_score'), 2),
            '51+' => round($surveys->where('age', '>', 50)->avg('satisfaction_score'), 2),
        ];
    }

    private function generateMonthlyData()
    {
        // Simular distribución mensual basada en encuestas
        $surveys = Survey::all();
        $totalSurveys = $surveys->count();
        $avgIncidents = $surveys->avg('incidents_count');
        
        $data = [];
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        foreach ($months as $month) {
            $data[] = [
                'month' => $month,
                'trips' => round($totalSurveys * (rand(80, 120) / 100)), // Variación mensual
                'incidents' => round($avgIncidents * (rand(80, 120) / 100)),
                'efficiency' => round($surveys->avg('satisfaction_score') * 10), // Convertir a %
            ];
        }
        
        return $data;
    }

    private function performRiskAnalysis()
    {
        $surveys = Survey::all();
        
        if ($surveys->isEmpty()) {
            return [
                'level' => 'Sin datos',
                'highRisk' => 0,
                'mediumRisk' => 0,
                'lowRisk' => 0,
                'factors' => [0, 0, 0, 0, 0],
                'recommendations' => ['No hay datos suficientes para análisis de riesgo'],
            ];
        }
        
        // Segmentar por nivel de riesgo basado en incidentes y estrés
        $highRisk = $surveys->filter(fn($s) => $s->incidents_count > 8 || $s->stress_level > 7)->count();
        $mediumRisk = $surveys->filter(fn($s) => ($s->incidents_count >= 4 && $s->incidents_count <= 8) || ($s->stress_level >= 5 && $s->stress_level <= 7))->count();
        $lowRisk = $surveys->filter(fn($s) => $s->incidents_count < 4 && $s->stress_level < 5)->count();
        
        $total = $surveys->count();
        $highPercent = $total > 0 ? ($highRisk / $total) * 100 : 0;
        
        if ($highPercent > 15) {
            $level = 'Alto';
        } elseif ($highPercent > 8) {
            $level = 'Medio';
        } else {
            $level = 'Bajo';
        }
        
        // Factores de riesgo basados en datos reales
        $avgSpeed = $surveys->avg('avg_speed');
        $avgStress = $surveys->avg('stress_level');
        $avgIncidents = $surveys->avg('incidents_count');
        $avgSatisfaction = $surveys->avg('satisfaction_score');
        
        return [
            'level' => $level,
            'highRisk' => $highRisk,
            'mediumRisk' => $mediumRisk,
            'lowRisk' => $lowRisk,
            'factors' => [
                round(($avgSpeed / 130) * 100),  // Velocidad normalizada
                round(($avgStress / 10) * 100),  // Estrés normalizado
                round(($avgIncidents / 15) * 100),  // Incidentes normalizados
                round($avgSatisfaction * 10),  // Satisfacción como %
                round(($surveys->avg('alerts_frequency') / 50) * 100),  // Alertas normalizadas
            ],
            'recommendations' => $this->getRiskRecommendations($level, $surveys),
        ];
    }
    
    private function getRiskRecommendations($level, $surveys)
    {
        $recs = [];
        
        if ($level === 'Alto') {
            $recs[] = 'Implementar programa de capacitación urgente para conductores de alto riesgo';
            $recs[] = 'Revisar y reforzar sistema de alertas de seguridad';
        }
        
        if ($surveys->avg('avg_speed') > 100) {
            $recs[] = 'Configurar alertas más agresivas para control de velocidad';
        }
        
        if ($surveys->avg('stress_level') > 6) {
            $recs[] = 'Implementar funciones de asistencia para reducción de estrés';
        }
        
        if ($surveys->avg('incidents_count') > 5) {
            $recs[] = 'Analizar patrones de incidentes y mejorar prevención';
        }
        
        if (empty($recs)) {
            $recs[] = 'Sistema operando dentro de parámetros seguros';
        }
        
        return $recs;
    }
}
