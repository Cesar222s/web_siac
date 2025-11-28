@extends('layouts.main')
@section('title','Contacto')
@section('content')
<div style="max-width:780px; margin:0 auto;">
    <div style="text-align:center; margin-bottom:2.8rem;" class="fade-in">
        <div style="display:inline-flex; align-items:center; justify-content:center; margin-bottom:1.2rem;">
            <img src="{{ asset('images/icon-email.svg') }}" alt="Contacto" style="width:120px; height:120px;" class="float">
        </div>
        <h1 style="margin:0 0 .6rem; font-size:clamp(2rem,2.4rem,2.6rem); background:linear-gradient(120deg,var(--accent),var(--primary)); -webkit-background-clip:text; background-clip:text; color:transparent;">Contáctanos</h1>
        <p style="color:var(--text-dim); font-size:1.05rem; max-width:520px; margin:0 auto;">¿Tienes dudas, sugerencias o necesitas soporte? Completa el formulario y nuestro equipo te responderá a la brevedad.</p>
    </div>

    <div class="card fade-in glow" style="animation-delay:.12s;">
        @if(session('success'))
        <div class="alert alert-success" style="animation:fadeSlide .6s cubic-bezier(.4,.0,.2,1); margin-bottom:1.5rem;">
            {{ session('success') }}
        </div>
        @endif
        
        <form action="{{ route('contact.submit') }}" method="POST">
            @csrf
            <div style="display:grid; gap:1.3rem; grid-template-columns:repeat(auto-fit, minmax(220px,1fr));">
                <div class="group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" placeholder="Juan Pérez" required>
                    @error('nombre')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
                </div>
                <div class="group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="juan@ejemplo.com" required>
                    @error('email')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
                </div>
            </div>
            <div class="group">
                <label for="asunto">Asunto</label>
                <select name="asunto" id="asunto" required style="width:100%; padding:.9rem 1rem; background:var(--surface-soft); border:1px solid var(--border); border-radius:12px; color:var(--text); font-size:.95rem; transition:all .3s ease; cursor:pointer;">
                    <option value="" disabled selected>Selecciona un asunto</option>
                    <option value="Consulta General" {{ old('asunto') == 'Consulta General' ? 'selected' : '' }}>Consulta General</option>
                    <option value="Soporte Técnico" {{ old('asunto') == 'Soporte Técnico' ? 'selected' : '' }}>Soporte Técnico</option>
                    <option value="Información de Servicios" {{ old('asunto') == 'Información de Servicios' ? 'selected' : '' }}>Información de Servicios</option>
                    <option value="Problemas con la Aplicación" {{ old('asunto') == 'Problemas con la Aplicación' ? 'selected' : '' }}>Problemas con la Aplicación</option>
                    <option value="Sugerencias y Mejoras" {{ old('asunto') == 'Sugerencias y Mejoras' ? 'selected' : '' }}>Sugerencias y Mejoras</option>
                    <option value="Colaboración o Alianzas" {{ old('asunto') == 'Colaboración o Alianzas' ? 'selected' : '' }}>Colaboración o Alianzas</option>
                    <option value="Otro" {{ old('asunto') == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
                @error('asunto')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
            </div>
            <div class="group">
                <label for="mensaje">Mensaje</label>
                <textarea name="mensaje" id="mensaje" rows="6" placeholder="Escribe tu mensaje aquí..." required>{{ old('mensaje') }}</textarea>
                @error('mensaje')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
            </div>
            <button class="btn glow" style="width:100%; font-size:1rem; padding:1.1rem; margin-top:.5rem;">
                <span style="display:flex; align-items:center; justify-content:center; gap:.6rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Enviar mensaje
                </span>
            </button>
        </form>
        
        <div style="margin-top:2.2rem; padding-top:2rem; border-top:1px solid var(--border); display:grid; gap:1.2rem; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); font-size:.88rem;">
            <div style="display:flex; align-items:start; gap:.8rem;">
                <div style="flex-shrink:0; width:40px; height:40px; border-radius:12px; background:rgba(45,232,255,.1); display:flex; align-items:center; justify-content:center;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                    <strong style="color:var(--text); display:block; margin-bottom:.2rem;">Email</strong>
                    <a href="mailto:soporte@siac.com" style="color:var(--text-dim); text-decoration:none; transition:color .25s ease;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-dim)'">soporte@siac.com</a>
                </div>
            </div>
            <div style="display:flex; align-items:start; gap:.8rem;">
                <div style="flex-shrink:0; width:40px; height:40px; border-radius:12px; background:rgba(125,91,255,.1); display:flex; align-items:center; justify-content:center;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <strong style="color:var(--text); display:block; margin-bottom:.2rem;">Horario</strong>
                    <span style="color:var(--text-dim);">Lun-Vie 9:00 - 18:00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapa de Ubicación -->
    <div class="card fade-in" style="animation-delay:.2s; margin-top:2rem; padding:0; overflow:hidden;">
        <div style="padding:1.5rem 2rem; background:linear-gradient(135deg,rgba(125,91,255,.08),rgba(45,232,255,.05)); border-bottom:1px solid var(--border);">
            <h2 style="margin:0 0 .5rem; font-size:1.4rem; color:var(--text);">📍 Nuestra Ubicación</h2>
            <p style="margin:0; color:var(--text-dim); font-size:.9rem;">Universidad Tecnológica de la Zona Metropolitana de Guadalajara</p>
        </div>
        <div style="position:relative; width:100%; height:450px; background:var(--surface-soft);">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3737.563919393111!2d-103.53575682475815!3d20.483096581033163!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842f55f2173b24d1%3A0xb3fc2b0647d8a722!2sUniversidad%20Tecnol%C3%B3gica%20de%20la%20Zona%20Metropolitana%20de%20Guadalajara!5e0!3m2!1ses-419!2smx!4v1764165056945!5m2!1ses-419!2smx" 
                width="100%" 
                height="100%" 
                style="border:0; display:block;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        <div style="padding:1.2rem 2rem; background:rgba(255,255,255,.02); border-top:1px solid var(--border); display:flex; align-items:center; gap:.8rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px; flex-shrink:0;">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
            <span style="color:var(--text-dim); font-size:.88rem;">Carretera Tlajomulco - San Miguel Cuyutlán km. 5.5, Jalisco, México</span>
        </div>
    </div>
</div>
@endsection