@extends('layouts.main')
@section('title','Requerimientos')
@section('content')
<div class="card fade-in">
    <h1 style="margin-top:0; color:var(--secondary);">Documento de Requerimientos SIAC v1.1</h1>
    <p><strong>Propósito:</strong> Incrementar seguridad y comodidad del conductor mediante monitoreo integral y alertas multi-dispositivo.</p>
    <div class="section">
        <h3>Ámbito Tecnológico</h3>
        <p>IoT, automotriz, wearables (Wear OS). Integración: Vehículo ↔ Servidor IoT ↔ App/PWA ↔ Smartwatch.</p>
    </div>
    <div class="section">
        <h3>Restricciones</h3>
        <ul style="margin:0; padding-left:1.2rem;">
            <li>Solo smartwatches Wear OS.</li>
            <li>Dependencia de conectividad 4G/5G/Wi-Fi.</li>
            <li>Compatibilidad sensores/cámaras con unidad central IoT.</li>
        </ul>
    </div>
    <div class="section">
        <h3>Supuestos</h3>
        <ul style="margin:0; padding-left:1.2rem;">
            <li>Conductor con Android y conexión estable.</li>
            <li>Smartwatch sincronizado previamente.</li>
            <li>Vehículo con sensores/cámaras compatibles.</li>
        </ul>
    </div>
    <div class="section">
        <h3>Funcionalidades Críticas</h3>
        <table>
            <thead><tr><th>Tipo</th><th>Descripción</th></tr></thead>
            <tbody>
                <tr><td>Colisión</td><td>Alertas inmediatas vehículo → smartwatch/app</td></tr>
                <tr><td>Fatiga</td><td>Detección visual y vibración preventiva</td></tr>
                <tr><td>Estado Vehículo</td><td>Resumen rápido en wearable y panel</td></tr>
            </tbody>
        </table>
    </div>
    <div class="section">
        <h3>Escalabilidad & Seguridad</h3>
        <p>Escalado horizontal con contenedores y balanceador. Cifrado TLS, hashing seguro, segmentación de datos y monitoreo de disponibilidad.</p>
    </div>
    <p style="margin-top:2rem; font-size:.75rem; color:var(--text-dim);">Versión 1.1 • Última actualización: {{ date('d/m/Y') }}</p>
</div>
@endsection