# AulaPro — Sistema de Gestión Académica SaaS

[![PHP Version](https://img.shields.io/badge/php-8.3-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-proprietary-lightgrey.svg)]()

**AulaPro** es una plataforma web SaaS de gestión integral para centros de Formación Profesional. Combina admisiones, calificaciones, pagos, comunicación en tiempo real e inventario en un único panel por roles.

Software propietario — todos los derechos reservados. No se distribuye bajo licencia de código abierto.

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
- **Almacenamiento de ficheros:** Cloudflare R2 (S3-compatible, capa gratuita) para todas las subidas nuevas — TFG, recursos de aula, justificantes, comprobantes, retos, imágenes de blog/landing/ofertaCiclos. Cliente propio (`include/R2Client.php`, firma AWS SigV4 manual vía curl, sin SDK). Los ficheros ya existentes en disco local antes de la migración siguen sirviéndose desde ahí (no hay backfill) — ver `include/FileServer.php` y `R2Client::imagenUrl()`/`documentoUrl()`
- **Cifrado de datos personales:** AES-256-GCM (`include/Crypto.php`) sobre campos PII sensibles (DNI, datos de directores/FCT, secretos MFA) conforme RGPD/LOPDGDD
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

> **Recomendado:** tras `composer install`, visita `/install/` en el navegador — el asistente guiado comprueba los requisitos del servidor, conecta con la base de datos, importa el esquema, genera el `.env` y crea la primera cuenta de administrador en 5 pantallas. Los pasos manuales de aquí abajo son la alternativa sin asistente (o para producción, vía manual FTP).

### 1. Dependencias
```bash
git clone https://github.com/tu-usuario/pfc.git
cd pfc
composer install
```

### 2. Configuración de entorno
Crea `.env` a partir de `.env.example` (nunca se sube a git) — es la única vía soportada para instalaciones nuevas. `config/db.php` sigue funcionando como alternativa heredada si ya la usas en un despliegue existente, pero no la uses para una instalación nueva.

```
DB_HOST=localhost
DB_NAME=aulapro
DB_USER=root
DB_PASS=tu_password

BREVO_API_KEY=xkeysib-...
FIREBASE_PROJECT_ID=pfc1-xxxxx

# Cloudflare R2 (opcional en local — sin configurar, todo sigue sirviéndose
# desde disco local; ver .env.example para los pasos de creación del bucket)
R2_ACCOUNT_ID=
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_BUCKET_NAME=
R2_PUBLIC_URL=
```

Copia `config/service-account.json` del panel de Firebase (tampoco se sube a git).

### 3. Base de datos
Importa el esquema (instalación nueva — ya incluye todas las tablas al día):

```bash
mysql -u root -p aulapro < noDeploy/database.sql
```

La importación deja `directores` y `configuracion_centro` vacíos a propósito: entra en `/install/` y completa el asistente (comprobación de entorno → conexión ya hecha → primera cuenta de administrador → identidad del centro → funcionalidades) para crear tu propia cuenta con tu propia contraseña. No existe ningún fichero de seed aparte — el asistente es el único camino soportado para dejar la instalación lista para usar.

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

Documentación completa de request/response de cada endpoint: **[noDeploy/API_DOCS.md](noDeploy/API_DOCS.md)**.

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
                            RateLimiter, AccountLockout, CircuitBreaker, Logger,
                            R2Client (Cloudflare R2), Crypto (cifrado PII)
modelos/                  — Funciones de consulta DB (un fichero por entidad)
api/v1/                   — API REST para la app móvil
config/                   — Config.php, .env (ignorado en git)
noDeploy/database.sql     — Esquema completo y único (fuente de verdad, sin sistema de migraciones)
landing-system/           — Sitio público de aterrizaje (landing): plantillas,
                            secciones editables, temas — ver landing-system/README.md
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
- Acceso directo a `public/uploads/aula/archivos/` bloqueado por completo (`Require all denied`) — solo servible vía `controladores/aula/verArchivo.php`, que aplica control de acceso por rol antes de servir el fichero (local o R2)
- Cifrado AES-256-GCM en reposo para campos PII sensibles (RGPD/LOPDGDD) — ver `include/Crypto.php`

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
**Licencia:** Propietaria — todos los derechos reservados
