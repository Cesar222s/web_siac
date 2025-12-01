@extends('layouts.main')
@section('title','Inicio')
@section('content')
<style>
    .stats-grid {display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.8rem; margin:3rem 0;}
    .stat-card {background:linear-gradient(135deg,rgba(29,39,56,.85),rgba(38,52,74,.5)); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,.08); border-radius:18px; padding:1.8rem 1.5rem; text-align:center; position:relative; overflow:hidden; transition:.3s;}
    .stat-card:before {content:''; position:absolute; top:-50%; right:-30%; width:200px; height:200px; background:radial-gradient(circle,rgba(125,91,255,.2),transparent 70%); filter:blur(25px);}
    .stat-card .stat-number {font-size:2.8rem; font-weight:700; background:linear-gradient(120deg,var(--primary),var(--accent)); -webkit-background-clip:text; background-clip:text; color:transparent; margin:.5rem 0; position:relative; z-index:1;}
    .stat-card .stat-label {font-size:.85rem; color:var(--text-dim); letter-spacing:.08em; position:relative; z-index:1;}
    .stat-card:hover {transform:translateY(-4px); box-shadow:0 20px 45px -15px rgba(125,91,255,.4);}
    .tech-stack {display:flex; flex-wrap:wrap; gap:1rem; justify-content:center; margin:2.5rem 0;}
    .tech-badge {background:var(--surface-soft); padding:.65rem 1.2rem; border-radius:25px; font-size:.82rem; border:1px solid rgba(255,255,255,.06); display:flex; align-items:center; gap:.5rem; transition:.25s;}
    .tech-badge svg {width:18px; height:18px; color:var(--accent);}
    .tech-badge:hover {transform:scale(1.05); border-color:var(--accent); box-shadow:0 8px 20px -8px rgba(125,91,255,.5);}
    .cta-section {background:linear-gradient(135deg,rgba(125,91,255,.15),rgba(45,232,255,.1)); border:1px solid rgba(125,91,255,.2); border-radius:24px; padding:3rem 2.5rem; text-align:center; margin:4rem 0; position:relative; overflow:hidden;}
    .cta-section:before {content:''; position:absolute; inset:0; background:radial-gradient(circle at 30% 50%, rgba(255,95,170,.15), transparent 60%); filter:blur(40px);}
    .cta-section h2 {font-size:2rem; margin-bottom:1rem; position:relative; z-index:1;}
    .cta-section p {font-size:1.05rem; color:var(--text-dim); margin-bottom:2rem; position:relative; z-index:1;}
    @media (max-width:780px){.stats-grid{grid-template-columns:repeat(2,1fr); gap:1.2rem;} .stat-card .stat-number{font-size:2.2rem;}}
</style>
<div class="hero fade-in glow" style="position:relative;">
    <div style="position:absolute; top:20px; right:30px; width:180px; height:180px; background:radial-gradient(circle, rgba(125,91,255,.3), transparent 70%); filter:blur(40px); animation:float 8s ease-in-out infinite;"></div>
    <div style="position:absolute; bottom:30px; left:40px; width:220px; height:220px; background:radial-gradient(circle, rgba(255,95,170,.25), transparent 70%); filter:blur(45px); animation:float 10s ease-in-out infinite reverse;"></div>
    <div style="display:flex; justify-content:center; margin-bottom:1.5rem;">
        <img src="{{ asset('images/logo.svg') }}" alt="SIAC Logo" style="width:150px; height:150px; position:relative; z-index:1;" class="float">
    </div>
    <h1 style="margin:0 0 .85rem; position:relative; z-index:1;">SIAC – Sistema Inteligente de Asistencia en Conducción</h1>
    <p style="position:relative; z-index:1; font-size:1.12rem;">Tecnología diseñada para tu seguridad y confianza en el camino. Monitoreo inteligente en tiempo real.</p>
    <div class="actions" style="position:relative; z-index:1;">
        <a href="{{ route('about') }}" class="btn btn-secondary">Conoce más</a>
        <a href="{{ route('register') }}" class="btn">Comenzar ahora</a>
        <a href="{{ route('login') }}" class="btn-outline">Iniciar sesión</a>
    </div>
    <img src="{{ asset('images/hero-car.svg') }}" alt="SIAC Sistema" style="position:absolute;bottom:-50px;right:-50px;opacity:.15;width:500px;height:auto;" class="float">
</div>

<div class="stats-grid fade-in" style="animation-delay:.1s;">
    <div class="stat-card">
        <div class="stat-number">99.9%</div>
        <div class="stat-label">DISPONIBILIDAD</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">&lt;50ms</div>
        <div class="stat-label">TIEMPO DE RESPUESTA</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">24/7</div>
        <div class="stat-label">MONITOREO ACTIVO</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">5+</div>
        <div class="stat-label">SENSORES INTEGRADOS</div>
    </div>
</div>

<div class="fade-in" style="margin-top:2.2rem; background:linear-gradient(135deg,rgba(125,91,255,.18),rgba(45,232,255,.15)); border:1px solid rgba(125,91,255,.35); padding:2rem 1.5rem; border-radius:24px; position:relative; overflow:hidden;">
    <div style="position:absolute; inset:0; background:radial-gradient(circle at 75% 30%, rgba(255,95,170,.25), transparent 60%); filter:blur(40px); opacity:.6;"></div>
    <h2 style="margin:0 0 1rem; position:relative; z-index:1; font-size:1.5rem; display:flex; align-items:center; gap:.6rem;">
        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h7v7H3z"/><path d="M14 3h7v7h-7z"/><path d="M14 14h7v7h-7z"/><path d="M3 14h7v7H3z"/></svg>
        Informe Analítico de SIAC
    </h2>
    <p style="margin:0 0 1.2rem; font-size:.95rem; color:var(--text-dim); max-width:860px; position:relative; z-index:1;">Explora nuestro tablero de análisis para visualizar patrones de uso, tendencias de comportamiento y oportunidades de mejora en la asistencia de conducción. Este informe ayuda a tomar decisiones basadas en datos reales y a priorizar acciones de seguridad.</p>
    <ul style="list-style:none; padding:0; margin:0 0 1.2rem; display:grid; gap:.4rem; font-size:.8rem; position:relative; z-index:1;">
        <li style="display:flex; align-items:center; gap:.5rem;"><span style="color:var(--accent); font-weight:600;">•</span> Identifica horas pico de riesgo y fatiga.</li>
        <li style="display:flex; align-items:center; gap:.5rem;"><span style="color:var(--accent); font-weight:600;">•</span> Optimiza alertas según comportamiento histórico.</li>
        <li style="display:flex; align-items:center; gap:.5rem;"><span style="color:var(--accent); font-weight:600;">•</span> Prioriza mejoras para la experiencia del conductor.</li>
        <li style="display:flex; align-items:center; gap:.5rem;"><span style="color:var(--accent); font-weight:600;">•</span> Refuerza decisiones con métricas verificables.</li>
    </ul>
    <div style="display:flex; gap:1rem; flex-wrap:wrap; position:relative; z-index:1;">
        <a href="https://lookerstudio.google.com/reporting/d46e76c2-3be2-4391-8352-dffd7f19b978" target="_blank" rel="noopener" class="btn" style="display:inline-flex; align-items:center; gap:.5rem;">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7"/><path d="M10 21H3v-7"/><path d="M21 3l-7 7"/><path d="M3 21l7-7"/></svg>
            Ver Informe
        </a>
    </div>
    <div style="margin-top:1rem; font-size:.7rem; color:var(--text-dim); position:relative; z-index:1;">
        Nota: La visualización embebida está deshabilitada por el propietario del informe. Para habilitarla en el futuro activa "Allow embedding" en Looker Studio (Compartir → Administrar acceso → Configuración de inserción).
    </div>
</div>


{{-- Sección de clustering eliminada a solicitud del usuario --}}

<div style="margin-top:3.5rem; text-align:center;">
    <h2 style="font-size:clamp(1.6rem,2rem,2.2rem); margin-bottom:2.5rem; background:linear-gradient(120deg,var(--text),var(--text-dim)); -webkit-background-clip:text; background-clip:text; color:transparent;">Características principales</h2>
</div>

<div class="grid fade-in" style="animation-delay:.15s;">
    <div class="card tilt-hover">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-monitor.svg') }}" alt="Monitoreo" style="width:80px; height:80px;">
        </div>
        <h2>Monitoreo inteligente</h2>
        <p>Seguimiento continuo del estado del conductor y entorno vehicular para anticipar riesgos y mejorar la experiencia de conducción.</p>
    </div>
    <div class="card tilt-hover">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-security.svg') }}" alt="Seguridad" style="width:80px; height:80px;">
        </div>
        <h2>Seguridad avanzada</h2>
        <p>Arquitectura robusta enfocada en confiabilidad, protección de datos y cumplimiento de estándares de seguridad automotriz.</p>
    </div>
    <div class="card tilt-hover">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-alert.svg') }}" alt="Alertas" style="width:80px; height:80px;">
        </div>
        <h2>Alertas preventivas</h2>
        <p>Notificaciones oportunas y contextuales que ayudan a evitar incidentes antes de que sucedan.</p>
    </div>
    <div class="card tilt-hover">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-sensor.svg') }}" alt="Sensores" style="width:80px; height:80px;">
        </div>
        <h2>Integración de sensores</h2>
        <p>Datos unificados provenientes de múltiples sensores para una visión clara del estado vehicular y contexto de conducción.</p>
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
        <div class="tech-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            IoT Sensors
        </div>
    </div>
</div>

<div class="cta-section fade-in" style="animation-delay:.25s;">
    <h2 style="background:linear-gradient(120deg,var(--primary),var(--accent)); -webkit-background-clip:text; background-clip:text; color:transparent;">¿Listo para conducir más seguro?</h2>
    <p>Únete a la plataforma que combina datos en tiempo real con inteligencia artificial para proteger cada trayecto.</p>
    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap; position:relative; z-index:1;">
        <a href="{{ route('register') }}" class="btn">Crear cuenta gratuita</a>
        <a href="{{ route('contact') }}" class="btn btn-outline">Contáctanos</a>
    </div>
</div>
@endsection