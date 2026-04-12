@extends('layouts.main')
@section('title','Sobre Nosotros')
@section('content')
<style>
    .about-hero {background:linear-gradient(120deg,#141c2b,#1d2738 55%,#141c2b);padding:3rem 2rem;border-radius:32px;position:relative;overflow:hidden;box-shadow:0 24px 55px -20px rgba(0,0,0,.65);} 
    .about-hero:before {content:'';position:absolute;top:-35%;left:-20%;width:520px;height:520px;background:radial-gradient(circle at 60% 40%,rgba(45,232,255,.35),transparent 65%);filter:blur(20px);opacity:.45;} 
    .about-hero h1 {margin:0 0 1rem;font-size:clamp(2.3rem,3.1rem,3.4rem);background:linear-gradient(90deg,var(--primary),var(--secondary));-webkit-background-clip:text;background-clip:text;color:transparent;display:flex;align-items:center;gap:.9rem;} 
    .about-grid {display:grid;gap:2.2rem;margin-top:2.8rem;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));} 
    .about-card {background:linear-gradient(165deg,rgba(29,39,56,.9) 0%,rgba(38,52,74,.55) 60%);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:1.7rem 1.5rem;position:relative;overflow:hidden;box-shadow:0 18px 40px -16px rgba(0,0,0,.6);transition:.35s;} 
    .about-card:before {content:'';position:absolute;top:-30%;right:-25%;width:420px;height:420px;background:radial-gradient(circle at 55% 35%,rgba(93,255,245,.35),transparent 65%);opacity:.4;filter:blur(18px);} 
    .about-card h2 {margin:.2rem 0 .8rem;font-size:1.18rem;letter-spacing:.05em;color:var(--accent);display:flex;align-items:center;gap:.6rem;} 
    .about-card p {margin:.25rem 0 .65rem;font-size:.95rem;color:var(--text-dim);line-height:1.5;} 
    .pill-set {display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.4rem;} 
    .pill {background:var(--surface-soft);padding:.4rem .75rem;border-radius:30px;font-size:.65rem;letter-spacing:.12em;color:var(--text-dim);border:1px solid rgba(255,255,255,.06);} 
    .team-list {list-style:none;margin:.4rem 0 0;padding:0;display:grid;gap:.55rem;} 
    .team-list li {background:var(--surface-soft);padding:.55rem .75rem;border-radius:10px;font-size:.78rem;display:flex;align-items:center;gap:.55rem;border:1px solid rgba(255,255,255,.05);} 
    .team-list svg {color:var(--accent-alt);} 
    .about-card:hover {transform:translateY(-6px) scale(1.02);box-shadow:0 30px 70px -22px rgba(0,0,0,.7);} 
    @media (max-width:780px){.about-hero{padding:2.2rem 1.4rem;} .about-grid{gap:1.4rem;} }
</style>
<div class="about-hero fade-in reveal">
    <div style="display:flex; justify-content:center; margin-bottom:1.5rem;">
        <img src="{{ asset('images/logo.svg') }}" alt="SIAC Logo" style="width:100px; height:100px;" class="float">
    </div>
    <h1 style="justify-content:center;">Nuestra Misión</h1>
    <p style="max-width:860px;font-size:1.05rem;color:var(--text-dim);margin:0 auto;">SIAC nace para transformar la seguridad vial mediante **Inteligencia Artificial** y **IoT**. Nos enfocamos en proteger al conductor mediante el monitoreo activo de fatiga y la asistencia perimetral, integrando tecnología ponible (Wearables) para una respuesta inmediata.</p>
</div>
<div class="about-grid fade-in reveal" style="animation-delay:.12s;">
    <div class="about-card">
        <h2>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Asistencia 360°
        </h2>
        <p>Sensores IoT distribuidos estratégicamente para eliminar puntos ciegos y asistir en maniobras complejas.</p>
        <div class="pill-set">
            <span class="pill">IoT Sensors</span><span class="pill">Proximidad</span><span class="pill">Ultrasonic</span>
        </div>
    </div>
    <div class="about-card reveal">
        <h2>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Previsión de Colisión
        </h2>
        <p>Algoritmos predictivos que analizan la trayectoria y velocidad para emitir alertas críticas en milisegundos.</p>
        <div class="pill-set">
            <span class="pill">Low Latency</span><span class="pill">Machine Learning</span>
        </div>
    </div>
    <div class="about-card reveal">
        <h2>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Guardián de Fatiga
        </h2>
        <p>Monitoreo biométrico facial para identificar somnolencia, distracción o fatiga visual extrema.</p>
        <div class="pill-set">
            <span class="pill">Computer Vision</span><span class="pill">Driver Safety</span>
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
    <div class="about-card reveal">
        <h2>
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            Analítica de Riesgo
        </h2>
        <p>Dashboard avanzado para visualizar zonas calientes de accidentes y optimizar tus rutas diarias.</p>
        <div class="pill-set">
            <span class="pill">Heatmaps</span><span class="pill">Big Data</span>
        </div>
    </div>
</div>
@endsection