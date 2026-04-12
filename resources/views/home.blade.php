@extends('layouts.main')
@section('title','Inicio')
@section('content')
<style>
    .stats-grid {display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.8rem; margin:3rem 0;}
    .stat-card {background:linear-gradient(135deg,rgba(29,39,56,.85),rgba(38,52,74,.5)); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,.08); border-radius:18px; padding:1.8rem 1.5rem; text-align:center; position:relative; overflow:hidden; transition:.3s;}
    .stat-card:before {content:''; position:absolute; top:-50%; right:-30%; width:200px; height:200px; background:radial-gradient(circle,rgba(99,102,241,.2),transparent 70%); filter:blur(25px);}
    .stat-card .stat-number {font-size:2.8rem; font-weight:700; background:linear-gradient(120deg,var(--primary),var(--accent)); -webkit-background-clip:text; background-clip:text; color:transparent; margin:.5rem 0; position:relative; z-index:1;}
    .stat-card .stat-label {font-size:.85rem; color:var(--text-dim); letter-spacing:.08em; position:relative; z-index:1;}
    .stat-card:hover {transform:translateY(-4px); box-shadow:0 20px 45px -15px rgba(99,102,241,.4);}
    .tech-stack {display:flex; flex-wrap:wrap; gap:1rem; justify-content:center; margin:2.5rem 0;}
    .tech-badge {background:var(--surface-soft); padding:.65rem 1.2rem; border-radius:25px; font-size:.82rem; border:1px solid rgba(255,255,255,.06); display:flex; align-items:center; gap:.5rem; transition:.25s;}
    .tech-badge svg {width:18px; height:18px; color:var(--accent);}
    .tech-badge:hover {transform:scale(1.05); border-color:var(--accent); box-shadow:0 8px 20px -8px rgba(99,102,241,.5);}
    .cta-section {background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(16,185,129,.1)); border:1px solid rgba(99,102,241,.2); border-radius:24px; padding:3rem 2.5rem; text-align:center; margin:4rem 0; position:relative; overflow:hidden;}
    .cta-section:before {content:''; position:absolute; inset:0; background:radial-gradient(circle at 30% 50%, rgba(245,158,11,.15), transparent 60%); filter:blur(40px);}
    .cta-section h2 {font-size:2rem; margin-bottom:1rem; position:relative; z-index:1;}
    .cta-section p {font-size:1.05rem; color:var(--text-dim); margin-bottom:2rem; position:relative; z-index:1;}
    @media (max-width:780px){.stats-grid{grid-template-columns:repeat(2,1fr); gap:1.2rem;} .stat-card .stat-number{font-size:2.2rem;}}
</style>
<div class="hero fade-in glow reveal" style="position:relative; overflow:hidden;">
    <div style="position:absolute; top:20px; right:30px; width:180px; height:180px; background:radial-gradient(circle, rgba(99,102,241,.15) 0%, transparent 70%); animation:float 8s ease-in-out infinite;"></div>
    <div style="position:absolute; bottom:30px; left:40px; width:220px; height:220px; background:radial-gradient(circle, rgba(245,158,11,.12) 0%, transparent 70%); animation:float 10s ease-in-out infinite reverse;"></div>
    <div style="display:flex; justify-content:center; margin-bottom:1.5rem;">
        <img src="{{ asset('images/logo.svg') }}" alt="SIAC Logo" style="width:150px; height:150px; position:relative; z-index:1;" class="float" loading="eager">
    </div>
    <h1 style="margin:0 0 .85rem; position:relative; z-index:1;">SIAC</h1>
    <p style="position:relative; z-index:1; font-size:1.12rem;">Monitoreo de **frecuencia cardíaca (BPM)** y niveles de actividad desde tu reloj inteligente para detectar fatiga y enviar **alertas precisas con GPS** a tus contactos.</p>
    <div class="actions" style="position:relative; z-index:1;">
        <a href="{{ route('about') }}" class="btn btn-secondary">Conocer más</a>
        <a href="{{ route('register') }}" class="btn">Empezar ahora</a>
        <a href="{{ route('login') }}" class="btn-outline">Acceso</a>
    </div>
    <img src="{{ asset('images/hero-car.svg') }}" alt="Vehículo" style="position:absolute;bottom:-50px;right:-50px;opacity:.15;width:500px;height:auto;" class="float" loading="lazy">
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
<div class="fade-in" style="margin-top:2rem; background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(99,102,241,.12)); border:1px solid rgba(16,185,129,.3); padding:1.5rem; border-radius:24px;">
        <h2 style="margin:0 0 .8rem;">Mapa de Incidencias</h2>
        <p style="margin:0 0 1rem; font-size:.95rem; color:var(--text-dim);">Visualiza zonas con mayor densidad de eventos; útil para identificar horas pico de riesgo y optimizar alertas.</p>
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

<div class="cta-section fade-in" style="animation-delay:.25s;">
    <h2 style="background:linear-gradient(120deg,var(--primary),var(--accent)); -webkit-background-clip:text; background-clip:text; color:transparent;">¿Listo para conducir más seguro?</h2>
    <p>Únete a la plataforma que combina datos biométricos en tiempo real con geolocalización para proteger cada trayecto.</p>
    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap; position:relative; z-index:1;">
        <a href="{{ route('register') }}" class="btn">Crear cuenta gratuita</a>
        <a href="{{ route('contact') }}" class="btn btn-outline">Contáctanos</a>
    </div>
</div>
@endsection