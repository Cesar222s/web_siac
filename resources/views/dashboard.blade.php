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
  <h1 style="margin:0;">Dashboard: Zonas Críticas ({{ request()->input('model','kmeans') === 'dbscan' ? 'DBSCAN' : 'K-Means' }})</h1>
</div>
@if(request()->input('model','kmeans') === 'dbscan')
  <div style="display:flex; gap:.6rem; align-items:center; margin:.6rem 0 1rem;">
    <span class="badge">eps = {{ request()->input('eps', 0.01) }}</span>
    <span class="badge">minPts = {{ request()->input('minPts', 20) }}</span>
  </div>
@else
  <p class="badge">k = {{ $k }}</p>
@endif
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
    <h3 style="margin:1rem 0 .5rem;">🥇 Top 5 zonas de mayor riesgo</h3>
    <ol>
      @foreach(($top5 ?? []) as $i => $t)
        <li>Zona {{ $i+1 }} — {{ $t['count'] }} puntos</li>
      @endforeach
    </ol>
  </div>
  <div class="card">
    <h2 style="margin-top:0;">Mapa de calor (web)</h2>
    <div class="map" id="heatmap"></div>
    <p style="font-size:.85rem; color:var(--text-dim)">Vista: densidad de puntos y centroides (Leaflet + Heatmap).</p>
  </div>
  @endif
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script>
  const points = @json($points);
  const clusters = @json($clusters);
  const top5 = @json($top5);
  const counts = @json($counts);
  const colLabels = @json($columns);
  const mapEl = document.getElementById('heatmap');
  if (mapEl) {
    const map = L.map(mapEl).setView([20.6736, -103.344], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    // Heat layer from points
    if (Array.isArray(points) && points.length > 0) {
      const heatData = points.map(p => [p[0], p[1], 0.6]);
      const heat = L.heatLayer(heatData, { radius: 18, blur: 20, maxZoom: 17 }).addTo(map);
      map.fitBounds(L.latLngBounds(points.map(p => L.latLng(p[0], p[1]))));
    }
    // Plot centroid markers and interactive zone circles
    if (Array.isArray(clusters)) {
      const label0 = (Array.isArray(colLabels) && colLabels.length > 0) ? colLabels[0] : 'dim0';
      const label1 = (Array.isArray(colLabels) && colLabels.length > 1) ? colLabels[1] : 'dim1';
      // Determine radius scale based on counts
      const maxCount = (Array.isArray(counts) && counts.length) ? Math.max.apply(null, counts) : 0;
      clusters.forEach((c, idx) => {
        if (Array.isArray(c) && c.length >= 2) {
          const centroidLat = c[0];
          const centroidLon = c[1];
          const count = counts[idx] || 0;
          // Base radius in meters scaled by density
          const radius = (count && maxCount) ? (300 + (count / maxCount) * 1200) : 300;
          // Zone circle
          const zone = L.circle([centroidLat, centroidLon], {
            radius: radius,
            color: '#7D5BFF',
            weight: 1.5,
            fillColor: '#7D5BFF',
            fillOpacity: 0.18
          }).addTo(map);
          // Centroid marker
          const marker = L.circleMarker([centroidLat, centroidLon], {
            radius: 7,
            color: '#7D5BFF',
            weight: 2,
            fillColor: '#7D5BFF',
            fillOpacity: 0.75
          }).addTo(map);
          const popupHtml = `
            <div style="min-width:220px">
              <strong>Zona ${idx+1}</strong><br/>
              Centroide (${label0}: ${centroidLat.toFixed(6)}, ${label1}: ${centroidLon.toFixed(6)})<br/>
              Puntos: ${count}
            </div>
          `;
          marker.bindPopup(popupHtml);
          zone.bindPopup(popupHtml);
        }
      });
    }
    // Highlight Top 5 centroids
    if (Array.isArray(top5)) {
      top5.forEach((t, idx) => {
        const c = t.centroid || [];
        if (Array.isArray(c) && c.length >= 2) {
          L.circleMarker([c[0], c[1]], {
            radius: 10,
            color: '#FF7D7D',
            weight: 3,
            fillColor: '#FF7D7D',
            fillOpacity: 0.8
          }).addTo(map).bindPopup(`Top ${idx+1} — ${t.count} puntos`);
        }
      });
    }
  }
</script>
@endsection