@extends('layouts.main')
@section('title','Mensajes de Contacto')
@section('content')
<div class="card fade-in" style="overflow-x:auto;">
    <h1 style="margin-top:0; color:var(--accent); display:flex; align-items:center; gap:.6rem;">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h18v13H5l-2 2V4z"/></svg>
        Mensajes Recibidos
    </h1>
    <p style="margin-top:.2rem; color:var(--text-dim); font-size:.9rem;">Últimos {{ count($messages) }} mensajes enviados desde el formulario de contacto.</p>
    <table style="width:100%; border-collapse:collapse; font-size:.8rem;">
        <thead>
            <tr style="background:var(--surface-soft);">
                <th style="text-align:left; padding:.7rem .8rem; letter-spacing:.1em; font-size:.65rem; color:var(--accent);">FECHA</th>
                <th style="text-align:left; padding:.7rem .8rem; letter-spacing:.1em; font-size:.65rem; color:var(--accent);">NOMBRE</th>
                <th style="text-align:left; padding:.7rem .8rem; letter-spacing:.1em; font-size:.65rem; color:var(--accent);">EMAIL</th>
                <th style="text-align:left; padding:.7rem .8rem; letter-spacing:.1em; font-size:.65rem; color:var(--accent);">ASUNTO</th>
                <th style="text-align:left; padding:.7rem .8rem; letter-spacing:.1em; font-size:.65rem; color:var(--accent);">MENSAJE</th>
            </tr>
        </thead>
        <tbody>
        @forelse($messages as $m)
            <tr style="border-top:1px solid var(--border);">
                <td style="padding:.55rem .8rem; white-space:nowrap;">{{ isset($m['created_at']) ? (is_object($m['created_at']) ? $m['created_at']->toDateTime()->format('d/m/Y H:i') : $m['created_at']) : '-' }}</td>
                <td style="padding:.55rem .8rem;">{{ $m['name'] ?? '-' }}</td>
                <td style="padding:.55rem .8rem;">{{ $m['email'] ?? '-' }}</td>
                <td style="padding:.55rem .8rem; font-weight:600; color:var(--accent-alt);">{{ $m['subject'] ?? '-' }}</td>
                <td style="padding:.55rem .8rem; max-width:340px;">{{ $m['message'] ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="padding:1rem .8rem; text-align:center; color:var(--text-dim);">Sin mensajes aún.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection