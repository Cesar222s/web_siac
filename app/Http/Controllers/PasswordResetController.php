<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\PasswordResetLink;

class PasswordResetController extends Controller
{
    public function requestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendLink(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email',$data['email'])->first();
        if(!$user){
            return back()->withErrors(['email'=>'No encontramos ese correo.']);
        }

        // invalidate old tokens
        PasswordResetToken::where('email',$user->email)->delete();

        $plainToken = Str::random(64);
        PasswordResetToken::create([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'expires_at' => Carbon::now()->addHour(),
        ]);

        Mail::to($user->email)->send(new PasswordResetLink($user, $plainToken));

        return back()->with('success','Se envió el enlace de recuperación si el correo existe.');
    }

    public function resetForm(string $token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = PasswordResetToken::where('email',$data['email'])->first();
        if(!$record || Carbon::now()->gt($record->expires_at) || !Hash::check($data['token'],$record->token)){
            return back()->withErrors(['email'=>'Token inválido o expirado.']);
        }

        $user = User::where('email',$data['email'])->first();
        if(!$user){
            return back()->withErrors(['email'=>'Usuario no encontrado.']);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        PasswordResetToken::where('email',$data['email'])->delete();

        Auth::login($user);
        return redirect()->route('profile')->with('success','Contraseña actualizada.');
    }
}
