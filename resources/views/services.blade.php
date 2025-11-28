@extends('layouts.main')
@section('title','Servicios')
@section('content')
<style>
    .services-hero {background:linear-gradient(135deg,#141c2b,#1d2738 60%,#141c2b);padding:3.5rem 2rem;border-radius:32px;position:relative;overflow:hidden;box-shadow:0 24px 55px -20px rgba(0,0,0,.65);text-align:center;}
    .services-hero:before {content:'';position:absolute;top:-40%;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle at 50% 50%,rgba(125,91,255,.3),transparent 65%);filter:blur(40px);opacity:.5;}
    .services-hero h1 {margin:0 0 1rem;font-size:clamp(2.4rem,3.2rem,3.6rem);background:linear-gradient(90deg,var(--primary),var(--accent));-webkit-background-clip:text;background-clip:text;color:transparent;position:relative;z-index:1;}
    .services-hero p {max-width:720px;margin:0 auto;font-size:1.1rem;color:var(--text-dim);position:relative;z-index:1;}
    .service-grid {display:grid;gap:2.5rem;margin-top:3.5rem;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));}
    .service-item {background:linear-gradient(145deg,rgba(29,39,56,.85),rgba(38,52,74,.5));backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.07);border-radius:24px;padding:2.2rem 2rem;position:relative;overflow:hidden;transition:.35s;}
    .service-item:before {content:'';position:absolute;top:-35%;right:-30%;width:320px;height:320px;background:radial-gradient(circle,rgba(45,232,255,.2),transparent 70%);filter:blur(30px);opacity:.4;}
    .service-icon {width:64px;height:64px;background:linear-gradient(135deg,rgba(125,91,255,.2),rgba(45,232,255,.15));border:1px solid rgba(125,91,255,.3);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;position:relative;z-index:1;}
    .service-icon svg {width:32px;height:32px;color:var(--accent);}
    .service-item h3 {font-size:1.4rem;margin:0 0 1rem;color:var(--text);position:relative;z-index:1;}
    .service-item p {font-size:.95rem;color:var(--text-dim);line-height:1.6;margin-bottom:1.2rem;position:relative;z-index:1;}
    .service-features {list-style:none;padding:0;margin:1.2rem 0 0;position:relative;z-index:1;}
    .service-features li {padding:.5rem 0;font-size:.88rem;color:var(--text-dim);display:flex;align-items:center;gap:.6rem;}
    .service-features li:before {content:'✓';color:var(--accent);font-weight:bold;font-size:1.1rem;}
    .service-item:hover {transform:translateY(-8px);box-shadow:0 28px 60px -20px rgba(125,91,255,.45);border-color:var(--accent);}
    @media (max-width:780px){.services-hero{padding:2.5rem 1.5rem;} .service-grid{gap:1.8rem;}}
</style>

<div class="services-hero fade-in">
    <div style="display:flex; justify-content:center; margin-bottom:1.5rem;">
        <img src="{{ asset('images/road-scene.svg') }}" alt="Conducción" style="width:400px; height:auto; opacity:0.6; border-radius:20px;">
    </div>
    <h1>Nuestros Servicios</h1>
    <p>Soluciones tecnológicas avanzadas para mejorar la seguridad, eficiencia y experiencia de conducción en cada trayecto.</p>
</div>

<div class="service-grid fade-in" style="animation-delay:.1s;">
    <div class="service-item">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-realtime.svg') }}" alt="Tiempo Real" style="width:70px; height:70px;">
        </div>
        <h3>Monitoreo en Tiempo Real</h3>
        <p>Seguimiento continuo de parámetros vehiculares y del conductor con actualizaciones instantáneas y visualización de datos en dashboard intuitivo.</p>
        <ul class="service-features">
            <li>Velocidad y aceleración en tiempo real</li>
            <li>Alertas automáticas configurables</li>
            <li>Historial completo de eventos</li>
            <li>Acceso desde dispositivos móviles</li>
        </ul>
    </div>

    <div class="service-item">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-alert.svg') }}" alt="Alertas" style="width:70px; height:70px;">
        </div>
        <h3>Sistema de Alertas</h3>
        <p>Notificaciones inteligentes vía email, SMS y push para mantener al conductor informado sobre condiciones críticas antes de que se conviertan en problemas.</p>
        <ul class="service-features">
            <li>Múltiples canales de notificación</li>
            <li>Priorización por severidad</li>
            <li>Configuración personalizada</li>
            <li>Respuesta en menos de 50ms</li>
        </ul>
    </div>

    <div class="service-item">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-iot.svg') }}" alt="IoT" style="width:70px; height:70px;">
        </div>
        <h3>Integración IoT</h3>
        <p>Conectividad con múltiples dispositivos y sensores vehiculares para una visión completa del ecosistema de conducción y mantenimiento predictivo.</p>
        <ul class="service-features">
            <li>Compatibilidad con sensores vehiculares</li>
            <li>Conectividad Bluetooth y WiFi</li>
            <li>API REST documentada</li>
            <li>Sincronización automática de datos</li>
        </ul>
    </div>

    <div class="service-item">
        <div style="display:flex; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/icon-support.svg') }}" alt="Soporte" style="width:70px; height:70px;">
        </div>
        <h3>Soporte Técnico</h3>
        <p>Asistencia profesional continua para garantizar el funcionamiento óptimo del sistema, resolución rápida de incidencias y capacitación de usuarios.</p>
        <ul class="service-features">
            <li>Soporte 24/7 por email</li>
            <li>Documentación técnica completa</li>
            <li>Actualizaciones de software incluidas</li>
            <li>Tiempo de respuesta < 2 horas</li>
        </ul>
    </div>
</div>

<div style="background:linear-gradient(135deg,rgba(125,91,255,.12),rgba(45,232,255,.08));border:1px solid rgba(125,91,255,.18);border-radius:24px;padding:3rem 2.5rem;text-align:center;margin:4rem 0;position:relative;overflow:hidden;" class="fade-in">
    <div style="position:absolute;inset:0;background:radial-gradient(circle at 70% 30%,rgba(255,95,170,.12),transparent 60%);filter:blur(35px);"></div>
    <h2 style="font-size:2rem;margin-bottom:1.2rem;background:linear-gradient(120deg,var(--primary),var(--accent));-webkit-background-clip:text;background-clip:text;color:transparent;position:relative;z-index:1;">¿Necesitas más información?</h2>
    <p style="font-size:1.05rem;color:var(--text-dim);margin-bottom:2rem;position:relative;z-index:1;">Contáctanos para conocer planes personalizados y soluciones a medida para tu flota o vehículo.</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;position:relative;z-index:1;">
        <a href="{{ route('contact') }}" class="btn">Contactar ahora</a>
        <a href="{{ route('about') }}" class="btn btn-outline">Sobre nosotros</a>
    </div>
</div>
@endsection
