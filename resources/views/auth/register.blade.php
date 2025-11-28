@extends('layouts.main')
@section('title','Registro')
@section('content')
<div class="card fade-in glow" style="max-width:560px; margin:0 auto; position:relative; overflow:visible;">
    <div style="position:absolute; top:-70px; left:-70px; width:220px; height:220px; background:radial-gradient(circle, rgba(255,95,170,.3), transparent 70%); filter:blur(55px); z-index:-1;"></div>
    <div style="text-align:center; margin-bottom:2rem;">
        <div style="display:inline-flex; align-items:center; justify-content:center; margin-bottom:1rem;">
            <img src="{{ asset('images/auth-security.svg') }}" alt="Seguridad" style="width:120px; height:120px;" class="float">
        </div>
        <h1 style="margin:0; font-size:clamp(1.7rem,2rem,2.2rem); background:linear-gradient(120deg,var(--secondary),var(--primary)); -webkit-background-clip:text; background-clip:text; color:transparent;">Crear cuenta</h1>
        <p style="color:var(--text-dim); margin:.5rem 0 0; font-size:.95rem;">Únete a SIAC y descubre una nueva forma de conducir</p>
    </div>
    <form action="{{ route('register.perform') }}" method="POST">
        @csrf
        <div style="display:grid; gap:1.3rem; grid-template-columns:repeat(auto-fit, minmax(220px,1fr));">
            <div class="group">
                <label for="name">Nombre(s)</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Juan" required>
                @error('name')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
            </div>
            <div class="group">
                <label for="last_name">Apellidos</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" placeholder="Pérez García" required>
                @error('last_name')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="group">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="tucorreo@ejemplo.com" required>
            @error('email')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
        </div>
        <div class="group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" placeholder="Ej: MiClave123" required>
            @error('password')<small style="color:var(--danger); font-size:.75rem; display:block; margin-top:.3rem;">{{ $message }}</small>@enderror
            
            <div id="password-requirements" style="margin-top:.8rem; padding:.9rem; background:rgba(125,91,255,.08); border:1px solid rgba(125,91,255,.2); border-radius:12px; font-size:.8rem;">
                <div style="margin-bottom:.5rem; font-weight:600; color:var(--text); display:flex; align-items:center; gap:.4rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px;">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Requisitos de seguridad:
                </div>
                <div id="req-length" class="password-req" style="display:flex; align-items:center; gap:.5rem; padding:.3rem 0; color:var(--text-dim); transition:all .3s ease;">
                    <span class="req-icon">○</span>
                    <span>Mínimo 8 caracteres</span>
                </div>
                <div id="req-uppercase" class="password-req" style="display:flex; align-items:center; gap:.5rem; padding:.3rem 0; color:var(--text-dim); transition:all .3s ease;">
                    <span class="req-icon">○</span>
                    <span>Al menos una mayúscula (A-Z)</span>
                </div>
                <div id="req-number" class="password-req" style="display:flex; align-items:center; gap:.5rem; padding:.3rem 0; color:var(--text-dim); transition:all .3s ease;">
                    <span class="req-icon">○</span>
                    <span>Al menos un número (0-9)</span>
                </div>
            </div>
        </div>
        <div class="group">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Repite tu contraseña" required>
            <small id="password-match" style="display:none; margin-top:.5rem; font-size:.8rem;"></small>
        </div>
        <button class="btn glow" style="width:100%; font-size:1rem; padding:1.1rem; margin-top:.8rem;">
            <span style="display:flex; align-items:center; justify-content:center; gap:.6rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px; height:20px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Crear cuenta
            </span>
        </button>
    </form>
    <div style="margin-top:1.8rem; text-align:center; padding-top:1.5rem; border-top:1px solid var(--border);">
        <p style="color:var(--text-dim); font-size:.9rem;">¿Ya tienes cuenta? <a class="btn-link" href="{{ route('login') }}">Inicia sesión</a></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqNumber = document.getElementById('req-number');
    const matchMessage = document.getElementById('password-match');

    // Validación en tiempo real de la contraseña
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Validar longitud (mínimo 8 caracteres)
        if (password.length >= 8) {
            updateRequirement(reqLength, true);
        } else {
            updateRequirement(reqLength, false);
        }
        
        // Validar mayúscula
        if (/[A-Z]/.test(password)) {
            updateRequirement(reqUppercase, true);
        } else {
            updateRequirement(reqUppercase, false);
        }
        
        // Validar número
        if (/[0-9]/.test(password)) {
            updateRequirement(reqNumber, true);
        } else {
            updateRequirement(reqNumber, false);
        }

        // Verificar coincidencia si hay texto en confirmar
        if (confirmInput.value) {
            checkPasswordMatch();
        }
    });

    // Verificar coincidencia de contraseñas
    confirmInput.addEventListener('input', checkPasswordMatch);

    function updateRequirement(element, isValid) {
        const icon = element.querySelector('.req-icon');
        if (isValid) {
            element.style.color = '#2de8ff';
            icon.textContent = '✓';
            icon.style.fontWeight = 'bold';
        } else {
            element.style.color = 'var(--text-dim)';
            icon.textContent = '○';
            icon.style.fontWeight = 'normal';
        }
    }

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        
        if (confirm.length === 0) {
            matchMessage.style.display = 'none';
            return;
        }
        
        matchMessage.style.display = 'block';
        
        if (password === confirm) {
            matchMessage.textContent = '✓ Las contraseñas coinciden';
            matchMessage.style.color = '#2de8ff';
        } else {
            matchMessage.textContent = '✗ Las contraseñas no coinciden';
            matchMessage.style.color = '#ff5faa';
        }
    }
});
</script>

@endsection