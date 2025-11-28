@extends('layouts.main')
@section('title','Recuperar Contraseña')
@section('content')
<div class="card fade-in" style="max-width:480px; margin:0 auto;">
    <div style="text-align:center; margin-bottom:1.5rem;">
        <div style="display:inline-flex; align-items:center; justify-content:center; width:64px; height:64px; background:rgba(125,91,255,.15); border-radius:16px; margin-bottom:1rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:32px; height:32px;">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h1 style="margin:0 0 .5rem; font-size:1.8rem;">Recuperar Contraseña</h1>
        <p style="color:var(--text-dim); font-size:.9rem; margin:0;">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña</p>
    </div>

    @if(session('status'))
    <div class="alert alert-success" style="margin-bottom:1.5rem;">
        {{ session('status') }}
    </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" autocomplete="on">
        @csrf
        <div class="group">
            <label for="email">Correo electrónico</label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="{{ old('email') }}" 
                placeholder="tu@email.com"
                autocomplete="email"
                autofocus
                required>
            @error('email')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
        </div>
        <button type="submit" class="btn" style="width:100%; margin-top:.5rem;">
            <span style="display:flex; align-items:center; justify-content:center; gap:.6rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                </svg>
                Enviar enlace de recuperación
            </span>
        </button>
    </form>
    <p style="margin-top:1.5rem; text-align:center; font-size:.9rem;">
        <a href="{{ route('login') }}" style="color:var(--primary); text-decoration:none; transition:color .25s ease;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--primary)'">
            ← Volver al inicio de sesión
        </a>
    </p>
</div>
@endsection
