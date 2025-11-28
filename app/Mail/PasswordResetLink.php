<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetLink extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $plainToken;

    public function __construct(User $user, string $plainToken)
    {
        $this->user = $user;
        $this->plainToken = $plainToken;
    }

    public function build()
    {
        $url = route('password.reset', ['token' => $this->plainToken]) . '?email=' . urlencode($this->user->email);
        return $this->subject('Recuperación de contraseña SIAC')
            ->view('emails.password-reset')
            ->with(['url' => $url, 'user' => $this->user]);
    }
}
