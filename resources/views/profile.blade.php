@extends('layouts.main')
@section('title','Perfil')
@section('content')

<div class="card profile-box fade-in" style="position:relative;">
    <h1 style="margin-top:0; color:var(--primary-alt); display:flex; align-items:center; gap:.6rem;">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3 9-8 10-5-1-8-5-8-10V6l8-4z"/><path d="M10 12l2 2 4-4"/><circle cx="12" cy="12" r="3"/></svg>
        Panel SIAC
    </h1>

    <p style="margin:.8rem 0 .4rem;"><strong>Nombre completo:</strong> {{ $user->name }} @if(!empty($user->last_name)) {{ $user->last_name }} @endif</p>

    <p style="margin:.4rem 0 .4rem;"><strong>Correo:</strong> {{ $user->email }}</p>

    @if($user->created_at)
        <p style="margin:.4rem 0 1rem;">
            <strong>Cuenta creada:</strong> {{ $user->created_at->format('d/m/Y') }}
        </p>
    @endif

    <form action="{{ route('logout') }}" method="POST" style="margin-top:1rem;">
        @csrf
        <button class="btn">Cerrar Sesión</button>
    </form>
</div>


<div class="card fade-in" style="margin-top:1rem;">
    <h2 style="margin-top:0; color:var(--primary-alt);">Análisis Personalizado de Riesgo</h2>

    @php($profile = is_array($user->profile ?? null) ? $user->profile : (json_decode($user->profile ?? '[]', true) ?: []))
    @php($risk = $profile['risk_result'] ?? null)
    @php($ts = $profile['risk_timestamp'] ?? null)


    @if($risk)
        <p style="margin:.2rem 0 1rem; color:#666">Generado: 
            {{ $ts ? \Carbon\Carbon::parse($ts)->format('d/m/Y H:i') : 'N/D' }}.
            Este resultado es de solo lectura.
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1rem;">

            {{-- SCORE DE RIESGO (VISIBLE) --}}
            <div class="card" style="padding:1rem;">
                <strong>Score de riesgo</strong>
                <pre style="white-space:pre-wrap; word-break:break-word;">
{{ json_encode($risk['prediccion'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}
                </pre>
            </div>

            {{-- USUARIO (OCULTO) --}}
            <div class="card" style="padding:1rem;" hidden>
                <strong>Usuario</strong>
                @php($usr = $risk['usuario'] ?? [])
                <table style="width:100%; border-collapse:collapse; margin-top:.6rem;">
                    <tbody>
                        @if(isset($usr['nombre']))
                        <tr>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);"><strong>Nombre</strong></td>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);">{{ $usr['nombre'] }}</td>
                        </tr>
                        @endif

                        @if(isset($usr['edad']))
                        <tr>
                            <td style="padding:.4rem .6rem; border:11px solid var(--border);"><strong>Edad</strong></td>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);">{{ $usr['edad'] }}</td>
                        </tr>
                        @endif

                        @if(isset($usr['experiencia_anios']))
                        <tr>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);"><strong>Años de experiencia</strong></td>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);">{{ $usr['experiencia_anios'] }}</td>
                        </tr>
                        @endif

                        @if(isset($usr['ubicacion']))
                        <tr>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);"><strong>Ubicación</strong></td>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);">{{ $usr['ubicacion'] }}</td>
                        </tr>
                        @endif

                        @if(isset($usr['horario_uso']))
                        <tr>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);"><strong>Horario de uso</strong></td>
                            <td style="padding:.4rem .6rem; border:1px solid var(--border);">{{ $usr['horario_uso'] }}</td>
                        </tr>
                        @endif

                        @if(empty($usr))
                        <tr>
                            <td colspan="2" style="padding:.6rem; border:1px solid var(--border); color:#666;">Sin datos de usuario</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- INSIGHTS (ÚNICO VISIBLE) --}}
            <div class="card" style="padding:1rem;">
                <strong>Insights</strong>
                <ul style="margin:.4rem 0; padding-left:1rem;">
                    @foreach(($risk['insights'] ?? []) as $i)
                        <li>{{ $i }}</li>
                    @endforeach
                    @if(empty($risk['insights']))
                        <li>No disponible</li>
                    @endif
                </ul>
            </div>

        </div>


        {{-- ESTADÍSTICAS (OCULTO) --}}
        <div class="card" style="padding:1rem; margin-top:1rem;" hidden>
            <strong>Estadísticas</strong>
            <pre style="white-space:pre-wrap; word-break:break-word;">
{{ json_encode($risk['estadisticas'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}
            </pre>
        </div>

        {{-- VISUALIZACIONES (OCULTO) --}}
        <div class="card" style="padding:1rem; margin-top:1rem;" hidden>
            <strong>Visualizaciones</strong>
            <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
                @if(isset($risk['visualizaciones']['risk_gauge']))
                    <div>
                        <img alt="Risk Gauge" src="{{ $risk['visualizaciones']['risk_gauge'] }}" style="width:100%; height:auto; border:1px solid var(--border); border-radius:8px;" />
                    </div>
                @endif

                @if(isset($risk['visualizaciones']['factors_comparison']))
                    <div>
                        <img alt="Factors" src="{{ $risk['visualizaciones']['factors_comparison'] }}" style="width:100%; height:auto; border:1px solid var(--border); border-radius:8px;" />
                    </div>
                @endif

                @if(isset($risk['visualizaciones']['population_distribution']))
                    <div>
                        <img alt="Population" src="{{ $risk['visualizaciones']['population_distribution'] }}" style="width:100%; height:auto; border:1px solid var(--border); border-radius:8px;" />
                    </div>
                @endif
            </div>
        </div>

    @else
        <p style="margin:.2rem 0 1rem; color:#666">Aún no hay análisis almacenado.</p>
        <a class="btn" href="{{ route('profile.risk.show') }}">Generar análisis</a>
    @endif
</div>

@endsection
