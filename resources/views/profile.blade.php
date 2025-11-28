@extends('layouts.main')
@section('title','Perfil')
@section('content')
<div class="card profile-box fade-in" style="position:relative;">
    <h1 style="margin-top:0; color:var(--primary-alt); display:flex; align-items:center; gap:.6rem;">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3 9-8 10-5-1-8-5-8-10V6l8-4z"/><path d="M10 12l2 2 4-4"/><circle cx="12" cy="12" r="3"/></svg>
        Panel SIAC
    </h1>
    <p style="margin:.8rem 0 .4rem;"><strong>Nombre completo:</strong> {{ $user->name }} @if(!empty($user->last_name)) {{ $user->last_name }} @endif</p>
    <p style="margin:.4rem 0 .4rem;"><strong>Correo:</strong> {{ $user->email }}</p>
    @if($user->created_at)
        <p style="margin:.4rem 0 1rem;"><strong>Cuenta creada:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
    @endif
    <form action="{{ route('logout') }}" method="POST" style="margin-top:1rem;">
        @csrf
        <button class="btn">Cerrar Sesión</button>
    </form>
</div>
@endsection