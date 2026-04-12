@extends('layouts.main')
@section('title','Inicio')
@section('content')
<style>
    .stats-grid {display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.5rem; margin:4rem 0;}
    .stat-card {background:#fff; border:1px solid var(--border); border-radius:18px; padding:2rem; text-align:center; transition:var(--transition); box-shadow:var(--shadow-elev);}
    .stat-card .stat-number {font-size:2.8rem; font-weight:800; color:var(--primary); margin:0.5rem 0;}
    .stat-card .stat-label {font-size:0.75rem; font-weight:700; color:var(--text-dim); text-transform:uppercase; letter-spacing:0.1em;}
    .stat-card:hover {transform:translateY(-8px); border-color:var(--primary); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);}
    
    .tech-stack {display:flex; flex-wrap:wrap; gap:0.8rem; justify-content:center; margin:3rem 0;}
    .tech-badge {background:#fff; padding:.6rem 1.2rem; border-radius:14px; font-size:.85rem; font-weight:600; border:1px solid var(--border); display:flex; align-items:center; gap:.6rem; transition:.2s; color:var(--text-dim);}
    .tech-badge svg {width:18px; height:18px; color:var(--primary);}
    .tech-badge:hover {border-color:var(--primary); color:var(--text); transform:translateY(-2px);}
    
    .cta-section {background:var(--primary); border-radius:24px; padding:4rem 2rem; text-align:center; margin:5rem 0; color:#fff;}
    .cta-section h2 {font-size:2.2rem; font-weight:800; margin-bottom:1rem; color:#fff;}
    .cta-section p {font-size:1.1rem; color:rgba(255,255,255,0.9); margin-bottom:2.5rem; max-width:600px; margin-left:auto; margin-right:auto;}
    @media (max-width:780px){.stats-grid{grid-template-columns:1fr;}}
</style>
<div class="hero fade-in reveal">
    <div style="display:flex; justify-content:center; margin-bottom:2rem;">
        <img src="{{ asset('images/logo.svg') }}" alt="SIAC Logo" style="width:120px; height:120px;" class="float" loading="eager">
    </div>
    <h1>Asistencia Inteligente <br><span style="color:var(--primary)">en Conducción</span></h1>
    <p>Monitoreo de frecuencia cardíaca y actividad en tiempo real para un viaje más seguro y protegido.</p>
    <div class="actions" style="justify-content:center;">
        <a href="{{ route('register') }}" class="btn">Empezar ahora</a>
        <a href="{{ route('about') }}" class="btn btn-outline">Saber más</a>
    </div>
</div>

<div class="stats-grid fade-in reveal" style="animation-delay:.1s;">
    <div class="stat-card">
        <div class="stat-number">99.9%</div>
        <div class="stat-label">UPTIME DEL SISTEMA</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">&lt;45ms</div>
        <div class="stat-label">LATENCIA DE ALERTAS</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">GPS</div>
        <div class="stat-label">MAPEO DE RIESGO</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">BPM</div>
        <div class="stat-label">MONITOREO CARDÍACO</div>
    </div>
</div>



{{-- Mapa interactivo de puntos (Leaflet) --}}
@if(isset($mapPoints) && count($mapPoints) > 0)
<div class="fade-in" style="margin-top:4rem; background:#fff; border:1px solid var(--border); padding:2rem; border-radius:24px; box-shadow:var(--shadow-elev);">
        <h2 style="margin:0 0 1rem; font-weight:800;">Mapa de Riesgos</h2>
        <p style="margin:0 0 2rem; font-size:1rem; color:var(--text-dim);">Visualización de zonas con mayor frecuencia de alertas detectadas en tiempo real.</p>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4QjV7g2W9SgZCkPWqsK0p12uglfL74E8JQvM2x3GHo=" crossorigin=""/>
        <div id="siac-map" style="height:460px; border-radius:16px; border:1px solid rgba(99,102,241,.35);"></div>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCfXDiFk9G2zGx2u3VZ8GZp3ZaLx3bVQxZ9M4x3E=" crossorigin=""></script>
        <script>
            const points = @json($mapPoints);
            const center = points.length ? points[0] : [20.6736, -103.344];
            const map = L.map('siac-map').setView(center, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            // Render puntos como círculos semi-transparente (heat-like)
            points.forEach(([lat, lon]) => {
                L.circle([lat, lon], { radius: 75, color: '#6366F1', fillColor: '#6366F1', fillOpacity: 0.25, weight: 1 }).addTo(map);
            });
        </script>
</div>
@endif

<div style="margin-top:3.5rem; text-align:center;">
    <h2 style="font-size:clamp(1.6rem,2rem,2.2rem); margin-bottom:2.5rem; background:linear-gradient(120deg,var(--text),var(--text-dim)); -webkit-background-clip:text; background-clip:text; color:transparent;">Características principales</h2>
</div>

<div class="grid fade-in reveal" style="animation-delay:.15s;">
    <div class="card tilt-hover">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-monitor.svg') }}" alt="Monitoreo" style="width:70px; height:70px;" loading="lazy">
        </div>
        <h2>Análisis Cardíaco</h2>
        <p>Monitoriza tus pulsaciones por minuto (BPM) de forma continua con Wear OS para detectar signos tempranos de agotamiento extremo o sueño al volante.</p>
    </div>
    <div class="card tilt-hover">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-security.svg') }}" alt="Acelerómetro" style="width:70px; height:70px;" loading="lazy">
        </div>
        <h2>Detección de Relajación Inusual</h2>
        <p>Detecta descensos bruscos en tu nivel de actividad corporal mientras conduces. Si los signos vitales disminuyen demasiado, se dispara un aviso.</p>
    </div>
    <div class="card tilt-hover">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-alert.svg') }}" alt="Alertas Automáticas" style="width:70px; height:70px;" loading="lazy">
        </div>
        <h2>Alertas a tus Contactos</h2>
        <p>Si sufres un incidente o no puedes responder, la aplicación enviará directamente mensajes de emergencia a tus personas de confianza (SMS).</p>
    </div>
    <div class="card tilt-hover">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-sensor.svg') }}" alt="Mapeo GPS" style="width:70px; height:70px;" loading="lazy">
        </div>
        <h2>Historial y Geolocalización</h2>
        <p>Conoce exactamente dónde ocurrieron las alertas de fatiga pasada y obtén registros con fechas, horas y las coordenadas GPS exactas.</p>
    </div>
</div>

<div style="margin-top:4rem; text-align:center;">
    <h2 style="font-size:clamp(1.6rem,2rem,2.2rem); margin-bottom:1.8rem; background:linear-gradient(120deg,var(--text),var(--text-dim)); -webkit-background-clip:text; background-clip:text; color:transparent;">Tecnologías empleadas</h2>
    <div class="tech-stack fade-in" style="animation-delay:.2s;">
        <div class="tech-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            Laravel 10
        </div>
        <div class="tech-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            MongoDB Atlas
        </div>
        <div class="tech-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            PHP 8.1
        </div>
        <div class="tech-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Android Studio
        </div>
        <div class="tech-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3 9-8 10-5-1-8-5-8-10V6l8-4z"/></svg>
            TLS/SMTP
        </div>
    </div>
</div>

<div class="cta-section fade-in">
    <h2>¿Listo para conducir seguro?</h2>
    <p>Únete a la plataforma que protege y monitorea cada trayecto de forma inteligente con biometría avanzada.</p>
    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('register') }}" class="btn" style="background:#fff; color:var(--primary);">Crear cuenta</a>
        <a href="{{ route('contact') }}" class="btn btn-outline" style="border-color:rgba(255,255,255,0.4); color:#fff; background:transparent;">Contáctanos</a>
    </div>
</div>
@endsection