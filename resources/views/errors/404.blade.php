@extends('layouts.main')
@section('title','Página no encontrada')
@section('content')
<div style="text-align:center; padding:3rem 1rem;">
    <div class="fade-in">
        <img src="{{ asset('images/404.svg') }}" alt="404" style="max-width:500px; width:100%; height:auto; margin:0 auto 2rem;" class="float">
        
        <h1 style="font-size:clamp(2rem,3rem,3.5rem); margin:0 0 1rem; background:linear-gradient(120deg,var(--primary),var(--accent)); -webkit-background-clip:text; background-clip:text; color:transparent;">
            ¡Ups! Ruta no encontrada
        </h1>
        
        <p style="font-size:1.1rem; color:var(--text-dim); max-width:600px; margin:0 auto 2.5rem;">
            Parece que tomaste un desvío equivocado. La página que buscas no existe o ha sido movida a otra ubicación.
        </p>
        
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="{{ route('home') }}" class="btn">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.5rem;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Volver al Inicio
            </a>
            <a href="{{ route('contact') }}" class="btn-outline">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:.5rem;">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
                Contactar Soporte
            </a>
        </div>
        
        <div style="margin-top:3rem; padding:2rem; background:linear-gradient(135deg,rgba(125,91,255,.08),rgba(45,232,255,.05)); border:1px solid rgba(125,91,255,.15); border-radius:20px; max-width:700px; margin-left:auto; margin-right:auto;">
            <h3 style="font-size:1.2rem; margin:0 0 1rem; color:var(--accent);">Enlaces útiles</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; text-align:left;">
                <a href="{{ route('about') }}" style="color:var(--text-dim); text-decoration:none; transition:.25s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-dim)'">
                    → Sobre Nosotros
                </a>
                <a href="{{ route('services') }}" style="color:var(--text-dim); text-decoration:none; transition:.25s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-dim)'">
                    → Servicios
                </a>
                @auth
                <a href="{{ route('profile') }}" style="color:var(--text-dim); text-decoration:none; transition:.25s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-dim)'">
                    → Mi Perfil
                </a>
                @else
                <a href="{{ route('login') }}" style="color:var(--text-dim); text-decoration:none; transition:.25s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-dim)'">
                    → Iniciar Sesión
                </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
