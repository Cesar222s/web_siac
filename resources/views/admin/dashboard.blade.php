@extends('layouts.main')
@section('title','Dashboard Admin')
@section('content')
<style>
    .dashboard-grid {display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:1.5rem; margin-bottom:2rem;}
    .metric-card {background:linear-gradient(135deg,rgba(29,39,56,.9),rgba(38,52,74,.6)); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:1.5rem; position:relative; overflow:hidden;}
    .metric-card:before {content:''; position:absolute; top:-50%; right:-30%; width:150px; height:150px; background:radial-gradient(circle,rgba(125,91,255,.2),transparent 70%); filter:blur(25px);}
    .metric-value {font-size:2.5rem; font-weight:700; background:linear-gradient(120deg,var(--primary),var(--accent)); -webkit-background-clip:text; background-clip:text; color:transparent; margin:.5rem 0; position:relative;}
    .metric-label {font-size:.85rem; color:var(--text-dim); letter-spacing:.05em; position:relative;}
    .chart-container {background:var(--surface-soft); border-radius:18px; padding:2rem; margin-bottom:2rem; border:1px solid var(--border);}
    .predictions-grid {display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-top:2rem;}
    .prediction-item {background:rgba(125,91,255,.1); border:1px solid rgba(125,91,255,.3); border-radius:12px; padding:1rem; text-align:center;}
    .risk-badge {display:inline-block; padding:.4rem .8rem; border-radius:20px; font-size:.75rem; font-weight:600;}
    .risk-high {background:rgba(255,95,170,.2); color:#ff5faa; border:1px solid rgba(255,95,170,.4);}
    .risk-medium {background:rgba(255,183,77,.2); color:#ffb74d; border:1px solid rgba(255,183,77,.4);}
    .risk-low {background:rgba(45,232,255,.2); color:#2de8ff; border:1px solid rgba(45,232,255,.4);}
</style>

<div class="fade-in">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <h1 style="margin:0; font-size:2rem;">Dashboard de Análisis</h1>
        <div style="display:flex; gap:1rem;">
            <a href="{{ route('admin.analytics') }}" class="btn btn-outline">Analytics Avanzado</a>
            <button class="btn" onclick="exportData()">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:.5rem;">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Exportar Datos
            </button>
        </div>
    </div>

    <!-- Métricas principales -->
    <div class="dashboard-grid fade-in" style="animation-delay:.1s;">
        <div class="metric-card">
            <div class="metric-label">ENCUESTAS TOTALES</div>
            <div class="metric-value">{{ $totalSurveys }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">SATISFACCIÓN PROMEDIO</div>
            <div class="metric-value">{{ $metrics['avgSatisfaction'] }} <span style="font-size:1rem;">/10</span></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">NIVEL DE ESTRÉS</div>
            <div class="metric-value">{{ $metrics['avgStress'] }} <span style="font-size:1rem;">/10</span></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">VELOCIDAD PROMEDIO</div>
            <div class="metric-value">{{ $metrics['avgSpeed'] }} <span style="font-size:1rem;">km/h</span></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">TOTAL INCIDENTES</div>
            <div class="metric-value">{{ $metrics['totalIncidents'] }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">EXPERIENCIA PROM.</div>
            <div class="metric-value">{{ $metrics['avgExperience'] }} <span style="font-size:1rem;">años</span></div>
        </div>
    </div>

    <!-- Gráfica de satisfacción por edad -->
    <div class="chart-container fade-in" style="animation-delay:.15s;">
        <h2 style="margin:0 0 1.5rem; font-size:1.4rem;">Satisfacción por Grupo de Edad</h2>
        <canvas id="satisfactionChart" height="80"></canvas>
    </div>

    <!-- Gráficas comparativas -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(400px,1fr)); gap:1.5rem; margin-bottom:2rem;">
        <div class="chart-container fade-in" style="animation-delay:.2s;">
            <h2 style="margin:0 0 1.5rem; font-size:1.4rem;">Nivel de Estrés por Edad</h2>
            <canvas id="stressChart" height="120"></canvas>
        </div>
        <div class="chart-container fade-in" style="animation-delay:.25s;">
            <h2 style="margin:0 0 1.5rem; font-size:1.4rem;">Incidentes por Velocidad</h2>
            <canvas id="incidentsChart" height="120"></canvas>
        </div>
    </div>

    <!-- Segmentación y recomendaciones -->
    <div class="chart-container fade-in" style="animation-delay:.3s;">
        <h2 style="margin:0 0 1rem; font-size:1.4rem;">Segmentación de Usuarios</h2>
        <p style="color:var(--text-dim); margin-bottom:1.5rem;">Análisis mediante clustering K-Means</p>
        
        <div class="predictions-grid">
            @if(is_array($segmentation))
                @foreach($segmentation as $segment => $count)
                <div class="prediction-item">
                    <div style="font-size:.8rem; color:var(--text-dim); margin-bottom:.5rem;">
                        @if($segment === 'safe') SEGURO
                        @elseif($segment === 'moderate') MODERADO
                        @elseif($segment === 'risky') RIESGOSO
                        @else {{ strtoupper($segment) }}
                        @endif
                    </div>
                    <div style="font-size:1.8rem; font-weight:700; color:var(--primary);">{{ $count }}</div>
                    <div style="font-size:.75rem; color:var(--text-dim); margin-top:.5rem;">usuarios</div>
                </div>
                @endforeach
            @else
                <div style="color:var(--text-dim);">No hay datos de segmentación disponibles</div>
            @endif
        </div>
    </div>

    <!-- Recomendaciones automáticas -->
    <div class="chart-container fade-in" style="animation-delay:.35s;">
        <h2 style="margin:0 0 1rem; font-size:1.4rem;">Recomendaciones del Sistema</h2>
        <div style="display:grid; gap:1rem;">
            @foreach($recommendations as $rec)
            <div style="background:rgba(125,91,255,.05); border-left:3px solid var(--primary); padding:1rem; border-radius:8px;">
                <div style="font-weight:600; color:var(--primary); margin-bottom:.5rem;">{{ $rec['title'] }}</div>
                <div style="color:var(--text-dim); font-size:.9rem;">{{ $rec['description'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartColors = {
    primary: '#7d5bff',
    accent: '#2de8ff',
    secondary: '#ff5faa',
    success: '#4caf50',
    warning: '#ffb74d',
};

// Datos desde PHP
const chartData = @json($chartData);

// Configuración común para todas las gráficas
const commonOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
        legend: {
            labels: {color: '#b8c5d6', font: {size: 12}}
        }
    },
    scales: {
        y: {
            ticks: {color: '#6b7d94'},
            grid: {color: 'rgba(255,255,255,.05)'}
        },
        x: {
            ticks: {color: '#6b7d94'},
            grid: {color: 'rgba(255,255,255,.05)'}
        }
    }
};

// Gráfica de satisfacción por edad
new Chart(document.getElementById('satisfactionChart'), {
    type: 'line',
    data: {
        labels: chartData.ageGroups,
        datasets: [{
            label: 'Satisfacción Promedio',
            data: chartData.satisfactionByAge,
            borderColor: chartColors.primary,
            backgroundColor: 'rgba(125,91,255,.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 2,
        }]
    },
    options: commonOptions
});

// Gráfica de estrés por edad
new Chart(document.getElementById('stressChart'), {
    type: 'bar',
    data: {
        labels: chartData.ageGroups,
        datasets: [{
            label: 'Nivel de Estrés',
            data: chartData.stressByAge,
            backgroundColor: chartColors.secondary,
            borderColor: chartColors.secondary,
            borderWidth: 1,
        }]
    },
    options: commonOptions
});

// Gráfica de incidentes por velocidad
new Chart(document.getElementById('incidentsChart'), {
    type: 'bar',
    data: {
        labels: chartData.speedRanges,
        datasets: [{
            label: 'Total Incidentes',
            data: chartData.incidentsBySpeed,
            backgroundColor: chartColors.accent,
            borderColor: chartColors.accent,
            borderWidth: 1,
        }]
    },
    options: commonOptions
});

function exportData() {
    alert('Exportando datos en formato CSV...\nEsta funcionalidad guardará los datos de análisis para su uso en herramientas externas.');
}
</script>
@endsection
