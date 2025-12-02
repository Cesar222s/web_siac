@extends('layouts.main')
@section('title','Dashboard')
@section('content')
<style>
  .grid {display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;}
  .card {background: var(--surface-soft); border:1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 1.2rem;}
  .map {height: 420px; background: #0b1624; border-radius: 12px; border:1px solid rgba(125,91,255,.3); position:relative;}
  .badge {display:inline-block; background: rgba(125,91,255,.15); border:1px solid rgba(125,91,255,.35); padding:.4rem .7rem; border-radius: 10px; font-size:.8rem;}
</style>

<div style="display:flex; align-items:center; gap:.8rem; margin-bottom: 1rem;">
  <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h7v7H3z"/><path d="M14 3h7v7h-7z"/><path d="M14 14h7v7h-7z"/><path d="M3 14h7v7H3z"/></svg>
  <h1 style="margin:0;">Dashboard: Zonas Críticas (K-Means)</h1>
</div>
<p class="badge">k = {{ $k }}</p>
@if(isset($available) && !$available)
  <div class="card" style="margin-top:1rem; border-color:#e08a8a; background:rgba(224,138,138,.12);">
    <strong>Datos no disponibles:</strong> {{ $message }}
  </div>
@endif

<div class="grid">
  @if(!isset($available) || $available)
  <div class="card">
    <h2 style="margin-top:0;">Resumen</h2>
    <p>Se identificaron {{ $zonesCount }} zonas críticas con mayor incidencia de accidentes.</p>
    <ul>
      @foreach($clusters as $i => $c)
        @php
          $label0 = $columns[0] ?? 'dim0';
          $label1 = $columns[1] ?? 'dim1';
        @endphp
        <li>
          Zona {{ $i+1 }}:
          Centroide ({{ $label0 }}: {{ is_array($c) ? ($c[0] ?? '?') : '?' }},
          {{ $label1 }}: {{ is_array($c) ? ($c[1] ?? '?') : '?' }}) —
          {{ $counts[$i] ?? 0 }} puntos
        </li>
      @endforeach
    </ul>
  </div>
  <div class="card">
    <h2 style="margin-top:0;">Mapa de calor (web)</h2>
    <div class="map" id="heatmap"></div>
    <p style="font-size:.85rem; color:var(--text-dim)">Vista previa: los clústeres se proyectan como densidades. (Integrar librería de mapas: Leaflet/Mapbox para interactivo).</p>
  </div>
  @endif
</div>

<script>
  // Placeholder heatmap render using points density
  const points = @json($points);
  const el = document.getElementById('heatmap');
  if (el && Array.isArray(points)) {
    const ctx = el.getContext ? el.getContext('2d') : null;
    if (!ctx) {
      const info = document.createElement('div');
      info.style.position = 'absolute';
      info.style.inset = '0';
      info.style.display = 'grid';
      info.style.placeItems = 'center';
      info.style.color = '#89a3ff';
      info.textContent = 'Integrar mapa interactivo (Leaflet/Mapbox)';
      el.appendChild(info);
    }
  }
</script>
@endsection