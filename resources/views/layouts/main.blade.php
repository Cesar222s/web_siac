<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="SIAC - Sistema Inteligente de Asistencia en Conducción. Monitoreo en tiempo real, seguridad avanzada y alertas inteligentes.">
    <meta name="keywords" content="SIAC, asistencia conducción, seguridad vehicular, IoT, monitoreo tiempo real">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    <title>SIAC - @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    {{-- Carga dinámica: si existe manifest de Vite usar assets compilados, si no usar fallback ligero --}}
    @php($manifestPath = public_path('build/manifest.json'))
    @if (file_exists($manifestPath))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @endif
    <style>
        /* Nueva paleta y escala tipográfica completa */
        :root {
            --bg:#070b13; --surface:#141c2b; --surface-alt:#1d2738; --surface-soft:#26344a;
            --border:#31455f; --text:#f5f8fc; --text-dim:#acb9c9; --primary:#7d5bff; --primary-alt:#a28dff;
            --secondary:#ff5faa; --secondary-alt:#ff85c0; --accent:#2de8ff; --accent-alt:#5efff5;
            --danger:#ff4d4d; --success:#3bdc84; --focus:#7d5bff80;
            --font-base:17px; --font-small:0.88rem; --font-btn:0.92rem; --radius:20px; --transition:.28s ease;
            --shadow-elev:0 16px 40px -18px rgba(0,0,0,.6);
        }
        /* Patrón de fondo animado */
        body:before {
            content:''; position:fixed; inset:0; z-index:-1;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(125,91,255,.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255,95,170,.12) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(45,232,255,.08) 0%, transparent 50%);
            animation:bgPulse 20s ease-in-out infinite alternate;
        }
        @keyframes bgPulse {
            0% { opacity:.3; transform:scale(1); }
            100% { opacity:.6; transform:scale(1.15); }
        }
        /* Patrón de cuadrícula sutil */
        body:after {
            content:''; position:fixed; inset:0; z-index:-1;
            background-image:
                linear-gradient(rgba(125,91,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(125,91,255,.03) 1px, transparent 1px);
            background-size:50px 50px;
            opacity:.4;
        }
        /* Transiciones base y utilidades */
        .ease-fast{transition:all .18s cubic-bezier(.4,.0,.2,1);} .ease{transition:all .28s cubic-bezier(.4,.0,.2,1);} .ease-slow{transition:all .45s cubic-bezier(.25,.8,.25,1);}
        .fade-slide{opacity:0; transform:translateY(14px); animation:fadeSlide .65s cubic-bezier(.4,.0,.2,1) forwards;} @keyframes fadeSlide{to{opacity:1; transform:translateY(0);}}
        .fade-in{opacity:0; animation:fadeIn .8s cubic-bezier(.4,.0,.2,1) forwards;} @keyframes fadeIn{to{opacity:1;}}
        .stagger > *{opacity:0; transform:translateY(16px);} .stagger.ready > *{animation:fadeStagger .7s cubic-bezier(.4,.0,.2,1) forwards;} .stagger.ready > *:nth-child(2){animation-delay:.1s;} .stagger.ready > *:nth-child(3){animation-delay:.2s;} .stagger.ready > *:nth-child(4){animation-delay:.3s;} @keyframes fadeStagger{to{opacity:1; transform:translateY(0);}}
        .tilt-hover{transform-style:preserve-3d;} .tilt-hover:hover{transform:perspective(900px) rotateX(4deg) rotateY(-4deg);} 
        .float{animation:float 6s ease-in-out infinite;} @keyframes float{0%,100%{transform:translateY(0);} 50%{transform:translateY(-12px);}}
        .glow{position:relative;} .glow:before{content:'';position:absolute;inset:-4px;background:linear-gradient(45deg,var(--primary),var(--secondary),var(--accent));border-radius:inherit;filter:blur(18px);opacity:0;transition:opacity .6s ease;z-index:-1;} .glow:hover:before{opacity:.35;} 
        *{box-sizing:border-box;} body{margin:0;font-family:'Roboto',sans-serif;background:var(--bg);color:var(--text);line-height:1.62;font-size:var(--font-base);letter-spacing:.15px;}
        h1,h2,h3{font-weight:600;letter-spacing:.02em;line-height:1.12;margin:0 0 .65rem;} h1{font-size:clamp(2.4rem,3.4rem,3.6rem);} h2{font-size:clamp(1.55rem,1.85rem,2rem);} h3{font-size:clamp(1.15rem,1.25rem,1.35rem);} p{margin:.4rem 0 1.05rem;font-size:1.02rem;}
        header{display:flex;align-items:center;justify-content:space-between;padding:1rem 2rem;background:linear-gradient(135deg,rgba(20,30,48,.95),rgba(24,42,66,.95));border-bottom:1px solid var(--border);position:sticky;top:0;z-index:60;backdrop-filter:blur(12px) saturate(180%);box-shadow:0 4px 24px rgba(0,0,0,.2);} .brand{display:flex;align-items:center;gap:.7rem;font-size:1.3rem;font-weight:700;letter-spacing:.08em;background:linear-gradient(120deg,var(--accent),var(--primary));-webkit-background-clip:text;background-clip:text;color:transparent;text-decoration:none;transition:transform .3s ease;} .brand:hover{transform:scale(1.05);}
        nav{display:flex;align-items:center;gap:.55rem;flex-wrap:wrap;} nav a{color:var(--text-dim);text-decoration:none;padding:.6rem .95rem;font-weight:500;border-radius:12px;position:relative;overflow:hidden;transition:color .25s ease, background .35s ease;} nav a:before{content:'';position:absolute;left:.95rem;right:.95rem;bottom:.45rem;height:2px;background:linear-gradient(90deg,var(--primary),var(--secondary));transform:scaleX(0);transform-origin:left;transition:transform .45s cubic-bezier(.4,.0,.2,1);} nav a:hover:before,nav a.active:before{transform:scaleX(1);} nav a.active,nav a:hover{color:var(--text);background:var(--surface-alt);}
        main{max-width:1280px;margin:2.6rem auto 4.2rem;padding:0 1.8rem;} .hero{background:linear-gradient(120deg,#162032,#1f2c45 60%,#162032);padding:3.4rem 1.8rem;border-radius:36px;position:relative;overflow:hidden;box-shadow:0 24px 55px -20px rgba(0,0,0,.65);} .hero h1{background:linear-gradient(90deg,var(--primary),var(--secondary));-webkit-background-clip:text;background-clip:text;color:transparent;} .hero p{max-width:780px;font-size:1.08rem;color:var(--text-dim);} .actions{margin-top:2rem;display:flex;justify-content:flex-start;flex-wrap:wrap;gap:1.1rem;}
        .btn{background:linear-gradient(95deg,var(--primary),var(--primary-alt));color:#fff;border:none;padding:1rem 1.65rem;border-radius:16px;font-weight:600;font-size:var(--font-btn);cursor:pointer;letter-spacing:.6px;transition:all .4s cubic-bezier(.4,.0,.2,1);box-shadow:0 14px 34px -12px rgba(125,91,255,.55), 0 0 0 0 rgba(125,91,255,.4);text-decoration:none;position:relative;overflow:hidden;} .btn:before{content:'';position:absolute;top:50%;left:50%;width:0;height:0;border-radius:50%;background:rgba(255,255,255,.3);transform:translate(-50%,-50%);transition:width .6s ease, height .6s ease;} .btn:hover:before{width:300px;height:300px;} .btn:hover{filter:brightness(1.12);transform:translateY(-4px) scale(1.02);box-shadow:0 20px 42px -14px rgba(125,91,255,.7), 0 0 0 3px rgba(125,91,255,.15);} .btn:active{transform:translateY(0) scale(.98);} .btn-secondary{background:linear-gradient(95deg,var(--secondary),var(--secondary-alt));box-shadow:0 14px 34px -12px rgba(255,95,170,.55), 0 0 0 0 rgba(255,95,170,.4);} .btn-secondary:hover{box-shadow:0 20px 42px -14px rgba(255,95,170,.7), 0 0 0 3px rgba(255,95,170,.15);} .btn-outline{background:rgba(45,232,255,.08);border:2px solid var(--accent);color:var(--accent);padding:.95rem 1.5rem;border-radius:16px;font-weight:600;font-size:var(--font-btn);box-shadow:0 0 0 0 rgba(45,232,255,.3);transition:all .35s cubic-bezier(.4,.0,.2,1);text-decoration:none;display:inline-block;} .btn-outline:hover{background:var(--accent);color:#06202b;box-shadow:0 8px 26px -8px rgba(45,232,255,.65), 0 0 0 3px rgba(45,232,255,.2);transform:translateY(-2px);}
        .grid{display:grid;gap:2.2rem;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));margin-top:3rem;} .card{background:linear-gradient(165deg,rgba(29,39,56,.92) 0%,rgba(38,52,74,.58) 55%);backdrop-filter:blur(20px) saturate(180%);border:1px solid rgba(255,255,255,.08);border-radius:var(--radius);padding:2rem 1.85rem;position:relative;box-shadow:var(--shadow-elev), 0 0 0 1px rgba(125,91,255,.05);overflow:hidden;transition:all .5s cubic-bezier(.4,.0,.2,1);} .card:before{content:'';position:absolute;top:-40%;right:-30%;width:520px;height:520px;background:radial-gradient(circle at 50% 50%,rgba(125,91,255,.25),transparent 70%);opacity:.3;filter:blur(28px);transition:opacity .5s ease, transform .5s ease;} .card:after{content:'';position:absolute;inset:0;background:linear-gradient(135deg, transparent 0%, rgba(45,232,255,.03) 100%);opacity:0;transition:opacity .5s ease;pointer-events:none;} .card h2{margin:.25rem 0 1rem;font-size:1.28rem;letter-spacing:.05em;background:linear-gradient(120deg,var(--accent),var(--primary-alt));-webkit-background-clip:text;background-clip:text;color:transparent;display:flex;align-items:center;gap:.75rem;} .card svg{flex:0 0 32px;color:var(--accent-alt);filter:drop-shadow(0 0 8px rgba(45,232,255,.4));transition:all .5s cubic-bezier(.4,.0,.2,1);} .card p{font-size:1.03rem;color:var(--text-dim);line-height:1.58;} .card:hover{transform:translateY(-8px) scale(1.03);box-shadow:0 36px 80px -24px rgba(0,0,0,.8), 0 0 0 1px rgba(125,91,255,.2), 0 0 40px rgba(125,91,255,.15);border-color:rgba(125,91,255,.25);} .card:hover:before{opacity:.6;transform:scale(1.2) rotate(15deg);} .card:hover:after{opacity:1;} .card:hover svg{transform:scale(1.2) rotate(8deg);filter:drop-shadow(0 0 12px rgba(45,232,255,.6));} .card.tilt-hover:hover{transform:perspective(900px) translateY(-8px) rotateX(5deg) rotateY(-3deg) scale(1.04);}
        form .group{margin-bottom:1.3rem;} form label{display:block;font-size:.7rem;font-weight:700;letter-spacing:.14em;margin-bottom:.55rem;text-transform:uppercase;background:linear-gradient(120deg,var(--accent),var(--primary-alt));-webkit-background-clip:text;background-clip:text;color:transparent;} form input,form textarea,form select{width:100%;background:linear-gradient(135deg,rgba(27,41,64,.7),rgba(29,39,56,.5));border:1.5px solid var(--border);border-radius:14px;padding:.95rem 1.15rem;color:var(--text);font-size:.97rem;font-family:inherit;transition:all .35s cubic-bezier(.4,.0,.2,1);box-shadow:inset 0 2px 8px rgba(0,0,0,.2);} form input::placeholder,form textarea::placeholder{color:var(--text-dim);opacity:.6;} form input:focus,form textarea:focus,form select:focus{outline:none;border-color:var(--primary);background:rgba(29,39,56,.8);box-shadow:0 0 0 3px rgba(125,91,255,.15), inset 0 2px 8px rgba(0,0,0,.25);transform:translateY(-1px);} form input:hover,form textarea:hover,form select:hover{border-color:rgba(125,91,255,.4);} form input[type="checkbox"]{width:auto;accent-color:var(--primary);} form textarea{resize:vertical;min-height:120px;}
        .alerts{margin-bottom:1rem;} .alert{padding:.95rem 1.2rem;border-radius:14px;font-size:.82rem;margin-bottom:.75rem;display:flex;align-items:center;gap:.7rem;backdrop-filter:blur(8px);} .alert:before{content:'✓';font-size:1.1rem;font-weight:700;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;} .alert-success{background:rgba(56,210,125,.16);border:1.5px solid var(--success);color:var(--success);box-shadow:0 4px 12px rgba(56,210,125,.2);} .alert-success:before{background:var(--success);color:#fff;} .alert-error{background:rgba(255,85,85,.16);border:1.5px solid var(--danger);color:var(--danger);box-shadow:0 4px 12px rgba(255,85,85,.2);} .alert-error:before{content:'✕';background:var(--danger);color:#fff;} a.btn-link{color:var(--accent);text-decoration:none;font-weight:600;transition:all .25s ease;position:relative;} a.btn-link:after{content:'→';margin-left:.3rem;transition:margin-left .25s ease;} a.btn-link:hover:after{margin-left:.6rem;}
        footer{background:linear-gradient(135deg,#0d1218,#111826);border-top:1px solid var(--border);padding:3rem 1.8rem;color:var(--text-dim);position:relative;} footer:before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--primary),var(--accent),transparent);}
        .footer-grid{max-width:1280px;margin:0 auto;display:grid;gap:2rem;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));}
        .footer-brand{font-size:1.35rem;font-weight:700;letter-spacing:.06em;color:var(--accent);display:flex;align-items:center;gap:.6rem;margin:0 0 .9rem;}
        .footer-links a{display:block;color:var(--text-dim);text-decoration:none;font-size:.82rem;padding:.3rem 0;transition:var(--transition);} .footer-links a:hover{color:var(--accent-alt);} .footer-copy{margin-top:2.4rem;font-size:.7rem;text-align:center;opacity:.7;}
        @media (max-width:780px){.hero{padding:2.4rem 1.3rem;} .hero h1{font-size:2.2rem;} .grid{gap:1.4rem;} header{padding:.85rem 1.2rem;} nav a:before{left:.6rem; right:.6rem;} }
        @media (prefers-reduced-motion: reduce){ .fade-in,.fade-slide,.stagger.ready > *, .card, .btn, nav a:before { animation:none !important; transition:none !important; } }
    </style>
    </style>
</head>
<body>
    @include('components.loader')
    <header>
        <a href="{{ route('home') }}" class="brand">
            <img src="{{ asset('images/logo.svg') }}" alt="SIAC Logo" style="width:32px; height:32px; filter:drop-shadow(0 0 8px rgba(125,91,255,0.4));">
            SIAC
        </a>
        <nav>
            @auth
                @if(auth()->user()->is_admin)
                    {{-- Menú exclusivo para Administradores --}}
                    <a href="{{ route('contact.messages') }}" class="{{ request()->routeIs('contact.messages') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Mensajes
                    </a>
                @else
                    {{-- Menú para Usuarios Normales --}}
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Inicio
                    </a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        Sobre Nosotros
                    </a>
                    <a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Servicios
                    </a>
                    <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        Contacto
                    </a>
                    <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Perfil
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button class="btn btn-outline" style="margin-left:1rem;">Salir</button>
                </form>
            @else
                {{-- Menú para usuarios NO autenticados --}}
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Inicio
                </a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    Sobre Nosotros
                </a>
                <a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Servicios
                </a>
                <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.35rem;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Contacto
                </a>
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Registro</a>
            @endauth
        </nav>
    </header>
    <main>
        <div class="alerts">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif
        </div>
        @yield('content')
    </main>
    <footer>
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <img src="{{ asset('images/logo.svg') }}" alt="SIAC" style="width:28px; height:28px; filter:drop-shadow(0 0 6px rgba(45,232,255,0.4));">
                    SIAC
                </div>
                <p style="font-size:.82rem;">Tecnología enfocada en asistencia y seguridad de conducción en tiempo real.</p>
                <div style="display:flex; gap:0.5rem; margin-top:1rem; flex-wrap:wrap;">
                    <span style="background:rgba(125,91,255,.15); border:1px solid rgba(125,91,255,.3); padding:.3rem .7rem; border-radius:12px; font-size:.7rem; color:var(--primary);">
                        🔒 Seguro
                    </span>
                    <span style="background:rgba(45,232,255,.15); border:1px solid rgba(45,232,255,.3); padding:.3rem .7rem; border-radius:12px; font-size:.7rem; color:var(--accent);">
                        ⚡ Tiempo Real
                    </span>
                    <span style="background:rgba(255,95,170,.15); border:1px solid rgba(255,95,170,.3); padding:.3rem .7rem; border-radius:12px; font-size:.7rem; color:var(--secondary);">
                        🚗 IoT
                    </span>
                </div>
            </div>
            <div class="footer-links">
                <strong style="font-size:.75rem;letter-spacing:.12em;color:var(--accent);">Navegación</strong>
                <a href="{{ route('home') }}">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.3rem;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Inicio
                </a>
                <a href="{{ route('about') }}">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.3rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    Sobre Nosotros
                </a>
                <a href="{{ route('services') }}">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.3rem;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Servicios
                </a>
                <a href="{{ route('contact') }}">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.3rem;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Contacto
                </a>
                @auth <a href="{{ route('profile') }}">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.3rem;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Perfil
                </a> @endauth
            </div>
            <div class="footer-links">
                <strong style="font-size:.75rem;letter-spacing:.12em;color:var(--accent);">Cuenta</strong>
                @guest <a href="{{ route('login') }}">Login</a> <a href="{{ route('register') }}">Registro</a> @endguest
                @auth <a href="#" onclick="event.preventDefault();document.getElementById('logout-footer').submit();">Cerrar sesión</a>@endauth
                <form id="logout-footer" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
            </div>
            <div class="footer-links">
                <strong style="font-size:.75rem;letter-spacing:.12em;color:var(--accent);">Contacto</strong>
                <a href="mailto:soporte@siac.com">soporte@siac.com</a>
                <span style="display:block;font-size:.7rem;margin-top:.4rem;">Horario: Lun-Vie 9:00-18:00</span>
            </div>
        </div>
        <div class="footer-copy">&copy; {{ date('Y') }} SIAC. Todos los derechos reservados.</div>
    </footer>
</body>
</html>