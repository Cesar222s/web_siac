<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="SIAC - Sistema Inteligente de Asistencia en Conducción. Monitoreo en tiempo real, seguridad avanzada y alertas inteligentes.">
    <meta name="keywords" content="SIAC, asistencia conducción, seguridad vehicular, IoT, monitoreo tiempo real">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    <!-- PWA Manifest and Theme Color -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#070b13">
    <title>SIAC - @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Carga dinámica: si existe manifest de Vite usar assets compilados, si no usar fallback ligero --}}
    @php($manifestPath = public_path('build/manifest.json'))
    @if (file_exists($manifestPath))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @endif
    <style>
        /* ── Midnight Guard Palette (aligned with Android app) ── */
        :root {
            --bg:#f8fafc; --surface:#ffffff; --surface-alt:#f1f5f9; --surface-soft:#f8fafc;
            --border:#e2e8f0; --text:#0f172a; --text-dim:#475569; --primary:#2563eb; --primary-alt:#3b82f6;
            --secondary:#f59e0b; --secondary-alt:#fbbf24; --accent:#2563eb; --accent-alt:#1d4ed8;
            --danger:#ef4444; --success:#10b981; --focus:#2563eb30;
            --font-base:17px; --font-small:0.88rem; --font-btn:0.92rem; --radius:16px; --transition:.2s ease;
            --shadow-elev:0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
        }
        body { background: var(--bg); color: var(--text); }
        /* Fondo limpio con toque sutil */
        body:after {
            content:''; position:fixed; inset:0; z-index:-1;
            background-image: radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.03) 0, transparent 50%), 
                              radial-gradient(at 100% 100%, rgba(245, 158, 11, 0.03) 0, transparent 50%);
        }
        /* Transiciones base y utilidades */
        .ease-fast{transition:all .18s cubic-bezier(.4,.0,.2,1);} .ease{transition:all .28s cubic-bezier(.4,.0,.2,1);} .ease-slow{transition:all .45s cubic-bezier(.25,.8,.25,1);}
        .fade-slide{opacity:0; transform:translateY(14px); animation:fadeSlide .65s cubic-bezier(.4,.0,.2,1) forwards;} @keyframes fadeSlide{to{opacity:1; transform:translateY(0);}}
        .fade-in{opacity:0; animation:fadeIn .8s cubic-bezier(.4,.0,.2,1) forwards;} @keyframes fadeIn{to{opacity:1;}}
        .stagger > *{opacity:0; transform:translateY(16px);} .stagger.ready > *{animation:fadeStagger .7s cubic-bezier(.4,.0,.2,1) forwards;} .stagger.ready > *:nth-child(2){animation-delay:.1s;} .stagger.ready > *:nth-child(3){animation-delay:.2s;} .stagger.ready > *:nth-child(4){animation-delay:.3s;} @keyframes fadeStagger{to{opacity:1; transform:translateY(0);}}
        .tilt-hover:hover{transform:translateY(-2px);}
        .float{animation:float 6s ease-in-out infinite;} @keyframes float{0%,100%{transform:translateY(0);} 50%{transform:translateY(-8px);}}
        .glow{position:relative;} .glow:hover{filter:brightness(1.05);}
        .reveal {opacity:0; transform:translateY(30px) scale(0.98); filter:blur(10px); transition:all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);}
        .reveal.active {opacity:1; transform:translateY(0) scale(1); filter:blur(0);}
        *{box-sizing:border-box;} body{margin:0;font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);line-height:1.62;font-size:var(--font-base);letter-spacing:.15px;overflow-x:hidden;}
        h1,h2,h3{font-weight:700;letter-spacing:-0.02em;line-height:1.12;margin:0 0 .65rem;} h1{font-size:clamp(2.4rem,3.4rem,3.6rem);} h2{font-size:clamp(1.55rem,1.85rem,2rem);} h3{font-size:clamp(1.15rem,1.25rem,1.35rem);} p{margin:.4rem 0 1.05rem;font-size:1.02rem;opacity:0.9;}
                header{display:flex;align-items:center;justify-content:space-between;padding:1rem 2.5rem;background:rgba(255,255,255,0.9);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:60;backdrop-filter:blur(10px);box-shadow:0 1px 3px rgba(0,0,0,0.05);} 
        .brand{display:flex;align-items:center;gap:.7rem;font-size:1.4rem;font-weight:800;letter-spacing:-0.03em;color:var(--text);text-decoration:none;transition:opacity .2s ease;} .brand:hover{opacity:0.8;}
        nav{display:flex;align-items:center;gap:.5rem;} 
        nav a{color:var(--text-dim);text-decoration:none;padding:.6rem 1rem;font-weight:600;font-size:0.9rem;border-radius:10px;transition:all .2s ease;} 
        nav a:hover, nav a.active{color:var(--primary);background:var(--surface-alt);}
        main{max-width:1200px;margin:3rem auto 5rem;padding:0 1.5rem;} 
        .hero{background:#fff;padding:5rem 2.5rem;border-radius:24px;border:1px solid var(--border);text-align:center;box-shadow:var(--shadow-elev);} 
        .hero h1{color:var(--text);margin-bottom:1.5rem;font-weight:800;letter-spacing:-0.04em;} 
        .hero p{max-width:700px;margin:0 auto 2.5rem;font-size:1.15rem;color:var(--text-dim);} 
        .btn{display:inline-flex;align-items:center;justify-content:center;background:var(--primary);color:#fff;border:none;padding:.9rem 1.8rem;border-radius:12px;font-weight:700;font-size:var(--font-btn);cursor:pointer;transition:all .2s ease;text-decoration:none;box-shadow:0 4px 14px 0 rgba(37,99,235,0.25);} 
        .btn:hover{background:var(--accent-alt);transform:translateY(-2px);box-shadow:0 6px 20px rgba(37,99,235,0.3);} 
        .btn-outline{background:transparent;border:2px solid var(--border);color:var(--text);box-shadow:none;} 
        .btn-outline:hover{border-color:var(--primary);color:var(--primary);background:transparent;}
        .grid{display:grid;gap:2rem;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));margin-top:4rem;} 
        .card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:2.5rem;box-shadow:var(--shadow-elev);transition:all .3s ease;} 
        .card:hover{transform:translateY(-5px);box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); border-color:var(--primary);}
        .card h2{margin:0 0 1rem;font-size:1.25rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:.75rem;} 
        .card svg{color:var(--primary);flex-shrink:0;}
        form .group{margin-bottom:1.5rem;} 
        form label{display:block;font-size:.85rem;font-weight:600;color:var(--text);margin-bottom:.5rem;} 
        form input,form textarea,form select{width:100%;background:#fff;border:1.5px solid var(--border);border-radius:10px;padding:.85rem 1rem;color:var(--text);transition:all .2s ease;} 
        form input:focus{outline:none;border-color:var(--primary);ring:3px rgba(37,99,235,0.15);}
        .alerts{margin-bottom:1rem;} .alert{padding:.95rem 1.2rem;border-radius:14px;font-size:.82rem;margin-bottom:.75rem;display:flex;align-items:center;gap:.7rem;backdrop-filter:blur(8px);} .alert:before{content:'✓';font-size:1.1rem;font-weight:700;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;} .alert-success{background:rgba(16,185,129,.16);border:1.5px solid var(--success);color:var(--success);box-shadow:0 4px 12px rgba(16,185,129,.2);} .alert-success:before{background:var(--success);color:#fff;} .alert-error{background:rgba(239,68,68,.16);border:1.5px solid var(--danger);color:var(--danger);box-shadow:0 4px 12px rgba(239,68,68,.2);} .alert-error:before{content:'✕';background:var(--danger);color:#fff;} a.btn-link{color:var(--secondary);text-decoration:none;font-weight:600;transition:all .25s ease;position:relative;} a.btn-link:after{content:'→';margin-left:.3rem;transition:margin-left .25s ease;} a.btn-link:hover:after{margin-left:.6rem;}
                footer{background:#fff;border-top:1px solid var(--border);padding:5rem 2rem;color:var(--text-dim);text-align:center;}
        .footer-grid{max-width:1100px;margin:0 auto;display:grid;gap:3rem;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));text-align:left;}
        .footer-brand{font-size:1.5rem;font-weight:800;color:var(--text);display:flex;align-items:center;gap:.7rem;margin-bottom:1rem;}
        .footer-links a{display:block;color:var(--text-dim);text-decoration:none;font-size:.9rem;padding:.4rem 0;transition:.2s;} .footer-links a:hover{color:var(--primary);} .footer-copy{margin-top:4rem;font-size:.8rem;opacity:.5;}
        @media (max-width:780px){.hero{padding:2.4rem 1.3rem;} .hero h1{font-size:2.2rem;} .grid{gap:1.4rem;} header{padding:.85rem 1.2rem;} nav a:before{left:.6rem; right:.6rem;} }
        @media (prefers-reduced-motion: reduce){ .fade-in,.fade-slide,.stagger.ready > *, .card, .btn, nav a:before { animation:none !important; transition:none !important; } }
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
                    {{-- Dashboard eliminado según indicación --}}
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
                {{-- Dashboard eliminado según indicación --}}
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
                    <img src="{{ asset('images/logo.svg') }}" alt="SIAC" style="width:28px; height:28px; filter:drop-shadow(0 0 6px rgba(245,158,11,0.4));">
                    SIAC
                </div>
                <p style="font-size:.82rem;">Tecnología enfocada en asistencia y seguridad de conducción en tiempo real.</p>
                <div style="display:flex; gap:0.5rem; margin-top:1rem; flex-wrap:wrap;">
                    <span style="background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.3); padding:.3rem .7rem; border-radius:12px; font-size:.7rem; color:var(--primary);">
                        🔒 Seguro
                    </span>
                    <span style="background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.3); padding:.3rem .7rem; border-radius:12px; font-size:.7rem; color:var(--accent);">
                        ⚡ Tiempo Real
                    </span>
                    <span style="background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); padding:.3rem .7rem; border-radius:12px; font-size:.7rem; color:var(--success);">
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

    <!-- PWA Status Banner -->
    <div id="offline-banner" style="display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%); background:#fee2e2; border:1px solid #ef4444; color:#991b1b; padding:.75rem 1.5rem; border-radius:12px; z-index:9999; font-weight:600; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);">
        No hay conexión. Trabajando en modo local.
    </div>

    <!-- PWA Install Button -->
    <button id="pwa-install-btn" style="display:none; position:fixed; bottom:30px; right:30px; background:var(--text); color:#fff; border:none; border-radius:14px; padding:.9rem 1.4rem; font-weight:700; font-size:0.95rem; box-shadow:0 10px 20px rgba(0,0,0,0.15); cursor:pointer; z-index:9999; align-items:center; gap:.7rem; transition:all .2s ease;">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Instalar SIAC
    </button>

    <!-- Service Worker and PWA Logic -->
    <script>
      let deferredPrompt;
      const installBtn = document.getElementById('pwa-install-btn');
      const offlineBanner = document.getElementById('offline-banner');

      // 0. Base de Datos Local (IndexedDB)
      const dbRequest = indexedDB.open('siac_offline', 1);
      dbRequest.onupgradeneeded = (e) => {
        const db = e.target.result;
        if (!db.objectStoreNames.contains('registrations')) {
          db.createObjectStore('registrations', { keyPath: 'id', autoIncrement: true });
        }
      };

      // 1. Manejo de Instalación
      window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        installBtn.style.display = 'flex';
      });

      installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        installBtn.style.transform = 'scale(0.95)';
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') installBtn.style.display = 'none';
        deferredPrompt = null;
      });

      // 2. Manejo de Conectividad y Sincronización Automática
      async function syncRegistrations() {
        if (!navigator.onLine) return;
        
        const db = await new Promise((resolve) => {
          const req = indexedDB.open('siac_offline', 1);
          req.onsuccess = () => resolve(req.result);
        });

        const tx = db.transaction('registrations', 'readonly');
        const store = tx.objectStore('registrations');
        const pending = await new Promise((resolve) => {
          const req = store.getAll();
          req.onsuccess = () => resolve(req.result);
        });

        if (pending.length > 0) {
          console.log(`PWA: Sincronizando ${pending.length} registros pendientes...`);
          for (const data of pending) {
            try {
              const response = await fetch('{{ route("register.perform") }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Accept': 'application/json'
                },
                body: JSON.stringify(data.formData)
              });

              if (response.ok) {
                const deleteTx = db.transaction('registrations', 'readwrite');
                deleteTx.objectStore('registrations').delete(data.id);
                console.log('PWA: Registro sincronizado exitosamente.');
              }
            } catch (err) {
              console.error('PWA: Error al sincronizar:', err);
            }
          }
        }
      }

      function updateOnlineStatus() {
        if (navigator.onLine) {
          offlineBanner.style.display = 'none';
          syncRegistrations(); // Sincronizar automáticamente al volver online
        } else {
          offlineBanner.style.display = 'block';
        }
      }

      window.addEventListener('online',  updateOnlineStatus);
      window.addEventListener('offline', updateOnlineStatus);
      updateOnlineStatus();

      // 3. Registro de Service Worker
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('/sw.js').then(function(registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
          }, function(err) {
            console.log('ServiceWorker registration failed: ', err);
          });
        });
      }

      // 4. Reveal-on-scroll Script
      document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('active');
            }
          });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
      });
    </script>
</body>
</html>