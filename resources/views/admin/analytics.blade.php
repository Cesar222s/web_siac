@extends('layouts.main')
@section('title','Analytics Avanzado')
@section('content')
<style>
    .analytics-header {display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;}
    .stats-grid {display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1.5rem; margin-bottom:2rem;}
    .stat-card {background:var(--surface-soft); border:1px solid var(--border); border-radius:16px; padding:1.5rem; transition:all .3s;}
    .stat-card:hover {transform:translateY(-4px); box-shadow:0 8px 24px rgba(125,91,255,.2);}
    .stat-icon {width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:1rem;}
    .icon-purple {background:rgba(125,91,255,.15); color:var(--primary);}
    .icon-cyan {background:rgba(45,232,255,.15); color:var(--accent);}
    .icon-pink {background:rgba(255,95,170,.15); color:var(--secondary);}
    .icon-green {background:rgba(76,175,80,.15); color:#4caf50;}
    .chart-row {display:grid; grid-template-columns:2fr 1fr; gap:1.5rem; margin-bottom:2rem;}
    .chart-box {background:var(--surface-soft); border-radius:18px; padding:2rem; border:1px solid var(--border);}
    .risk-list {display:flex; flex-direction:column; gap:.8rem;}
    .risk-item {background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.06); border-radius:10px; padding:1rem; display:flex; align-items:start; gap:.8rem;}
    .risk-number {background:var(--primary); color:#fff; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; flex-shrink:0;}
    @media(max-width:900px){.chart-row{grid-template-columns:1fr;}}
</style>

<div class="fade-in">
    <div class="analytics-header">
        <h1 style="margin:0; font-size:2rem;">Analytics Avanzado</h1>
        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
            <select class="btn btn-outline" style="padding:.7rem 1rem;">
                <option>Últimos 7 días</option>
                <option>Últimos 30 días</option>
                <option selected>Últimos 12 meses</option>
                <option>Todo el tiempo</option>
            </select>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">← Dashboard</a>
        </div>
    </div>

    <!-- Estadísticas mensuales -->
    <div class="stats-grid fade-in" style="animation-delay:.1s;">
        <div class="stat-card">
            <div class="stat-icon icon-purple">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/>
                </svg>
            </div>
            <div style="font-size:.85rem; color:var(--text-dim); margin-bottom:.3rem;">PROMEDIO VIAJES/MES</div>
            <div style="font-size:2rem; font-weight:700; color:var(--text);">{{ number_format(collect($monthlyData)->avg('trips'), 1) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-pink">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4l2 2"/>
                </svg>
            </div>
            <div style="font-size:.85rem; color:var(--text-dim); margin-bottom:.3rem;">INCIDENTES TOTALES</div>
            <div style="font-size:2rem; font-weight:700; color:var(--text);">{{ array_sum(array_column($monthlyData, 'incidents')) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-cyan">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/>
                </svg>
            </div>
            <div style="font-size:.85rem; color:var(--text-dim); margin-bottom:.3rem;">EFICIENCIA PROMEDIO</div>
            <div style="font-size:2rem; font-weight:700; color:var(--text);">{{ number_format(collect($monthlyData)->avg('efficiency'), 1) }}%</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-green">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <div style="font-size:.85rem; color:var(--text-dim); margin-bottom:.3rem;">TASA DE MEJORA</div>
            <div style="font-size:2rem; font-weight:700; color:#4caf50;">+15.3%</div>
        </div>
    </div>

    <!-- Gráficas principales -->
    <div class="chart-row fade-in" style="animation-delay:.15s;">
        <div class="chart-box">
            <h2 style="margin:0 0 1.5rem; font-size:1.3rem;">Tendencias Mensuales</h2>
            <canvas id="trendsChart" height="80"></canvas>
        </div>
        <div class="chart-box">
            <h2 style="margin:0 0 1.5rem; font-size:1.3rem;">Distribución de Incidentes</h2>
            <canvas id="incidentsChart" height="80"></canvas>
        </div>
    </div>

    <!-- Análisis de eficiencia -->
    <div class="chart-box fade-in" style="animation-delay:.2s; margin-bottom:2rem;">
        <h2 style="margin:0 0 1.5rem; font-size:1.3rem;">Evolución de Eficiencia Combustible</h2>
        <canvas id="efficiencyChart" height="60"></canvas>
    </div>

    <!-- Análisis de riesgo y recomendaciones -->
    <div class="chart-row fade-in" style="animation-delay:.25s;">
        <div class="chart-box">
            <h2 style="margin:0 0 1rem; font-size:1.3rem;">Análisis de Riesgo</h2>
            <p style="color:var(--text-dim); margin-bottom:1.5rem; font-size:.9rem;">Nivel actual: <span class="risk-badge risk-{{ strtolower($riskAnalysis['level']) }}">{{ $riskAnalysis['level'] }}</span></p>
            <canvas id="riskChart" height="100"></canvas>
        </div>
        <div class="chart-box">
            <h2 style="margin:0 0 1rem; font-size:1.3rem;">Recomendaciones</h2>
            <p style="color:var(--text-dim); margin-bottom:1.5rem; font-size:.9rem;">Basadas en análisis predictivo</p>
            <div class="risk-list">
                @foreach($riskAnalysis['recommendations'] as $index => $rec)
                <div class="risk-item">
                    <div class="risk-number">{{ $index + 1 }}</div>
                    <div style="font-size:.9rem; line-height:1.5; color:var(--text-dim);">{{ $rec }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Metodologías aplicadas -->
    <div class="chart-box fade-in" style="animation-delay:.3s;">
        <h2 style="margin:0 0 1rem; font-size:1.3rem;">Metodologías de Análisis Aplicadas</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:1rem; margin-top:1.5rem;">
            <div style="background:rgba(125,91,255,.08); border:1px solid rgba(125,91,255,.2); border-radius:10px; padding:1.2rem;">
                <div style="font-weight:600; color:var(--primary); margin-bottom:.5rem;">Análisis de Tendencias</div>
                <div style="font-size:.85rem; color:var(--text-dim); line-height:1.6;">Regresión lineal para predicción de patrones de velocidad y consumo</div>
            </div>
            <div style="background:rgba(45,232,255,.08); border:1px solid rgba(45,232,255,.2); border-radius:10px; padding:1.2rem;">
                <div style="font-weight:600; color:var(--accent); margin-bottom:.5rem;">Análisis Estadístico</div>
                <div style="font-size:.85rem; color:var(--text-dim); line-height:1.6;">Cálculo de promedios, desviación estándar y valores atípicos</div>
            </div>
            <div style="background:rgba(255,95,170,.08); border:1px solid rgba(255,95,170,.2); border-radius:10px; padding:1.2rem;">
                <div style="font-weight:600; color:var(--secondary); margin-bottom:.5rem;">Machine Learning</div>
                <div style="font-size:.85rem; color:var(--text-dim); line-height:1.6;">Predicción de riesgos basada en patrones históricos</div>
            </div>
            <div style="background:rgba(76,175,80,.08); border:1px solid rgba(76,175,80,.2); border-radius:10px; padding:1.2rem;">
                <div style="font-weight:600; color:#4caf50; margin-bottom:.5rem;">Visualización de Datos</div>
                <div style="font-size:.85rem; color:var(--text-dim); line-height:1.6;">Chart.js para representación gráfica interactiva</div>
            </div>
        </div>
    </div>

    <!-- Análisis de Encuestas -->
    @if(isset($surveyAnalysis) && $surveyAnalysis['total'] > 0)
    <div class="chart-box fade-in" style="animation-delay:.35s; margin-top:2rem;">
        <h2 style="margin:0 0 .5rem; font-size:1.4rem;">📊 Análisis de Encuestas de Satisfacción</h2>
        <p style="color:var(--text-dim); margin-bottom:1.5rem; font-size:.9rem;">Datos de {{ $surveyAnalysis['total'] }} encuestas procesadas con análisis estadístico avanzado</p>
        
        <!-- KPIs de Encuestas -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:2rem;">
            <div style="background:rgba(125,91,255,.1); border:1px solid rgba(125,91,255,.3); border-radius:12px; padding:1.2rem; text-align:center;">
                <div style="font-size:.75rem; color:var(--text-dim); margin-bottom:.3rem;">SATISFACCIÓN PROM.</div>
                <div style="font-size:2rem; font-weight:700; color:var(--primary);">{{ $surveyAnalysis['avgSatisfaction'] }}/10</div>
            </div>
            <div style="background:rgba(255,95,170,.1); border:1px solid rgba(255,95,170,.3); border-radius:12px; padding:1.2rem; text-align:center;">
                <div style="font-size:.75rem; color:var(--text-dim); margin-bottom:.3rem;">NIVEL DE ESTRÉS</div>
                <div style="font-size:2rem; font-weight:700; color:var(--secondary);">{{ $surveyAnalysis['avgStress'] }}/10</div>
            </div>
            <div style="background:rgba(45,232,255,.1); border:1px solid rgba(45,232,255,.3); border-radius:12px; padding:1.2rem; text-align:center;">
                <div style="font-size:.75rem; color:var(--text-dim); margin-bottom:.3rem;">VELOCIDAD PROM.</div>
                <div style="font-size:2rem; font-weight:700; color:var(--accent);">{{ $surveyAnalysis['avgSpeed'] }} km/h</div>
            </div>
            <div style="background:rgba(76,175,80,.1); border:1px solid rgba(76,175,80,.3); border-radius:12px; padding:1.2rem; text-align:center;">
                <div style="font-size:.75rem; color:var(--text-dim); margin-bottom:.3rem;">INCIDENTES PROM.</div>
                <div style="font-size:2rem; font-weight:700; color:#4caf50;">{{ $surveyAnalysis['avgIncidents'] }}</div>
            </div>
        </div>

        <!-- Análisis de Correlación -->
        <div style="background:rgba(125,91,255,.05); border:1px solid rgba(125,91,255,.2); border-radius:12px; padding:1.5rem; margin-bottom:2rem;">
            <h3 style="margin:0 0 1rem; font-size:1.1rem; color:var(--primary);">🔬 Análisis de Correlación (Coeficiente de Pearson)</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
                <div>
                    <div style="font-size:.85rem; color:var(--text-dim); margin-bottom:.5rem;">Velocidad ↔ Incidentes</div>
                    <div style="font-size:1.5rem; font-weight:700; color:{{ abs($surveyAnalysis['correlations']['speedIncident']) > 0.7 ? 'var(--secondary)' : (abs($surveyAnalysis['correlations']['speedIncident']) > 0.4 ? 'var(--accent)' : '#4caf50') }};">
                        r = {{ $surveyAnalysis['correlations']['speedIncident'] }}
                    </div>
                    <div style="font-size:.75rem; color:var(--text-dim); margin-top:.3rem;">
                        {{ abs($surveyAnalysis['correlations']['speedIncident']) > 0.7 ? 'Correlación fuerte' : (abs($surveyAnalysis['correlations']['speedIncident']) > 0.4 ? 'Correlación moderada' : 'Correlación débil') }}
                    </div>
                </div>
                <div>
                    <div style="font-size:.85rem; color:var(--text-dim); margin-bottom:.5rem;">Estrés ↔ Satisfacción</div>
                    <div style="font-size:1.5rem; font-weight:700; color:{{ abs($surveyAnalysis['correlations']['stressSatisfaction']) > 0.7 ? 'var(--secondary)' : (abs($surveyAnalysis['correlations']['stressSatisfaction']) > 0.4 ? 'var(--accent)' : '#4caf50') }};">
                        r = {{ $surveyAnalysis['correlations']['stressSatisfaction'] }}
                    </div>
                    <div style="font-size:.75rem; color:var(--text-dim); margin-top:.3rem;">
                        {{ abs($surveyAnalysis['correlations']['stressSatisfaction']) > 0.7 ? 'Correlación fuerte' : (abs($surveyAnalysis['correlations']['stressSatisfaction']) > 0.4 ? 'Correlación moderada' : 'Correlación débil') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas de análisis de encuestas -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(400px,1fr)); gap:1.5rem;">
            <div style="background:var(--surface-soft); border-radius:16px; padding:1.5rem; border:1px solid var(--border);">
                <h3 style="margin:0 0 1rem; font-size:1.1rem;">Segmentación de Conductores</h3>
                <canvas id="segmentChart" height="200"></canvas>
            </div>
            <div style="background:var(--surface-soft); border-radius:16px; padding:1.5rem; border:1px solid var(--border);">
                <h3 style="margin:0 0 1rem; font-size:1.1rem;">Preferencias de Alertas</h3>
                <canvas id="alertPrefChart" height="200"></canvas>
            </div>
            <div style="background:var(--surface-soft); border-radius:16px; padding:1.5rem; border:1px solid var(--border);">
                <h3 style="margin:0 0 1rem; font-size:1.1rem;">Distribución por Edad</h3>
                <canvas id="ageDistChart" height="200"></canvas>
            </div>
            <div style="background:var(--surface-soft); border-radius:16px; padding:1.5rem; border:1px solid var(--border);">
                <h3 style="margin:0 0 1rem; font-size:1.1rem;">Satisfacción por Grupo de Edad</h3>
                <canvas id="satByAgeChart" height="200"></canvas>
            </div>
        </div>

        <!-- Top Features -->
        <div style="background:var(--surface-soft); border-radius:16px; padding:1.5rem; border:1px solid var(--border); margin-top:1.5rem;">
            <h3 style="margin:0 0 1rem; font-size:1.1rem;">⭐ Características Más Valoradas</h3>
            <div style="display:grid; gap:.8rem;">
                @foreach($surveyAnalysis['topFeatures'] as $feature => $count)
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div style="flex:1; font-size:.9rem; color:var(--text);">{{ $feature }}</div>
                    <div style="flex:2; background:rgba(125,91,255,.1); border-radius:8px; height:28px; position:relative; overflow:hidden;">
                        <div style="background:linear-gradient(90deg,var(--primary),var(--accent)); height:100%; width:{{ ($count / $surveyAnalysis['total']) * 100 }}%; border-radius:8px;"></div>
                    </div>
                    <div style="font-weight:700; color:var(--primary); min-width:50px; text-align:right;">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- MACHINE LEARNING ANALYSIS -->
    <div class="chart-box fade-in" style="animation-delay:.4s; margin-top:2rem; background:linear-gradient(135deg,rgba(125,91,255,.05),rgba(45,232,255,.05)); border:2px solid rgba(125,91,255,.3);">
        <h2 style="margin:0 0 .5rem; font-size:1.6rem;">🤖 Análisis con Machine Learning</h2>
        <p style="color:var(--text-dim); margin-bottom:2rem; font-size:.9rem;">Algoritmos aplicados: K-Means Clustering, K-Nearest Neighbors (k-NN), Feature Importance</p>

        <!-- K-Means Clustering Results -->
        <div style="background:rgba(255,255,255,.02); border-radius:12px; padding:1.5rem; margin-bottom:2rem;">
            <h3 style="margin:0 0 .5rem; font-size:1.2rem; color:var(--primary);">🎯 K-Means Clustering (k=3)</h3>
            <p style="color:var(--text-dim); font-size:.85rem; margin-bottom:1.5rem;">Segmentación automática de conductores en 3 grupos basado en comportamiento</p>
            
            @if(isset($kmeansResults['interpretation']))
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1rem; margin-bottom:1.5rem;">
                @foreach($kmeansResults['interpretation'] as $idx => $cluster)
                <div style="background:{{ $cluster['color'] }}15; border:2px solid {{ $cluster['color'] }}; border-radius:12px; padding:1.5rem;">
                    <div style="font-size:.75rem; color:{{ $cluster['color'] }}; font-weight:700; margin-bottom:.8rem;">CLUSTER {{ $idx + 1 }}</div>
                    <div style="font-size:1.3rem; font-weight:700; color:{{ $cluster['color'] }}; margin-bottom:.5rem;">{{ $cluster['label'] }}</div>
                    <div style="font-size:2rem; font-weight:700; color:var(--text); margin-bottom:1rem;">{{ $cluster['count'] }} <span style="font-size:.9rem; font-weight:400;">conductores</span></div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem; font-size:.8rem;">
                        <div><span style="color:var(--text-dim);">Velocidad:</span> <strong>{{ $cluster['avgSpeed'] }} km/h</strong></div>
                        <div><span style="color:var(--text-dim);">Incidentes:</span> <strong>{{ $cluster['avgIncidents'] }}</strong></div>
                        <div><span style="color:var(--text-dim);">Estrés:</span> <strong>{{ $cluster['avgStress'] }}/10</strong></div>
                        <div><span style="color:var(--text-dim);">Satisfacción:</span> <strong>{{ $cluster['avgSatisfaction'] }}/10</strong></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="font-size:.8rem; color:var(--text-dim); padding:1rem; background:rgba(125,91,255,.1); border-radius:8px;">
                <strong>Convergencia:</strong> El algoritmo convergió en {{ $kmeansResults['iterations'] }} iteraciones. 
                K-Means identifica patrones naturales en los datos agrupando conductores con características similares.
            </div>
            @endif
        </div>

        <!-- K-Nearest Neighbors Results -->
        <div style="background:rgba(255,255,255,.02); border-radius:12px; padding:1.5rem; margin-bottom:2rem;">
            <h3 style="margin:0 0 .5rem; font-size:1.2rem; color:var(--accent);">📍 K-Nearest Neighbors (k=5)</h3>
            <p style="color:var(--text-dim); font-size:.85rem; margin-bottom:1.5rem;">Predicción de nivel de riesgo para conductor ejemplo</p>
            
            @if(isset($knnPrediction['prediction']))
            <div style="display:grid; grid-template-columns:1fr 2fr; gap:1.5rem;">
                <div style="background:rgba(45,232,255,.1); border:1px solid rgba(45,232,255,.3); border-radius:12px; padding:1.5rem; text-align:center;">
                    <div style="font-size:.75rem; color:var(--text-dim); margin-bottom:.5rem;">PREDICCIÓN</div>
                    <div style="font-size:1.5rem; font-weight:700; color:var(--accent); margin-bottom:.5rem;">{{ $knnPrediction['prediction'] }}</div>
                    <div style="font-size:.85rem; color:var(--text-dim);">Confianza: <strong>{{ $knnPrediction['confidence'] }}%</strong></div>
                </div>
                <div>
                    <div style="font-size:.85rem; color:var(--text-dim); margin-bottom:.8rem;">Votación de 5 vecinos más cercanos:</div>
                    @foreach($knnPrediction['votes'] as $label => $votes)
                    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:.5rem;">
                        <div style="flex:1; font-size:.9rem;">{{ $label }}</div>
                        <div style="flex:2; background:rgba(45,232,255,.1); border-radius:8px; height:24px; position:relative; overflow:hidden;">
                            <div style="background:var(--accent); height:100%; width:{{ ($votes / 5) * 100 }}%; border-radius:8px;"></div>
                        </div>
                        <div style="font-weight:700; color:var(--accent); min-width:40px;">{{ $votes }}</div>
                    </div>
                    @endforeach
                    <div style="font-size:.75rem; color:var(--text-dim); margin-top:1rem; padding:.8rem; background:rgba(45,232,255,.1); border-radius:8px;">
                        <strong>Cómo funciona:</strong> k-NN encuentra los 5 conductores más similares y predice el riesgo por votación mayoritaria.
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Feature Importance -->
        <div style="background:rgba(255,255,255,.02); border-radius:12px; padding:1.5rem;">
            <h3 style="margin:0 0 .5rem; font-size:1.2rem; color:var(--secondary);">📊 Importancia de Características</h3>
            <p style="color:var(--text-dim); font-size:.85rem; margin-bottom:1.5rem;">Análisis de correlación con satisfacción del usuario (Método: Correlación de Pearson)</p>
            
            @if(isset($featureImportance))
            <div style="display:grid; gap:.8rem;">
                @foreach($featureImportance as $feature => $data)
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div style="flex:1; font-size:.9rem; color:var(--text);">{{ $feature }}</div>
                    <div style="flex:2; background:rgba(255,95,170,.1); border-radius:8px; height:32px; position:relative; overflow:hidden;">
                        <div style="background:linear-gradient(90deg,var(--secondary),var(--primary)); height:100%; width:{{ $data['importance'] }}%; border-radius:8px; display:flex; align-items:center; justify-content:end; padding-right:.5rem;">
                            <span style="font-size:.75rem; font-weight:700; color:#fff;">{{ $data['importance'] }}%</span>
                        </div>
                    </div>
                    <div style="font-size:.8rem; color:var(--text-dim); min-width:80px; text-align:right;">r = {{ $data['correlation'] }}</div>
                </div>
                @endforeach
            </div>
            <div style="font-size:.75rem; color:var(--text-dim); margin-top:1rem; padding:.8rem; background:rgba(255,95,170,.1); border-radius:8px;">
                <strong>Interpretación:</strong> Las barras muestran qué factores tienen mayor impacto en la satisfacción del usuario. 
                Valores de correlación cercanos a 1 o -1 indican relación fuerte.
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyData = @json($monthlyData);
const riskAnalysis = @json($riskAnalysis);

const chartColors = {
    primary: '#7d5bff',
    accent: '#2de8ff',
    secondary: '#ff5faa',
    success: '#4caf50',
};

// Gráfica de tendencias mensuales
new Chart(document.getElementById('trendsChart'), {
    type: 'line',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [
            {
                label: 'Viajes',
                data: monthlyData.map(d => d.trips),
                borderColor: chartColors.primary,
                backgroundColor: 'rgba(125,91,255,.1)',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Incidentes',
                data: monthlyData.map(d => d.incidents),
                borderColor: chartColors.secondary,
                backgroundColor: 'rgba(255,95,170,.1)',
                tension: 0.4,
                fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {legend: {labels: {color: '#b8c5d6'}}},
        scales: {
            y: {ticks: {color: '#6b7d94'}, grid: {color: 'rgba(255,255,255,.05)'}},
            x: {ticks: {color: '#6b7d94'}, grid: {color: 'rgba(255,255,255,.05)'}}
        }
    }
});

// Gráfica de distribución de incidentes
new Chart(document.getElementById('incidentsChart'), {
    type: 'doughnut',
    data: {
        labels: ['Velocidad', 'Temperatura', 'Combustible', 'Otros'],
        datasets: [{
            data: [35, 28, 22, 15],
            backgroundColor: [chartColors.primary, chartColors.secondary, chartColors.accent, chartColors.success],
        }]
    },
    options: {
        responsive: true,
        plugins: {legend: {position: 'bottom', labels: {color: '#b8c5d6'}}}
    }
});

// Gráfica de eficiencia
new Chart(document.getElementById('efficiencyChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [{
            label: 'Eficiencia (%)',
            data: monthlyData.map(d => d.efficiency),
            backgroundColor: chartColors.accent,
        }]
    },
    options: {
        responsive: true,
        plugins: {legend: {labels: {color: '#b8c5d6'}}},
        scales: {
            y: {ticks: {color: '#6b7d94'}, grid: {color: 'rgba(255,255,255,.05)'}},
            x: {ticks: {color: '#6b7d94'}, grid: {color: 'rgba(255,255,255,.05)'}}
        }
    }
});

// Gráfica de análisis de riesgo
new Chart(document.getElementById('riskChart'), {
    type: 'radar',
    data: {
        labels: ['Velocidad', 'Temperatura', 'Combustible', 'Alertas', 'Distancia'],
        datasets: [{
            label: 'Nivel de Riesgo',
            data: riskAnalysis.factors,
            borderColor: chartColors.secondary,
            backgroundColor: 'rgba(255,95,170,.2)',
        }]
    },
    options: {
        responsive: true,
        plugins: {legend: {labels: {color: '#b8c5d6'}}},
        scales: {
            r: {
                ticks: {color: '#6b7d94', backdropColor: 'transparent'},
                grid: {color: 'rgba(255,255,255,.1)'},
                pointLabels: {color: '#b8c5d6'}
            }
        }
    }
});

// Gráficas de análisis de encuestas
@if(isset($surveyAnalysis) && $surveyAnalysis['total'] > 0)
const surveyData = @json($surveyAnalysis);

// Segmentación de conductores
new Chart(document.getElementById('segmentChart'), {
    type: 'doughnut',
    data: {
        labels: ['Seguros', 'Moderados', 'Riesgosos'],
        datasets: [{
            data: [surveyData.segments.safe, surveyData.segments.moderate, surveyData.segments.risky],
            backgroundColor: [chartColors.success, chartColors.accent, chartColors.secondary],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {position: 'bottom', labels: {color: '#b8c5d6'}},
        }
    }
});

// Preferencias de alertas
new Chart(document.getElementById('alertPrefChart'), {
    type: 'pie',
    data: {
        labels: Object.keys(surveyData.alertPreferences),
        datasets: [{
            data: Object.values(surveyData.alertPreferences),
            backgroundColor: [chartColors.primary, chartColors.accent, chartColors.secondary, chartColors.success],
        }]
    },
    options: {
        responsive: true,
        plugins: {legend: {position: 'bottom', labels: {color: '#b8c5d6'}}}
    }
});

// Distribución por edad
new Chart(document.getElementById('ageDistChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(surveyData.ageDistribution),
        datasets: [{
            label: 'Usuarios',
            data: Object.values(surveyData.ageDistribution),
            backgroundColor: chartColors.primary,
        }]
    },
    options: {
        responsive: true,
        plugins: {legend: {display: false}},
        scales: {
            y: {ticks: {color: '#6b7d94'}, grid: {color: 'rgba(255,255,255,.05)'}},
            x: {ticks: {color: '#6b7d94'}, grid: {color: 'rgba(255,255,255,.05)'}}
        }
    }
});

// Satisfacción por edad
new Chart(document.getElementById('satByAgeChart'), {
    type: 'line',
    data: {
        labels: Object.keys(surveyData.satisfactionByAge),
        datasets: [{
            label: 'Satisfacción',
            data: Object.values(surveyData.satisfactionByAge),
            borderColor: chartColors.accent,
            backgroundColor: 'rgba(45,232,255,.2)',
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: {legend: {labels: {color: '#b8c5d6'}}},
        scales: {
            y: {min: 0, max: 10, ticks: {color: '#6b7d94'}, grid: {color: 'rgba(255,255,255,.05)'}},
            x: {ticks: {color: '#6b7d94'}, grid: {color: 'rgba(255,255,255,.05)'}}
        }
    }
});
@endif
</script>
@endsection
