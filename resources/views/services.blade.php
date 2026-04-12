@extends('layouts.main')
@section('title','Servicios')
@section('content')
<style>
    .services-hero {background:linear-gradient(135deg,#141c2b,#1d2738 60%,#141c2b);padding:3.5rem 2rem;border-radius:32px;position:relative;overflow:hidden;box-shadow:0 24px 55px -20px rgba(0,0,0,.65);text-align:center;}
    .services-hero:before {content:'';position:absolute;top:-40%;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle at 50% 50%,rgba(99,102,241,.15),transparent 65%);opacity:.5;}
    .services-hero h1 {margin:0 0 1rem;font-size:clamp(2.4rem,3.2rem,3.6rem);background:linear-gradient(90deg,var(--primary),var(--accent));-webkit-background-clip:text;background-clip:text;color:transparent;position:relative;z-index:1;}
    .services-hero p {max-width:720px;margin:0 auto;font-size:1.1rem;color:var(--text-dim);position:relative;z-index:1;}
    .service-grid {display:grid;gap:2.5rem;margin-top:3.5rem;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));}
    .service-item {background:linear-gradient(145deg,rgba(29,39,56,.85),rgba(38,52,74,.5));backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.07);border-radius:24px;padding:2.2rem 2rem;position:relative;overflow:hidden;transition:.35s;}
    .service-item:before {content:'';position:absolute;top:-35%;right:-30%;width:320px;height:320px;background:radial-gradient(circle,rgba(245,158,11,.1),transparent 70%);opacity:.4;}
    .service-icon {width:64px;height:64px;background:linear-gradient(135deg,rgba(99,102,241,.2),rgba(245,158,11,.15));border:1px solid rgba(99,102,241,.3);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;position:relative;z-index:1;}
    .service-icon svg {width:32px;height:32px;color:var(--accent);}
    .service-item h3 {font-size:1.4rem;margin:0 0 1rem;color:var(--text);position:relative;z-index:1;}
    .service-item p {font-size:.95rem;color:var(--text-dim);line-height:1.6;margin-bottom:1.2rem;position:relative;z-index:1;}
    .service-features {list-style:none;padding:0;margin:1.2rem 0 0;position:relative;z-index:1;}
    .service-features li {padding:.5rem 0;font-size:.88rem;color:var(--text-dim);display:flex;align-items:center;gap:.6rem;}
    .service-features li:before {content:'✓';color:var(--accent);font-weight:bold;font-size:1.1rem;}
    .service-item:hover {transform:translateY(-8px);box-shadow:0 28px 60px -20px rgba(99,102,241,.45);border-color:var(--accent);}
    @media (max-width:780px){.services-hero{padding:2.5rem 1.5rem;} .service-grid{gap:1.8rem;}}
</style>

<div class="services-hero fade-in reveal">
    <div style="display:flex; justify-content:center; margin-bottom:1.5rem;">
        <img src="{{ asset('images/road-scene.svg') }}" alt="Conducción" style="width:400px; height:auto; opacity:0.6; border-radius:20px;" loading="eager">
    </div>
    <h1>Ecosistema de Seguridad</h1>
    <p>Soluciones integrales de hardware y software que trabajan en conjunto para eliminar el error humano al volante.</p>
</div>

<div class="service-grid fade-in reveal" style="animation-delay:.1s;">
    <div class="service-item reveal">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-realtime.svg') }}" alt="Biometría" style="width:70px; height:70px;" loading="lazy">
        </div>
        <h3>Guardia Biométrico</h3>
        <p>Monitoreo constante del ritmo cardíaco y acelerómetro para detectar signos de cansancio, microsueños y anomalías al volante.</p>
        <ul class="service-features">
            <li>Detección de latidos irregulares</li>
            <li>Análisis de inactividad corporal</li>
            <li>Alertas sonoras y de vibración</li>
            <li>Registro de frecuencia cardíaca crítica</li>
        </ul>
    </div>

    <div class="service-item reveal">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-alert.svg') }}" alt="Wearable" style="width:70px; height:70px;" loading="lazy">
        </div>
        <h3>Alertas Wear OS</h3>
        <p>Extensión del ecosistema hacia tu muñeca para recibir alertas silenciosas mediante vibración en situaciones de alta criticidad.</p>
        <ul class="service-features">
            <li>Vibración personalizada por riesgo</li>
            <li>Notificaciones de proximidad</li>
            <li>Sincronización con App Android</li>
            <li>Interfaz optimizada para reloj</li>
        </ul>
    </div>

    <div class="service-item reveal">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-iot.svg') }}" alt="SMS Alertas" style="width:70px; height:70px;" loading="lazy">
        </div>
        <h3>Red de Emergencia por SMS</h3>
        <p>Sistema resiliente que despacha mensajes de texto automáticos con tus coordenadas precisas cuando no puedas responder a una alerta.</p>
        <ul class="service-features">
            <li>Despacho automático sin internet activo</li>
            <li>Datos de telemetría y coordenadas GPS</li>
            <li>Lista de contactos ilimitada</li>
            <li>Confirmación de llegada segura</li>
        </ul>
    </div>

    <div class="service-item reveal">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-support.svg') }}" alt="Analítica" style="width:70px; height:70px;" loading="lazy">
        </div>
        <h3>Analítica Predictiva</h3>
        <p>Uso de Big Data para identificar patrones de tráfico y zonas críticas, permitiendo una planificación de rutas más segura.</p>
        <ul class="service-features">
            <li>Clustering de zonas de riesgo</li>
            <li>Reportes periódicos de desempeño</li>
            <li>Optimización de rutas por seguridad</li>
            <li>Exportación de datos analíticos</li>
        </ul>
    </div>
</div>

<div style="background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(245,158,11,.08));border:1px solid rgba(99,102,241,.18);border-radius:24px;padding:3rem 2.5rem;text-align:center;margin:4rem 0;position:relative;overflow:hidden;" class="fade-in reveal">
    <div style="position:absolute;inset:0;background:radial-gradient(circle at 70% 30%,rgba(16,185,129,.05),transparent 60%);"></div>
    <h2 style="font-size:2rem;margin-bottom:1.2rem;background:linear-gradient(120deg,var(--primary),var(--accent));-webkit-background-clip:text;background-clip:text;color:transparent;position:relative;z-index:1;">¿Listo para una conducción inteligente?</h2>
    <p style="font-size:1.05rem;color:var(--text-dim);margin-bottom:2rem;position:relative;z-index:1;">Contáctanos para conocer planes personalizados y soluciones a medida para tu flota o vehículo.</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;position:relative;z-index:1;">
        <a href="{{ route('contact') }}" class="btn">Contactar ahora</a>
        <a href="{{ route('about') }}" class="btn btn-outline">Sobre nosotros</a>
    </div>
</div>
@endsection
