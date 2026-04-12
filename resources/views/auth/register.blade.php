@extends('layouts.main')
@section('title','Registro')
@section('content')
<div class="card fade-in reveal" style="max-width:600px; margin:0 auto; padding: 4rem 3rem;">
    <div style="text-align:center; margin-bottom:3rem;">
        <div style="display:inline-flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
            <img src="{{ asset('images/auth-security.svg') }}" alt="Seguridad" style="width:100px; height:100px;" class="float">
        </div>
        <h1 style="margin:0; font-size:2.4rem; font-weight:800; color:var(--text); letter-spacing:-0.03em;">Crear cuenta</h1>
        <p style="color:var(--text-dim); margin:.5rem 0 0; font-size:1.1rem;">Protección y seguridad para cada trayecto</p>
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
            
            <div id="password-requirements" style="margin-top:1rem; padding:1.2rem; background:var(--surface-alt); border:1px solid var(--border); border-radius:12px; font-size:.85rem;">
                <div style="margin-bottom:.8rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:.5rem;">
                    Seguridad:
                </div>
                <div id="req-length" class="password-req" style="display:flex; align-items:center; gap:.5rem; padding:.2rem 0; color:var(--text-dim);">
                    <span class="req-icon">○</span>
                    <span>Mínimo 8 caracteres</span>
                </div>
                <div id="req-uppercase" class="password-req" style="display:flex; align-items:center; gap:.5rem; padding:.2rem 0; color:var(--text-dim);">
                    <span class="req-icon">○</span>
                    <span>Al menos una mayúscula</span>
                </div>
                <div id="req-number" class="password-req" style="display:flex; align-items:center; gap:.5rem; padding:.2rem 0; color:var(--text-dim);">
                    <span class="req-icon">○</span>
                    <span>Al menos un número</span>
                </div>
            </div>
        </div>
        <div class="group">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Repite tu contraseña" required>
            <small id="password-match" style="display:none; margin-top:.5rem; font-size:.8rem;"></small>
        </div>
        <button class="btn" style="width:100%; padding:1.1rem; margin-top:2rem; font-weight:800;">Crear cuenta</button>
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

    // --- LÓGICA OFFLINE PWA ---
    const registerForm = document.querySelector('form[action$="register"]');
    
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            if (navigator.onLine) return; // Si hay internet, proceder normal

            e.preventDefault();
            console.log('PWA: Detectado registro offline. Guardando datos...');

            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => { data[key] = value; });

            // Guardar en IndexedDB
            const db = await new Promise((resolve) => {
                const req = indexedDB.open('siac_offline', 1);
                req.onsuccess = () => resolve(req.result);
            });

            const tx = db.transaction('registrations', 'readwrite');
            tx.objectStore('registrations').add({ formData: data, timestamp: new Date().getTime() });

            await new Promise((resolve) => { tx.oncomplete = resolve; });

            // UI: Mostrar mensaje de éxito offline
            this.style.transition = 'opacity 0.5s ease';
            this.style.opacity = '0';
            setTimeout(() => {
                this.innerHTML = `
                    <div style="text-align:center; padding:3rem 2rem; background:#f0fdf4; border:1px solid #16a34a; border-radius:24px;" class="fade-in">
                        <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="#16a34a" stroke-width="2.5" style="margin-bottom:1.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <h2 style="color:#166534; margin:0 0 1rem; font-weight:800;">¡Perfil Guardado!</h2>
                        <p style="color:#166534; font-size:1.1rem; margin-bottom:2rem;">Te has registrado localmente. Tus datos se enviarán cuando recuperes la conexión.</p>
                        <div style="background:rgba(22,163,74,0.1); padding:1rem; border-radius:12px; font-size:.9rem; color:#166534; font-weight:600;">
                            🔄 Sincronización automática activa
                        </div>
                    </div>
                `;
                this.style.opacity = '1';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 500);
        });
    }

    // --- VALIDACIÓN DE FORMULARIO EXISTENTE ---
    confirmInput.addEventListener('input', checkPasswordMatch);

    function updateRequirement(element, isValid) {
        const icon = element.querySelector('.req-icon');
        if (isValid) {
            element.style.color = '#10B981';
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
            matchMessage.style.color = '#10B981';
        } else {
            matchMessage.textContent = '✗ Las contraseñas no coinciden';
            matchMessage.style.color = '#F59E0B';
        }
    }
});
</script>

@endsection