<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

# Despliegue de SIAC en Render (Docker)

Este proyecto incluye configuración lista para desplegarlo en la plataforma [Render](https://render.com) usando un contenedor Docker multi-stage (Laravel + PHP-FPM + Nginx + Supervisor).

## Archivos añadidos
- `Dockerfile`: build multi-stage (Node para assets, Composer para dependencias, runtime PHP-FPM + Nginx).
- `render.yaml`: definición del servicio web (plan free) y variables de entorno.
- `deploy/nginx.conf`: configuración de Nginx con `try_files` para rutas Laravel.
- `deploy/supervisord.conf`: lanza procesos `php-fpm` y `nginx`.
- `deploy/entrypoint.sh`: realiza caché de config/rutas/vistas si `APP_KEY` está definido.
- `.dockerignore`: reduce el contexto de build.

## Variables de entorno mínimas
Configura en Render (Dashboard → Environment):
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://<tu-servicio>.onrender.com`
- `APP_KEY` (genera local con `php artisan key:generate` y pega el valor)  
- `LOG_CHANNEL=stderr`
- `SESSION_DRIVER=cookie` (evita sesiones en disco efímero)
- `CACHE_DRIVER=file` (o `redis` si añades un servicio externo)
- `QUEUE_CONNECTION=sync` (cambia a `database` si activas un worker)

Base de datos (ejemplo MySQL):
- `DB_CONNECTION=mysql`
- `DB_HOST=...`
- `DB_PORT=3306`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`

MongoDB Atlas (alternativa):
- `MONGODB_URI=mongodb+srv://usuario:pass@cluster/db?retryWrites=true&w=majority`

## Pasos de despliegue rápido
1. Conecta el repositorio en Render y elige “Docker” (Render detecta `Dockerfile`).
2. Revisa/añade las variables arriba.
3. Primer deploy: Render generará la imagen; observa logs de build (Composer / Node / cache).  
4. Una vez estable, elimina `generateValue: true` de `APP_KEY` en `render.yaml` si deseas fijarlo.
5. Healthcheck `/` debe responder 200 (ver en Dashboard).

## Comandos locales de verificación
```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan config:cache && php artisan route:cache && php artisan view:cache
docker build -t siac-local .
docker run -p 8080:80 -e APP_KEY="base64:TU_LLAVE" -e APP_ENV=production siac-local
```
Visita `http://localhost:8080` para validar.

## Worker de colas opcional
Para procesar jobs en segundo plano:
1. Cambia `QUEUE_CONNECTION=database` y ejecuta migración `php artisan queue:table && php artisan migrate`.
2. Descomenta la sección de servicio `worker` en `render.yaml` y ajusta `startCommand`:  
	`php artisan queue:work --sleep=3 --tries=3 --timeout=90`
3. Sube cambios y crea el nuevo servicio en Render.

## Buenas prácticas
- Evita escribir archivos persistentes en `storage/app`: usa S3/Cloudinary para uploads.
- Mantén `APP_DEBUG=false` en producción.
- Usa HTTPS (Render ya provee certificado por defecto).
- Re-genera assets tras cambios en `resources/` (Render auto-deploy con push).

## Actualizaciones futuras
- Añadir Redis para cache/colas.
- Implementar estrategia de CI para tests antes de deploy.
- Integrar monitoreo (Better Stack / Sentry) vía nuevas env vars.

---

## Migración de Render a VPS con Laravel Forge (o manual)

Esta guía te ayuda a pasar de un despliegue fácil en Render a un servidor propio (VPS) para mayor control, rendimiento y costos más previsibles.

### 1. Cuándo migrar
- Latencia p95 > 400ms constante.
- Necesidad de múltiples workers de cola o websockets persistentes.
- Requerimientos de almacenamiento/archivos persistentes locales.
- Necesidad de tunear PHP-FPM/OpCache, límites de memoria, supervisión avanzada.

### 2. Selección de servidor
- **Hetzner**: Excelente costo/rendimiento (ej. CX22/CX32).  
- **DigitalOcean**: Simplicidad y comunidad grande.  
- **Contabo / Vultr**: Alternativas económicas (vigilar IO).  
Recomendado iniciar con 2 vCPU / 4 GB RAM (producción pequeña) y escalar según métricas.

### 3. Opción A: Usar Laravel Forge
Forge automatiza instalación de: Nginx, PHP, certificados SSL, Firewall, Daemons (supervisor) y despliegue Git.
Pasos:
1. Crear servidor desde Forge (selecciona proveedor y tamaño).
2. Conectar repositorio Git (GitHub) y configurar branch `main` para deploy automático.
3. Añadir variables `.env` (copiar desde Render; NO copiar llaves secretas expiradas).  
4. Ejecutar en la sección de Deploy Script (Forge genera base; añade):
	```bash
	composer install --no-dev --optimize-autoloader
	php artisan migrate --force
	npm ci && npm run build
	php artisan config:cache && php artisan route:cache && php artisan view:cache
	```
5. Activar SSL (Let’s Encrypt) con tu dominio.
6. Configurar Daemon para colas: `php artisan queue:work --sleep=3 --tries=3 --timeout=90`.
7. (Opcional) Configurar Redis desde Forge si usarás cache/colas.

### 4. Opción B: Manual (sin Forge)
1. Instala paquetes: `nginx php8.2-fpm php8.2-cli php8.2-mbstring php8.2-zip php8.2-intl php8.2-pdo php8.2-mysql redis git supervisor certbot` (nombres pueden variar según distro).  
2. Clona el repo: `/var/www/siac`.  
3. Crea usuario `www-data` propietario de la carpeta.  
4. Ejecuta:  
	```bash
	composer install --no-dev --optimize-autoloader
	npm ci && npm run build
	cp .env.example .env  # Ajusta claves
	php artisan key:generate
	php artisan migrate --force
	php artisan config:cache && php artisan route:cache && php artisan view:cache
	```
5. Configura Nginx (bloque básico):
	```nginx
	server {
		 listen 80;
		 server_name tu-dominio.com;
		 root /var/www/siac/public;
		 index index.php;
		 location / { try_files $uri $uri/ /index.php?$query_string; }
		 location ~ \.php$ {
			  include fastcgi_params;
			  fastcgi_pass unix:/run/php/php-fpm.sock; # Ajustar ruta del socket
			  fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
			  fastcgi_param DOCUMENT_ROOT $realpath_root;
		 }
		 location ~ /\.ht { deny all; }
	}
	```
6. Certificado SSL (Let’s Encrypt): `certbot --nginx -d tu-dominio.com`.
7. Supervisor (queue):
	```ini
	[program:laravel-queue]
	command=php /var/www/siac/artisan queue:work --sleep=3 --tries=3 --timeout=90
	directory=/var/www/siac
	autostart=true
autorestart=true
	redirect_stderr=true
	stdout_logfile=/var/log/laravel-queue.log
	```
8. Reinicia servicios: `systemctl restart nginx php8.2-fpm supervisor`.

### 5. Migración de datos
- Exporta base de datos desde Render (si usas externo DB válido) o desde tu proveedor actual.  
- Importa al nuevo servidor (MySQL/MariaDB/Postgres).  
- Verifica integridad: `php artisan tinker` y revisa modelos clave.

### 6. Sincronización de archivos
- Si usabas almacenamiento efímero en Render, sube cualquier contenido persistente (ej. imágenes) a un bucket S3 y ajusta `FILESYSTEM_DISK=s3`.
- Para migrar uploads existentes: descarga, sube al bucket, verifica rutas en BD.

### 7. Estrategia de corte (Cutover)
1. Baja TTL del dominio (DNS) a 300s uno o dos días antes.
2. Realiza despliegue en nuevo VPS (staging) y prueba login, registros, envío de correo (SMTP).  
3. Congela cambios en Render (modo mantenimiento) si necesitas consistencia.
4. Cambia DNS a la nueva IP del VPS.  
5. Verifica métricas y logs; mantén Render activo unas horas de respaldo (opcional).  
6. Habilita colas en nuevo entorno.  

### 8. Rollback sencillo
- Mantén copia de `.env` y dump reciente de la BD.  
- Si falla el nuevo entorno, regresa DNS al CNAME/IP de Render y restablece servicio.  
- Documenta el error antes de reintentar migración.

### 9. Optimización post-migración
- Activa OpCache settings: `opcache.enable=1`, `opcache.memory_consumption=192`, `opcache.max_accelerated_files=20000`.  
- Instala y configura Redis para sesiones y cache: `SESSION_DRIVER=redis`, `CACHE_DRIVER=redis`.  
- Añade monitoreo: UptimeRobot / Better Stack.  
- Log centralizado: Enviar a Logtail/Sentry para trazas y excepciones.

### 10. Websockets / Tiempo real (futuro)
- Usa Laravel Reverb o SOKeti (instancia separada) detrás de Nginx reverse proxy.  
- Escala horizontalmente separando workers intensivos (notificaciones) y HTTP.

### 11. Checklist rápido de migración
| Paso | Estado |
|------|--------|
| Clonar repo en VPS | ☐ |
| Configurar `.env` | ☐ |
| Instalar dependencias (Composer/NPM) | ☐ |
| Generar APP_KEY | ☐ |
| Migrar base de datos | ☐ |
| Compilar assets | ☐ |
| Cachear config/rutas/vistas | ☐ |
| Configurar Nginx + SSL | ☐ |
| Configurar Supervisor (colas) | ☐ |
| Probar endpoints clave | ☐ |
| Cambiar DNS | ☐ |
| Monitorear logs | ☐ |

### 12. Seguridad básica
- Actualiza paquetes del sistema con frecuencia (`apt update && apt upgrade -y`).
- Firewall: abre sólo puertos 22 (SSH), 80/443 (HTTP/HTTPS), base de datos si se requiere restringido.
- SSH: clave pública, deshabilita password login.
- Limita tamaño de subida de archivos: Nginx `client_max_body_size 10M;`.

---
