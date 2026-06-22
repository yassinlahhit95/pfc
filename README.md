# AulaPro — Sistema de Gestión Académica SaaS

[![PHP Version](https://img.shields.io/badge/php-8.3-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**AulaPro** es una plataforma web SaaS de gestión integral para centros de Formación Profesional. Combina admisiones, calificaciones, pagos, comunicación en tiempo real e inventario en un único panel por roles.

---

## Módulos disponibles

| Módulo | Admin | Profesor | Estudiante | Tutor |
|---|:---:|:---:|:---:|:---:|
| Admisiones / Pre-matrícula | ✓ | | | |
| Ciclos, módulos y horarios | ✓ | ✓ | | |
| Calificaciones y evaluaciones | ✓ | ✓ | ✓ | |
| Retos y proyectos | ✓ | ✓ | ✓ | |
| TFG (subida y calificación) | ✓ | ✓ | ✓ | |
| Chat en tiempo real | ✓ | ✓ | ✓ | ✓ |
| Mensajería interna | ✓ | ✓ | ✓ | |
| Pagos y recibos | ✓ | | ✓ | ✓ |
| Anuncios y eventos | ✓ | ✓ | ✓ | |
| Inventario y préstamos | ✓ | | | |
| Panel parental (tutores) | ✓ | | | ✓ |
| API REST v1 (móvil) | — | ✓ | ✓ | |
| Configuración SaaS / licencia | ✓ | | | |

Todos los módulos son activables/desactivables por el administrador desde Configuración. Los cambios se propagan a todos los usuarios en ≤ 60 s (caché APCu).

---

## Stack tecnológico

- **Backend:** PHP 8.3, MySQLi (sin ORM), sesiones PHP para autenticación
- **Frontend:** jQuery (global), Font Awesome 6, JS vanilla, CSS custom properties
- **Email:** Brevo API (transaccional + cola asíncrona)
- **Push:** Firebase Cloud Messaging v1 (FCM)
- **PDF/ZIP:** `mpdf/mpdf`, extensión `zip` de PHP
- **Caché:** APCu (feature flags compartidos entre workers) + caché de sesión (fallback)
- **Servidor local:** Laragon — Apache 2.4, PHP 8.3, MySQL — dominio `pfc.test`
- **Producción:** `aulapro.yassin.agency` — Apache en cPanel, despliegue FTP manual

---

## Requisitos del sistema

- PHP 8.3+ con extensiones: `mysqli`, `zip`, `curl`, `openssl`, `mbstring`
- APCu (`php8.3-apcu` o equivalente) — recomendado para caché compartida de feature flags
- MySQL 8.0+ / MariaDB 10.6+
- Apache 2.4 con `mod_rewrite`, `mod_headers`, `mod_expires`, `mod_deflate`
- Composer

---

## Instalación local

### 1. Dependencias
```bash
git clone https://github.com/tu-usuario/pfc.git
cd pfc
composer install
```

### 2. Configuración de entorno
Crea `config/db.php` y `.env` a partir de los ejemplos (nunca se suben a git):

```
DB_HOST=localhost
DB_NAME=aulapro
DB_USER=root
DB_PASS=tu_password

BREVO_API_KEY=xkeysib-...
FIREBASE_PROJECT_ID=pfc1-xxxxx
```

Copia `config/service-account.json` del panel de Firebase (tampoco se sube a git).

### 3. Base de datos
Importa el esquema y ejecuta las migraciones en orden:

```bash
mysql -u root -p aulapro < database.sql
mysql -u root -p aulapro < config/migrations/001_cola_emails.sql
mysql -u root -p aulapro < config/migration_perf_indexes.sql
```

### 4. Permisos
```bash
chmod -R 775 public/uploads/
chmod -R 775 logs/
```

### 5. APCu (recomendado)
```bash
# Debian/Ubuntu
sudo apt install php8.3-apcu
# Añadir al php.ini: apc.enable_cli=1
```

Sin APCu la aplicación funciona con caché por sesión (5 s). Con APCu los feature flags se comparten entre todos los workers PHP-FPM (60 s TTL).

---

## Cola de emails asíncrona

El envío masivo de notas (Enviar notas → ciclo) inserta emails en la tabla `cola_emails` en lugar de enviarlos en línea. Un cron los procesa:

```bash
# Añadir al crontab del servidor:
* * * * * php /path/to/aulapro/cron/procesar_cola_emails.php >> /path/to/logs/cron_emails.log 2>&1
```

El worker procesa 10 emails por minuto, reintenta hasta 3 veces ante fallos y marca el estado final (`enviado` / `fallido`).

---

## API REST v1 (móvil)

Endpoints bajo `/api/v1/`:

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/api/v1/auth.php` | Login → devuelve Bearer token (30 días) |
| GET | `/api/v1/me.php` | Perfil del usuario autenticado |
| GET | `/api/v1/grades.php` | Calificaciones del estudiante |
| GET | `/api/v1/schedule.php` | Horario semanal |
| GET | `/api/v1/announcements.php` | Anuncios del ciclo |
| GET | `/api/v1/events.php` | Eventos del centro |

Autenticación: `Authorization: Bearer <token>`. Rate limit: 120 req/min por token.

---

## Estructura de directorios

```
controladores/admin/      — Controladores POST de acciones de admin
controladores/profesores/ — Controladores POST de profesores
controladores/estudiantes/— Controladores POST de estudiantes
controladores/chat/       — Endpoints de chat (AJAX)
controladores/comunes/    — email_helper.php, notificaciones_grades.php
cron/                     — Scripts de cron (solo CLI/localhost)
include/                  — AdminGuard, ProfesorGuard, Security, FeatureGuard,
                            RateLimiter, AccountLockout, CircuitBreaker, Logger
modelos/                  — Funciones de consulta DB (un fichero por entidad)
api/v1/                   — API REST para la app móvil
config/                   — Config.php, .env (ignorado en git)
config/migrations/        — Migraciones SQL (ignoradas en git)
public/css/               — dashboard.css (tokens), estilo.css (contenido)
public/js/                — paginacion.js, filtros.js, toast.js, chat.js
vistas/admin/             — Vistas PHP del panel de administrador
vistas/profesores/        — Vistas del panel de profesor
vistas/estudiantes/       — Vistas del panel de estudiante
vistas/tutores/           — Vistas del panel de tutor
vistas/comunes/           — nav.php y footer.php compartidos
logs/                     — Logs de errores (ignorados en git)
```

---

## Seguridad destacada

- CSRF tokens rotantes en todos los formularios (`Security::generateCSRFToken()`)
- Rate limiting DB-backed por IP (`RateLimiter`) + por token API (`api/v1`)
- Bloqueo de cuenta por intentos fallidos (`AccountLockout`, `SELECT FOR UPDATE`)
- Circuit breaker para Brevo y FCM (`CircuitBreaker`) — abre tras 3 fallos, espera 60 s
- Upload anti-RCE: `RemoveHandler`, `RemoveType` y `FilesMatch` en `.htaccess` de uploads
- CSP + HSTS + CORP + COOP + X-Frame-Options en `.htaccess` raíz
- `Options -Indexes` en todas las carpetas
- Carpetas internas (`config/`, `include/`, `modelos/`, `logs/`, `cron/`) bloqueadas vía `.htaccess`

---

## Despliegue en producción

Despliegue manual por FTP a `aulapro.yassin.agency`. Nunca subir:
- `.env`
- `config/db.php`
- `config/service-account.json`
- `logs/`
- `vendor/` (instalar con `composer install --no-dev` en servidor)

---

**Autor:** Yassin Lahhit  
**Año:** 2026
