@extends('layouts.main')
@section('title','Servicios')
@section('content')
    .services-hero {
        background: #fff;
        padding: 5rem 2.5rem;
        border-radius: 24px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-elev);
        text-align: center;
        margin-bottom: 2rem;
    }
    .services-hero h1 {
        margin: 0 0 1rem;
        font-size: clamp(2.5rem, 4vw, 3.5rem);
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.03em;
    }
    .services-hero p {
        max-width: 650px;
        margin: 0 auto;
        font-size: 1.1rem;
        color: var(--text-dim);
    }
    .service-grid {
        display: grid;
        gap: 2rem;
        margin-top: 4rem;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        justify-content: center;
    }
    .service-item {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 3rem 2rem;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .service-item:hover {
        transform: translateY(-8px);
        border-color: var(--primary);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
    }
    .service-icon-wrapper {
        width: 64px;
        height: 64px;
        background: var(--surface-alt);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        transition: 0.3s;
    }
    .service-item:hover .service-icon-wrapper {
        background: var(--primary);
    }
    .service-item:hover .service-icon-wrapper img {
        filter: brightness(0) invert(1);
    }
    .service-item h3 {
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 1rem;
        color: var(--text);
    }
    .service-item p {
        font-size: 1rem;
        color: var(--text-dim);
        line-height: 1.6;
        margin-bottom: 2rem;
        flex-grow: 1;
    }
    .service-features {
        list-style: none;
        padding: 0;
        margin: 0;
        border-top: 1px solid var(--border);
        padding-top: 1.5rem;
    }
    .service-features li {
        padding: 0.5rem 0;
        font-size: 0.9rem;
        color: var(--text-dim);
        display: flex;
        align-items: center;
        gap: 0.7rem;
        font-weight: 500;
    }
    .service-features li svg {
        color: var(--primary);
        width: 16px;
        height: 16px;
    }
    @media (max-width: 768px) {
        .services-hero { padding: 3rem 1.5rem; }
        .service-grid { grid-template-columns: 1fr; }
    }

<div class="services-hero fade-in reveal">
    <div style="display:flex; justify-content:center; margin-bottom:1.5rem;">
        <img src="{{ asset('images/road-scene.svg') }}" alt="Conducción" style="width:400px; height:auto; opacity:0.6; border-radius:20px;" loading="eager">
    </div>
    <h1>Ecosistema de Seguridad</h1>
    <p>Soluciones integrales de hardware y software que trabajan en conjunto para eliminar el error humano al volante.</p>
</div>

<div class="service-grid fade-in reveal" style="animation-delay:.1s;">
    <div class="service-item reveal">
        <div class="service-icon-wrapper">
            <img src="{{ asset('images/icon-realtime.svg') }}" alt="Biometría" style="width:48px; height:48px;" loading="lazy">
        </div>
        <h3>Guardia Biométrico</h3>
        <p>Monitoreo constante del ritmo cardíaco y acelerómetro para detectar signos de cansancio, microsueños y anomalías al volante.</p>
        <ul class="service-features">
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Detección de pulsaciones críticas</li>
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Análisis de inactividad corporal</li>
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Alertas sonoras y de vibración</li>
        </ul>
    </div>

    <div class="service-item reveal">
        <div class="service-icon-wrapper">
            <img src="{{ asset('images/icon-alert.svg') }}" alt="Wearable" style="width:48px; height:48px;" loading="lazy">
        </div>
        <h3>Alertas Wear OS</h3>
        <p>Extensión del ecosistema hacia tu muñeca para recibir alertas silenciosas mediante vibración en situaciones de alta criticidad.</p>
        <ul class="service-features">
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Vibración personalizada por riesgo</li>
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Notificaciones de proximidad</li>
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Sincronización con App Android</li>
        </ul>
    </div>

    <div class="service-item reveal">
        <div class="service-icon-wrapper">
            <img src="{{ asset('images/icon-iot.svg') }}" alt="SMS Alertas" style="width:48px; height:48px;" loading="lazy">
        </div>
        <h3>Red de Emergencia por SMS</h3>
        <p>Sistema resiliente que despacha mensajes de texto automáticos con tus coordenadas precisas cuando no puedas responder a una alerta.</p>
        <ul class="service-features">
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Despacho automático sin internet</li>
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Telemetría y coordenadas GPS</li>
            <li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Lista de contactos ilimitada</li>
        </ul>
    </div>
</div>

<div style="background:#fff; border:1px solid var(--border); border-radius:32px; padding:4rem 2rem; text-align:center; margin:5rem 0;">
    <h2 style="font-size:2.2rem; font-weight:800; margin-bottom:1.2rem;">¿Listo para una conducción inteligente?</h2>
    <p style="font-size:1.1rem; color:var(--text-dim); margin-bottom:2.5rem; max-width:600px; margin-left:auto; margin-right:auto;">Contáctanos para conocer planes personalizados para flotas o vehículos privados.</p>
    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('contact') }}" class="btn">Contactar ahora</a>
        <a href="{{ route('about') }}" class="btn btn-outline">Saber más</a>
    </div>
</div>
@endsection
