@extends('layouts.main')
@section('title','Restablecer Password')
@section('content')
<div class="card" style="max-width:520px; margin:0 auto;">
    <h1>Restablecer Contraseña</h1>
    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ request('email') }}" required>
            @error('email')<small class="alert-error">{{ $message }}</small>@enderror
        </div>
        <div class="group">
            <label for="password">Nueva Contraseña</label>
            <input type="password" name="password" id="password" required>
            @error('password')<small class="alert-error">{{ $message }}</small>@enderror
        </div>
        <div class="group">
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <button class="btn" style="width:100%;">Actualizar</button>
    </form>
</div>
@endsection
