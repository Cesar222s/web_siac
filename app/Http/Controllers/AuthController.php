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
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',      // Al menos una mayúscula
                'regex:/[0-9]/',      // Al menos un número
            ],
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);
        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        return redirect()->route('profile')->with('success', 'Registro exitoso.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Sesión cerrada.');
    }
}
