@extends('layouts.main')
@section('title','Login')
@section('content')
<div class="card fade-in glow" style="max-width:520px; margin:0 auto; position:relative; overflow:visible;">
    <div style="position:absolute; top:-60px; right:-60px; width:200px; height:200px; background:radial-gradient(circle, rgba(125,91,255,.35), transparent 70%); filter:blur(50px); z-index:-1;"></div>
    <div style="text-align:center; margin-bottom:2rem;">
        <div style="display:inline-flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/auth-security.svg') }}" alt="Seguridad" style="width:120px; height:120px;" class="float">
        </div>
        <h1 style="margin:0; font-size:clamp(1.7rem,2rem,2.2rem); background:linear-gradient(120deg,var(--primary),var(--secondary)); -webkit-background-clip:text; background-clip:text; color:transparent;">Bienvenido de nuevo</h1>
        <p style="color:var(--text-dim); margin:.5rem 0 0; font-size:.95rem;">Ingresa tus credenciales para continuar</p>
    </div>
    <form action="{{ route('login.perform') }}" method="POST">
        @csrf
        <div class="group">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="tucorreo@ejemplo.com" required>
            @error('email')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
        </div>
        <div class="group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" placeholder="••••••••" required>
            @error('password')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
        </div>
        <div class="group" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.8rem;">
            <label style="display:flex; align-items:center; gap:.5rem; margin:0; cursor:pointer; text-transform:none; background:none; color:var(--text-dim); font-size:.85rem;">
                <input type="checkbox" name="remember" id="remember" style="width:auto;">
                Recordarme
            </label>
            <a href="{{ route('password.request') }}" class="btn-link" style="font-size:.82rem;">¿Olvidaste tu contraseña?</a>
        </div>
        <button class="btn glow" style="width:100%; font-size:1rem; padding:1.1rem;">Iniciar sesión</button>
    </form>
    <div style="margin-top:1.8rem; text-align:center; padding-top:1.5rem; border-top:1px solid var(--border);">
        <p style="color:var(--text-dim); font-size:.9rem;">¿No tienes cuenta? <a class="btn-link" href="{{ route('register') }}">Crear cuenta</a></p>
    </div>
</div>
@endsection