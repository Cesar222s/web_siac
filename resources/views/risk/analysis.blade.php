@extends('layouts.main')
@section('title','Análisis de Riesgo')
@section('content')
<div class="card fade-in" style="position:relative;">
  <h1 style="margin-top:0; color:var(--primary-alt);">Análisis de Riesgo</h1>

  <form action="{{ route('profile.risk.analyze') }}" method="POST" style="margin:1rem 0 1.2rem; display:grid; gap:1rem; grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
      @csrf
      <div class="group">
        <label for="edad">Edad</label>
        <input type="number" id="edad" name="edad" value="{{ old('edad', optional($user)->profile['driver_age'] ?? 30) }}" min="18" max="100" required>
      </div>
      <div class="group">
        <label for="experiencia_anios">Años de experiencia</label>
        <input type="number" id="experiencia_anios" name="experiencia_anios" value="{{ old('experiencia_anios', optional($user)->profile['experience_years'] ?? 3) }}" min="0" max="70" required>
      </div>
      <div class="group" style="grid-column:1/-1;">
        <label for="ubicacion">Ubicación habitual</label>
        <input type="text" id="ubicacion" name="ubicacion" value="{{ old('ubicacion', optional($user)->profile['usual_location'] ?? '') }}" placeholder="Ej: Guadalajara" required>
      </div>
      <div class="group" style="grid-column:1/-1;">
        <label for="horario_uso">Horario de uso</label>
        <select id="horario_uso" name="horario_uso" required>
          @php $h = old('horario_uso', 'Noche'); @endphp
          <option value="Madrugada" {{ $h==='Madrugada'?'selected':'' }}>Madrugada</option>
          <option value="Mañana" {{ $h==='Mañana'?'selected':'' }}>Mañana</option>
          <option value="Tarde" {{ $h==='Tarde'?'selected':'' }}>Tarde</option>
          <option value="Noche" {{ $h==='Noche'?'selected':'' }}>Noche</option>
        </select>
      </div>
      <div style="grid-column:1/-1; display:flex; gap:.6rem;">
        <button class="btn glow" type="submit">Analizar</button>
        <a class="btn" href="{{ route('profile') }}">Volver al perfil</a>
      </div>
  </form>

  @if($error)
    <div class="alert" style="background:#ffe6ea; border:1px solid #ff5faa; color:#b00020; padding:.8rem; border-radius:8px;">
      {{ $error }}
    </div>
  @endif

  @if($risk)
    <div style="margin-top:1rem;">
      <h2 style="margin-top:.2rem; color:var(--primary-alt);">Resultado</h2>
      <div style="display:grid; gap:1rem; grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
        <div class="card" style="padding:1rem;">
          <strong>Usuario</strong>
          <pre style="white-space:pre-wrap; word-break:break-word;">{{ json_encode($risk['usuario'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        <div class="card" style="padding:1rem;">
          <strong>Predicción</strong>
          <pre style="white-space:pre-wrap; word-break:break-word;">{{ json_encode($risk['prediccion'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        <div class="card" style="padding:1rem;">
          <strong>Insights</strong>
          <ul style="margin:.4rem 0; padding-left:1rem;">
            @foreach(($risk['insights'] ?? []) as $i)
              <li>{{ $i }}</li>
            @endforeach
          </ul>
        </div>
      </div>

      <div class="card" style="padding:1rem; margin-top:1rem;">
        <strong>Estadísticas</strong>
        <pre style="white-space:pre-wrap; word-break:break-word;">{{ json_encode($risk['estadisticas'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
      </div>

      <div class="card" style="padding:1rem; margin-top:1rem;">
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
    </div>
  @endif
</div>
@endsection
