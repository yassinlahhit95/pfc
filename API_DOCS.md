# AulaPro API v1 — documentación

API REST para la app móvil, bajo `/api/v1/`. Sin sesiones — autenticación
por token Bearer (`api_tokens`, 30 días de validez). Todos los endpoints
devuelven JSON.

> Hay un segundo API, `api/admin.php`, que **no** es esta API — es un canal
> interno de control SaaS (activar/suspender instancia, licencia, feature
> flags), autenticado con API key + firma HMAC, usado por el sistema de
> gestión del SaaS, no por la app móvil de los usuarios. Ver la sección
> aparte al final de este documento.

---

## Autenticación

### `POST /api/v1/auth.php` — Login

```
POST /api/v1/auth.php
Content-Type: application/json

{
  "email": "usuario@aulapro.com",
  "password": "123456",
  "role": "estudiante",       // opcional — restringe la búsqueda a un rol
  "device_info": "iPhone 15"  // opcional — solo informativo, máx 200 chars
}
```

`role` acepta: `estudiante`, `profesor`, `director`, `tutor`. Si se omite,
prueba los 4 tipos hasta encontrar coincidencia de email+contraseña.

**Respuesta 201:**
```json
{
  "ok": true,
  "token": "a1b2c3...(64 hex)",
  "token_type": "Bearer",
  "expires_at": "2026-08-21 12:00:00",
  "user_type": "estudiante",
  "user_id": 123,
  "must_change_password": true
}
```
`must_change_password` solo aparece (en `true`) para tutores/secretarias con
contraseña temporal pendiente de cambio.

**Errores:**
| HTTP | `code` | Motivo |
|---|---|---|
| 400 | `validation` | Falta `email` o `password` |
| 401 | `invalid_credentials` | Email/contraseña incorrectos |
| 429 | `rate_limited` | Más de 10 intentos desde la misma IP en 15 min |
| 429 | `account_locked` | Cuenta bloqueada por intentos fallidos repetidos |

### `DELETE /api/v1/auth.php` — Logout

```
DELETE /api/v1/auth.php
Authorization: Bearer <token>
```

Revoca el token actual. Siempre responde `200 { "ok": true, "message": "..." }`,
incluso si el token ya no era válido (logout es idempotente).

### Cabecera de autenticación (resto de endpoints)

Todos los demás endpoints requieren:
```
Authorization: Bearer <token de 64 caracteres hexadecimales>
```

**Errores comunes de autenticación:**
| HTTP | `code` | Motivo |
|---|---|---|
| 401 | `unauthenticated` | Cabecera ausente, mal formada, o token con formato inválido |
| 401 | `token_expired` | Token no encontrado o caducado — hay que hacer login de nuevo |
| 429 | `rate_limited` | Más de 120 peticiones/minuto con el mismo token |

---

## `GET /api/v1/me.php` — Perfil del usuario autenticado

```json
{
  "ok": true,
  "user_type": "estudiante",
  "profile": { "idEstudiante": 123, "nombreEstudiante": "...", "...": "..." },
  "ciclo": { "idCiclo": 4, "nombreCiclo": "...", "abreviaturaCiclo": "DAM" }
}
```

`profile` es la fila completa de la tabla del usuario, **sin** las columnas
sensibles (`password`, `fcm_token`, `pwd_changed_at`, `mfa_secret`,
`mfa_backup_codes` — nunca se envían al cliente). `ciclo` solo aparece para
estudiantes matriculados; en el resto de roles es `null`.

---

## `GET /api/v1/grades.php` — Calificaciones

El contenido depende del rol del token:

- **estudiante** → `{ "modulos": [...], "retos": [...] }` — sus propias notas
- **profesor** → `{ "modulos": [{ "idModulo", "nombreModulo", "estudiantes": [...] }] }` — notas de los módulos que imparte, agrupadas por módulo
- **tutor** → `{ "students": [{ "idEstudiante", "nombreEstudiante", "parentesco", "modulos": [...] }] }` — notas de sus estudiantes tutorizados
- **director** → `403 forbidden` (usar el panel web)

---

## `GET /api/v1/schedule.php` — Horario semanal

- **estudiante** → horario del ciclo en el que está matriculado
- **profesor** → su horario personal de docencia
- **tutor** → horario combinado de los ciclos de sus estudiantes tutorizados
- **director** → `403 forbidden`

```json
{ "ok": true, "schedule": [
  { "diaSemana": "Lunes", "horaInicio": "08:00:00", "horaFin": "09:00:00",
    "nombreModulo": "...", "nombreProfesor": "...", "codigoAula": "...", "nombreAula": "..." }
]}
```

---

## `GET /api/v1/announcements.php` — Anuncios

Query params: `limit` (1-100, por defecto 20), `offset` (por defecto 0).

Devuelve anuncios no caducados dirigidos al rol del usuario autenticado (o
`dirigidoA = 'todos'`). Los directores ven todos los anuncios sin filtrar.

```json
{ "ok": true, "announcements": [...], "limit": 20, "offset": 0 }
```

---

## `GET /api/v1/events.php` — Eventos del centro

Query params:
- `limit` (1-100, por defecto 20), `offset` (por defecto 0)
- `upcoming` — flag sin valor; si está presente, solo eventos desde hoy
- `from=YYYY-MM-DD` / `to=YYYY-MM-DD` — rango de fechas (formato validado, se ignora si no cumple `\d{4}-\d{2}-\d{2}`)

Cualquier usuario autenticado (de cualquier rol) puede ver eventos.

```json
{ "ok": true, "events": [...], "limit": 20, "offset": 0 }
```

---

## Formato de error estándar

Todos los endpoints que fallan devuelven:
```json
{ "ok": false, "error": "Mensaje legible.", "code": "codigo_corto" }
```

`code` comunes transversales: `unauthenticated`, `token_expired`,
`rate_limited`, `validation`, `not_found`, `forbidden`, `method_not_allowed`.

---

## CORS

Las apps móviles no envían `Origin` de navegador, así que el CORS está
abierto (`Access-Control-Allow-Origin: *`) — es seguro porque todos los
endpoints (salvo login) exigen igualmente un Bearer token válido; un origen
abierto no da acceso a nada sin el token.

---

## Límites de tasa

| Ámbito | Límite |
|---|---|
| Login (`POST /api/v1/auth.php`) por IP | 10 intentos / 15 min |
| Resto de endpoints, por token | 120 peticiones / min |
| Bloqueo de cuenta | vía `AccountLockout` (mismo mecanismo que el login web) |

---

## Notas para quien integre un cliente móvil

- El token dura 30 días (`V1_TOKEN_TTL_DAYS`) — no hay refresh token; cuando
  caduca, hay que volver a hacer login.
- Guardar el token de forma segura (Keychain/Keystore), nunca en texto plano
  en preferencias sin cifrar.
- Los tokens caducados de un usuario se purgan automáticamente en su
  siguiente login (y además hay una purga global de hasta 100 tokens
  caducados por cada login de cualquier usuario) — no hace falta ninguna
  tarea de mantenimiento aparte.
- No existe endpoint de registro (`sign up`) en esta API — las cuentas se
  crean siempre desde el panel web (admin/secretaría).

---

## `api/admin.php` — API interna de control SaaS (no confundir con la anterior)

Canal separado, sin relación con `/api/v1/`, usado por el sistema externo
de gestión del SaaS para controlar instancias de AulaPro (activar/suspender,
forzar feature flags, gestionar el token de licencia). No lo usa la app
móvil ni ningún cliente de usuario final.

- **Autenticación:** cabeceras `X-Api-Key`, `X-Timestamp`, `X-Signature`
  (HMAC-SHA256 de `metodo|timestamp|cuerpo` con un secreto compartido,
  configurado en `.env` como `ADMIN_API_KEY`/`ADMIN_API_SECRET`). Rechaza
  peticiones con más de 5 minutos de desfase (protección contra repetición).
- **Acciones disponibles** (parámetro `action`, por query string en GET o en
  el cuerpo JSON en POST): `health`, `stats`, `features`, `config`,
  `set_feature`, `suspend`, `activate`, `set_message`, `clear_message`,
  `lock_features`, `unlock_features`, `status`, `heartbeat`.
- Requiere que `noDeploy/migrations/006_add_saas_control_columns.sql` esté
  aplicado en la base de datos — si no, `heartbeat`/`set_message`/etc.
  devuelven error o funcionan en modo degradado según la acción.
- No documentado en detalle aquí porque es un canal interno entre sistemas,
  no una API de cara al usuario — para su contrato completo, ver
  directamente `api/admin.php`.
