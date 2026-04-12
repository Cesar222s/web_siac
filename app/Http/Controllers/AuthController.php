<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirigir a mensajes si es administrador
            if (Auth::user()->is_admin) {
                return redirect()->route('contact.messages')->with('success', 'Bienvenido, Administrador.');
            }
            
            return redirect()->route('profile')->with('success', 'Bienvenido de nuevo.');
        }

        return back()->withErrors([
            'email' => 'Credenciales inválidas.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',      // Al menos una mayúscula
                'regex:/[0-9]/',      // Al menos un número
            ],
            'driver_age' => 'required|integer|min:16|max:90',
            'experience_years' => 'required|integer|min:0|max:70',
            'usual_location' => 'required|string|max:180',
            // HH:MM-HH:MM o HH:MM; separados por coma, 24h
            'usual_hours' => [
                'required',
                'string',
                'max:180',
                'regex:/^\s*(?:([01]?[0-9]|2[0-3]):[0-5][0-9]-([01]?[0-9]|2[0-3]):[0-5][0-9]|([01]?[0-9]|2[0-3]):[0-5][0-9])\s*(?:,\s*(?:([01]?[0-9]|2[0-3]):[0-5][0-9]-([01]?[0-9]|2[0-3]):[0-5][0-9]|([01]?[0-9]|2[0-3]):[0-5][0-9]))*\s*$/'
            ],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes proporcionar un correo electrónico válido (ejemplo@dominio.com).',
            'email.unique' => 'Este correo ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'usual_hours.regex' => 'Usa el formato: 06:00-09:00,18:00 (24h, separado por comas).',
        ]);
        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // Persistencia ligera en JSON (columna 'profile' opcional)
            'profile' => json_encode([
                'driver_age' => $data['driver_age'] ?? null,
                'experience_years' => $data['experience_years'] ?? null,
                'usual_location' => $data['usual_location'] ?? null,
                'usual_hours' => $data['usual_hours'] ?? null,
            ]),
        ]);

        // Llamar API de riesgo con los datos del usuario
        try {
            $apiUrl = config('services.risk_api.url', env('RISK_API_URL', 'http://localhost:8001/analyze'));
            $horarioUso = 'Tarde';
            // Derivar horario aproximado según usual_hours si existe
            if (!empty($data['usual_hours'])) {
                $hstr = (string)$data['usual_hours'];
                $hours = collect(explode(',', $hstr))->map(fn($s)=>trim($s))->filter()->map(function($t){
                    $h = (int)explode(':', $t)[0];
                    return $h;
                })->all();
                if (!empty($hours)) {
                    $max = max($hours); $min = min($hours);
                    if ($max >= 22 || $min <= 5) $horarioUso = 'Noche';
                    elseif ($min >= 6 && $max <= 11) $horarioUso = 'Mañana';
                    elseif ($min >= 12 && $max <= 17) $horarioUso = 'Tarde';
                    else $horarioUso = 'Tarde';
                }
            }
            $payload = [
                'edad' => (int)($data['driver_age'] ?? 30),
                'experiencia_anios' => (int)($data['experience_years'] ?? 3),
                'ubicacion' => (string)($data['usual_location'] ?? ''),
                'horario_uso' => $horarioUso,
            ];
            $response = \Illuminate\Support\Facades\Http::timeout(15)->post($apiUrl, $payload);
            if ($response->successful()) {
                $risk = $response->json();
                session()->flash('risk_result', $risk);
                // Persistir en el perfil para ver en el Perfil de forma solo lectura
                try {
                    $profile = json_decode($user->profile ?? '{}', true) ?: [];
                    $profile['risk_result'] = $risk;
                    $profile['risk_timestamp'] = now()->toIso8601String();
                    $user->profile = json_encode($profile);
                    $user->save();
                } catch (\Throwable $e) {
                    // Si falla la persistencia, continuar sin bloquear el registro
                }
            } else {
                session()->flash('risk_error', 'API no disponible (' . $response->status() . ')');
            }
        } catch (\Throwable $e) {
            session()->flash('risk_error', 'Error llamando a API: ' . $e->getMessage());
        }

        Auth::login($user);
        return redirect()->route('profile.risk.show')->with('success', 'Registro exitoso.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Sesión cerrada.');
    }
}
