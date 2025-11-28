<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'email' => 'required|email',
            'asunto' => 'required|string|max:160',
            'mensaje' => 'required|string|max:1000',
        ]);

        ContactMessage::create([
            'name' => $data['nombre'],
            'email' => $data['email'],
            'subject' => $data['asunto'],
            'message' => $data['mensaje'],
        ]);

        return back()->with('success', 'Mensaje enviado correctamente.');
    }

    public function messages()
    {
        // Recuperar últimos 50 mensajes usando el modelo
        $messages = ContactMessage::orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
            
        return view('contact-messages', ['messages' => $messages]);
    }
}
