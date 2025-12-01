@extends('layouts.main')
@section('title','Modelo Supervisado')
@section('content')
<div class="card fade-in" style="max-width:1100px; margin:0 auto;">
    <h1 style="margin-top:0;">Modelos Supervisados Basados en el Informe</h1>
    <p style="font-size:.9rem; color:var(--text-dim);">Demostración de cuatro enfoques distintos (Clasificación Binaria, Severidad, Tipo de Accidente y Regresión de Heridos) utilizando el dataset cargado. Sirve para ilustrar cómo los patrones mostrados en el informe analítico pueden apoyar decisiones predictivas.</p>

    @if(!$single['available'])
        <div class="alert alert-error" style="margin-top:1rem;">{{ $single['message'] ?? 'Modelo simple no disponible.' }}</div>
    @endif
    @if(!$multi['available'])
        <div class="alert alert-error" style="margin-top:1rem;">{{ $multi['message'] ?? 'Modelos múltiples no disponibles.' }}</div>
    @endif

    @if($single['available'] && $multi['available'])
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1rem; margin-top:1.2rem;">
        <div class="card" style="padding:1rem;">
            <h3 style="margin:.2rem 0 .4rem;font-size:.8rem;letter-spacing:.1em;">DATASET</h3>
            <p style="margin:0;font-size:.7rem;">Filas: {{ $multi['summary']['rows'] }}</p>
            <p style="margin:0;font-size:.7rem;">Features: {{ count($multi['summary']['features']) }}</p>
            <p style="margin:0;font-size:.7rem;">Ejemplo feature: {{ $multi['summary']['features'][0] }}</p>
        </div>
        <div class="card" style="padding:1rem;">
            <h3 style="margin:.2rem 0 .4rem;font-size:.8rem;letter-spacing:.1em;">BINARIO (Heridos)</h3>
            <p style="margin:0;font-size:.7rem;">Accuracy: {{ $multi['binary_injury']['accuracy'] }}</p>
            <table style="width:100%; font-size:.6rem; margin-top:.4rem; border-collapse:collapse;">
                <tr><th></th><th>Pred 0</th><th>Pred 1</th></tr>
                <tr><td>Real 0</td><td>{{ $single['confusion']['TN'] }}</td><td>{{ $single['confusion']['FP'] }}</td></tr>
                <tr><td>Real 1</td><td>{{ $single['confusion']['FN'] }}</td><td>{{ $single['confusion']['TP'] }}</td></tr>
            </table>
        </div>
        <div class="card" style="padding:1rem;">
            <h3 style="margin:.2rem 0 .4rem;font-size:.8rem;letter-spacing:.1em;">SEVERIDAD</h3>
            <p style="margin:0;font-size:.7rem;">Accuracy: {{ $multi['severity']['accuracy'] }}</p>
            <p style="margin:0;font-size:.7rem;">Regla: {{ $multi['severity']['baseline'] }}</p>
            <table style="width:100%; font-size:.6rem; margin-top:.4rem; border-collapse:collapse;">
                <tr><th></th>@foreach($multi['severity']['labels'] as $lab)<th>{{ $lab }}</th>@endforeach</tr>
                @foreach($multi['severity']['labels'] as $real)
                    <tr><td>Real {{ $real }}</td>@foreach($multi['severity']['labels'] as $pred)<td>{{ $multi['severity']['matrix'][$real][$pred] }}</td>@endforeach</tr>
                @endforeach
            </table>
        </div>
        <div class="card" style="padding:1rem;">
            <h3 style="margin:.2rem 0 .4rem;font-size:.8rem;letter-spacing:.1em;">TIPO ACCIDENTE</h3>
            <p style="margin:0;font-size:.7rem;">Accuracy: {{ $multi['type_classification']['accuracy'] }}</p>
            <table style="width:100%; font-size:.6rem; margin-top:.4rem; border-collapse:collapse;">
                <tr><th></th>@foreach($multi['type_classification']['labels'] as $lab)<th>{{ $lab }}</th>@endforeach</tr>
                @foreach($multi['type_classification']['labels'] as $real)
                    <tr><td>Real {{ $real }}</td>@foreach($multi['type_classification']['labels'] as $pred)<td>{{ $multi['type_classification']['matrix'][$real][$pred] }}</td>@endforeach</tr>
                @endforeach
            </table>
            <p style="margin:.3rem 0 0; font-size:.55rem; color:var(--text-dim);">Map top8: @foreach($multi['type_classification']['mapping'] as $orig=>$mapped) {{ $orig }}→{{ $mapped }} @endforeach (resto=0)</p>
        </div>
        <div class="card" style="padding:1rem;">
            <h3 style="margin:.2rem 0 .4rem;font-size:.8rem;letter-spacing:.1em;">REGRESIÓN HERIDOS</h3>
            <p style="margin:0;font-size:.7rem;">MAE: {{ $multi['injury_regression']['mae'] }}</p>
            <p style="margin:0;font-size:.7rem;">RMSE: {{ $multi['injury_regression']['rmse'] }}</p>
            <p style="margin:0;font-size:.7rem;">R²: {{ $multi['injury_regression']['r2'] }}</p>
            <details style="margin-top:.4rem; font-size:.6rem;">
                <summary>Coeficientes</summary>
                <ul style="list-style:none; margin:.3rem 0 0; padding:0;">
                    @foreach($multi['injury_regression']['coefficients'] as $i=>$coef)
                        <li>{{ $i==0?'Intercepto':'β'.$i }}: {{ number_format($coef,4) }}</li>
                    @endforeach
                </ul>
            </details>
        </div>
    </div>
    <div style="margin-top:1.4rem; font-size:.65rem; color:var(--text-dim);">
        <p>Notas: Modelos simplificados sin normalización ni validación cruzada. Para producción: estandarizar variables, balancear clases, usar regularización y evaluación estratificada.</p>
    </div>
    @endif
    <div style="margin-top:1.2rem;">
        <a href="{{ route('home') }}" class="btn btn-outline">← Volver al inicio</a>
    </div>
</div>
@endsection
