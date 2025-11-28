<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Reset Password</title></head>
<body style="font-family:Arial, sans-serif; background:#f5f8fa; padding:20px;">
    <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; padding:24px; border:1px solid #e2e8f0;">
        <h2 style="color:#0d1b2a;">Recuperación de Contraseña</h2>
        <p>Hola {{ $user->name }}, solicitaste recuperar tu contraseña. Haz clic en el botón para continuar.</p>
        <p style="text-align:center;">
            <a href="{{ $url }}" style="background:#1e3d59; color:#fff; padding:12px 20px; text-decoration:none; border-radius:6px; font-weight:bold;">Restablecer Contraseña</a>
        </p>
        <p>Este enlace expira en 1 hora. Si no solicitaste esto, ignora este correo.</p>
        <p style="font-size:12px; color:#6c7a86;">&copy; {{ date('Y') }} SIAC</p>
    </div>
</body>
</html>
