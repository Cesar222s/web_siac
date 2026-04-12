@extends('layouts.main')
@section('title','Sobre Nosotros')
@section('content')
<style>
    .about-hero {
        background: #fff;
        padding: 5rem 2rem;
        border-radius: 24px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-elev);
        text-align: center;
        margin-bottom: 3rem;
    }
    .about-hero h1 {
        margin: 0 0 1.2rem;
        font-size: clamp(2.5rem, 4vw, 3.5rem);
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.03em;
    }
    .about-grid {
        display: grid;
        gap: 1.5rem;
        margin-top: 3rem;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }
    .about-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .about-card:hover {
        transform: translateY(-8px);
        border-color: var(--primary);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
    }
    .about-card h2 {
        margin: 0 0 1rem;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    .about-card h2 svg {
        color: var(--primary);
        width: 24px;
        height: 24px;
    }
    .about-card p {
        margin: 0 0 1.5rem;
        font-size: 0.95rem;
        color: var(--text-dim);
        line-height: 1.6;
    }
    .pill-set {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: auto;
    }
    .pill {
        background: var(--surface-alt);
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-dim);
        letter-spacing: 0.05em;
    }
    @media (max-width: 768px) {
        .about-hero { padding: 3rem 1.5rem; }
    }
</style>
<div class="about-hero fade-in reveal">
    <div style="display:flex; justify-content:center; margin-bottom:2rem;">
        <img src="{{ asset('images/logo.svg') }}" alt="SIAC Logo" style="width:100px; height:100px;" class="float">
    </div>
    <h1>Nuestra Misión</h1>
    <p style="max-width:750px; margin: 0 auto; line-height: 1.7;">Transformar la seguridad vial mediante <strong>biometría de alta precisión</strong> y geolocalización, protegiendo al conductor con monitoreo activo e integración tecnológica avanzada.</p>
</div>
<div class="about-grid fade-in reveal" style="animation-delay:.12s;">
    <div class="about-card">
        <h2>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Seguimiento de Geolocalización
        </h2>
        <p>Mapeo GPS milimétrico que rastrea tu viaje en tiempo real y permite despachar alertas con tus coordenadas precisas cuando más importa.</p>
        <div class="pill-set">
            <span class="pill">Tracking GPS</span><span class="pill">Leaflet Map</span><span class="pill">Precisión</span>
        </div>
    </div>

    <div class="about-card reveal">
        <h2>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Guardián Biométrico
        </h2>
        <p>Monitoreo ininterrumpido a través de frecuencia cardíaca (HR/BPM) y movimiento corporal (acelerómetro) para descartar estrés, sueño o ritmo anormal.</p>
        <div class="pill-set">
            <span class="pill">Cardio Tracker</span><span class="pill">Acelerómetro</span>
        </div>
    </div>
    <div class="about-card reveal">
        <h2>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Wear OS Ecosystem
        </h2>
        <p>Sincronización total con tu smartwatch para recibir retroalimentación háptica en situaciones críticas.</p>
        <div class="pill-set">
            <span class="pill">Wear OS</span><span class="pill">Haptic Alerts</span>
        </div>
    </div>
    <div class="about-card reveal">
        <h2>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            PWA & App Nativa
        </h2>
        <p>Controles e historial de conducción accesibles desde cualquier lugar, con soporte offline y notificaciones push.</p>
        <div class="pill-set">
            <span class="pill">PWA</span><span class="pill">Android Native</span>
        </div>
    </div>

</div>
@endsection