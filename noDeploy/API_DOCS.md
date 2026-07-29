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

`role` acepta: `estudiante`, `profesor`, `director`, `tutor`, `secretaria`. Si
se omite, prueba los 5 tipos hasta encontrar coincidencia de email+contraseña.

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
- **director / secretaria** → `403 forbidden` (usar el panel web)

---

## `GET /api/v1/schedule.php` — Horario semanal

- **estudiante** → horario del ciclo en el que está matriculado
- **profesor** → su horario personal de docencia
- **tutor** → horario combinado de los ciclos de sus estudiantes tutorizados
- **director / secretaria** → `403 forbidden`

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
`dirigidoA = 'todos'`). Directores y secretarías ven todos los anuncios sin filtrar.

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

## Mensajería (`reclamaciones` — hilos formales, no confundir con el chat)

Tutores no usan este sistema (`403 forbidden`) — solo tienen chat.

### `GET /api/v1/messages.php` — lista de hilos raíz
- estudiante → sus propios hilos · profesor → hilos dirigidos a él ·
  director/secretaria → todos los hilos dirigidos a dirección (secretaria
  comparte la vista de admin)

### `GET /api/v1/messages.php?id=<idRaiz>` — hilo completo (raíz + respuestas)

### `POST /api/v1/messages.php`
- Nuevo hilo: `{ "asunto", "descripcion", "idProfesor"? }` (estudiante) o
  `{ "asunto", "descripcion", "idEstudiante" }` (profesor)
- Responder: `{ "id_parent", "contenido" }` — las respuestas de secretaría se
  guardan con `emisor_rol='admin'` (proxy ya existente en el sistema web)
- Marcar leído: `{ "action": "read", "id" }`

### `GET /api/v1/messages-unread.php` — `{ "unread": N }`
Equivalente Bearer-token del polling web (`ajax/mensajes_polling.php`).
Sondear cada ~60s como mucho mientras la pestaña de mensajes esté abierta —
las push notifications cubren el caso en tiempo real.

---

## Chat directo (`chat_conversaciones`/`chat_mensajes`)

Disponible para los 5 roles. La matriz de pares permitidos vive en
`chatParEsPermitido()` (`modelos/chat.php`) — la API la reutiliza tal cual.
**Nota**: el rol `director` de la API se mapea internamente a `admin` en
las respuestas de chat (el sistema de chat es anterior a la API y usa ese
nombre).

- `GET /api/v1/chat.php?action=contacts&q=<busqueda>` → `{ contacts: [...] }`
- `GET /api/v1/chat.php?action=conversations` → `{ conversations: [...] }`
- `GET /api/v1/chat.php?action=messages&conv_id=<id>&after=<msgId>` →
  `{ messages: [...] }` — **efecto secundario**: marca como leídos los
  mensajes de la conversación al consultarlos (igual que el chat web)
- `GET /api/v1/chat.php?action=unread` → `{ unread: N }`
- `POST /api/v1/chat.php` `{ "action": "start", "target_rol", "target_id" }`
  → `{ conv_id }` (crea o reutiliza la conversación)
- `POST /api/v1/chat.php` `{ "action": "send", "conv_id", "contenido" }` →
  `{ message_id }` — límite 30 mensajes/60s por usuario (no por token)

Diseño de polling recomendado para el cliente: refresco disparado por push
+ un suelo de ~15s solo mientras una conversación está abierta en pantalla
— no el backoff adaptativo 3s-30s de la web, ya que la push nativa hace
innecesario el polling agresivo en primer plano.

---

## Aula digital (`GET`/`POST /api/v1/classroom.php`, dispatch por `action`)

Alcance móvil: ver + descargar + entregar/calificar + publicar/despublicar +
sesiones vivas + favoritos. Gestión de carpetas/archivos (crear/renombrar/
mover/versionar/papelera) se queda solo en el panel web (esa UX de gestor de
archivos por drag&drop no encaja en un móvil). Dirección y secretaría tienen
acceso de solo lectura/supervisión a **todos** los módulos (mismo permiso que
ya existe en el panel web); estudiante ve los módulos de su ciclo; profesor
ve los módulos que imparte; tutor no tiene acceso (no está contemplado en el
aula digital).

**GET:**
- `?action=modules` → `{ modules: [...] }`
- `?action=folders&idModulo=` → `{ folders: [...] }`
- `?action=files&idModulo=&idCarpeta=` (idCarpeta opcional, si se omite
  devuelve todos los archivos del módulo) → `{ files: [...] }` — para
  estudiante, cada archivo incluye `esFavorito: bool`.
- `?action=tasks&idModulo=` → `{ tasks: [...] }` — para estudiante, cada
  tarea incluye `miEntrega: { nota, estado, comentarioCalificacion, fechaEntrega }`
  o `null` si no ha entregado; para profesor incluye también las tareas en
  borrador (`publicado: 0`) con `totalEntregas`/`totalCorregidas`.
- `?action=submission&idTarea=` (solo estudiante) → `{ submission: {...} | null }`
  — la entrega propia completa (incl. `archivoEntrega`, `respuesta`, `version`).
- `?action=submissions&idTarea=` (profesor/dirección/secretaría) →
  `{ submissions: [...] }` — roster completo del ciclo con su entrega (o sin
  ella).
- `?action=sessions&idModulo=` → `{ sessions: [...] }`
- `?action=favorites` (solo estudiante) → `{ favorites: [...] }`
- `?action=download&id=<idArchivo>&token=<token>` — **el token va como
  query param, no como cabecera `Authorization`**, porque este enlace lo
  abre un visor/navegador externo, no el cliente HTTP propio de la app.
  Mismo `api_tokens` (expiración/rate-limit) que el resto de la API — no es
  un mecanismo de auth más débil, solo un transporte distinto para esta
  única acción. Sirve el fichero directamente (`Content-Disposition: attachment`
  — fuerza descarga real, no vista previa en el navegador) vía el mismo
  `FileServer.php::servirArchivo()` que usa el panel web (local heredado si
  existe, si no redirige a una URL firmada de R2).

**POST** (acción va en la query string, `?action=...`, igual que en GET —
no en el body, para que el multipart de `submit` no choque con `v1Body()`):
- `?action=submit` (multipart/form-data, solo estudiante) — `idTarea`,
  `respuesta` opcional, `archivoEntrega` opcional (PDF/DOCX/TXT, máx 20MB).
  Mismo patrón de subida que `enviarEntrega.php` en el panel web (MIME
  server-side, nombre aleatorio, R2).
- `?action=grade` (solo profesor) — `{ idEntrega, nota: 0-10, comentario? }`.
- `?action=publish` (solo profesor) — `{ idTarea }` — **alterna** el estado
  publicado/borrador (igual que `publicarTarea.php`, no fija un valor).
- `?action=create-session` (solo profesor) —
  `{ idModulo, titulo, descripcion?, fechaSesion, horaSesion, enlaceReunion?, plataforma? }`
  — mismas validaciones que `crearSesion.php` (`validarFechaHoraSesion`,
  `validarEnlaceReunion`).
- `?action=favorite` (solo estudiante) — `{ idArchivo }` — **alterna**
  favorito/no favorito, devuelve `{ favorito: 0|1 }`.

Toda acción de escritura notifica in-app (`aula_notificaciones`) y por push
(FCM) a la contraparte relevante — ver la sección de notificaciones push más
abajo para los `type` usados en cada caso.

---

## Asistencias

- `GET /api/v1/attendance.php` — registros según el rol:
  - estudiante → los suyos · tutor → los de cada hijo vinculado
    (`estudiante_tutor`) · profesor → requiere `?idModulo=` (debe impartirlo),
    `?fecha=YYYY-MM-DD` opcional; la respuesta incluye además `roster` (todos
    los alumnos del módulo, para poder pasar lista aunque aún no tengan
    registro) · director/secretaria → `403` (usar el panel web)
  - Cada fila `ausente`/`retraso` incluye `justificacion` (la más reciente,
    o `null`) — bataneado en una sola consulta, no una por fila. Si la
    justificación tiene un `archivo` adjunto, el objeto incluye también
    `archivo_url` (resuelto igual que en el panel web: local heredado si
    existe, si no URL firmada de R2).
- `POST /api/v1/attendance.php` (solo profesor) —
  `{ idModulo, fecha, registros: [{idEstudiante, estado, observacion?}] }`
  — upsert masivo (reenviar el mismo módulo+fecha actualiza, no duplica).
- `POST /api/v1/attendance-justify.php` (multipart/form-data, estudiante o
  tutor) — `idAsistencia`, `motivo`, `archivo` opcional (PDF/JPG/PNG, máx
  8MB, detección MIME server-side). Solo admite filas `ausente`/`retraso`
  sin justificación `pendiente`/`aprobada` ya existente.
- `GET /api/v1/attendance-resolve.php` (solo profesor) — justificaciones
  pendientes de sus módulos.
- `POST /api/v1/attendance-resolve.php` (solo profesor) —
  `{ idJustificacion, aprobar: bool, motivoRechazo? }` (obligatorio si se
  rechaza) — transaccional: al aprobar, `asistencias.estado` pasa a
  `justificado` automáticamente.

---

## Notificaciones push (FCM)

`POST /api/v1/fcm-token.php` — `{ token: "<fcm registration token>" }`.
Registra/actualiza el token de este dispositivo para el usuario autenticado
(cualquiera de los 5 roles). Llamar al iniciar sesión y en cada
`onTokenRefresh` del SDK de Firebase Messaging. Columna destino resuelta vía
`V1_USER_MAP` — `token_fcm` para `secretarias`, `fcm_token` para el resto
(misma asimetría que ya maneja `V1_STRIP`).

El envío real reutiliza `controladores/firebase/firebase_helper.php`
(`enviarNotificacionFirebase($token, $titulo, $mensaje, $tipo, $extra)`) —
mismo camino que ya usa el panel web para mensajería/chat/anuncios/aula
digital/TFG. El payload incluye `data.type` para que el cliente pueda hacer
deep-link al tocar la notificación:

| `type`               | Origen                                              |
|-----------------------|------------------------------------------------------|
| `chat_message`        | Chat directo (`data.conv_id`)                        |
| `message`              | Mensajería/reclamaciones (`data.idReclamacion`)      |
| `announcement`         | Anuncios (admin/secretaría)                          |
| `grade_tfg`            | Calificación de TFG                                   |
| `entrega_enviada`      | Nueva entrega de un estudiante (al profesor)          |
| `entrega_calificada`   | Entrega calificada (al estudiante)                    |
| `aula_archivo_nuevo`   | Nuevos recursos subidos a un módulo                    |
| `sesion_nueva`         | Nueva sesión viva creada                               |
| `asistencia_resuelta`  | Justificación de falta resuelta                        |

---

## Pagos e inventario

Alcance móvil de solo lectura por ahora — crear/editar pagos, dar de alta
dispositivos, etc. se queda en el panel web; prestar/devolver sí está
disponible (acción de un toque, encaja bien en móvil).

`GET /api/v1/payments.php` — la forma de la respuesta depende del rol:
- **director/secretaria** → `{ payments: [...] }` (todos), o con
  `?idCiclo=` filtrado, o con `?pending=1` → `{ pending: [...] }`
  (estudiantes con saldo pendiente)
- **estudiante** → `{ payments: [...], estado: {totalPagado, precioCiclo, restante} }`
  (sus propios pagos)
- **tutor** → `{ students: [{idEstudiante, nombreEstudiante, payments, estado}, ...] }`
  (uno por cada hijo vinculado)
- **profesor** → `403 forbidden`

Inventario (solo director/secretaria):
- `GET /api/v1/inventory.php?action=devices` → `{ devices: [...] }`
- `GET /api/v1/inventory.php?action=loans` → `{ loans: [...] }`
- `POST /api/v1/inventory.php` `{ action: "prestar", idArticulo, idEstudiante }`
- `POST /api/v1/inventory.php` `{ action: "devolver", idPrestamo }`

---

## Cuenta (todos los roles)

- `POST /api/v1/change-password.php` — `{ current_password, new_password }`.
  Exige la contraseña actual (a diferencia del flujo web de
  `must_change_password`, que confía en el login recién completado — este
  es un cambio voluntario desde una sesión ya autenticada). Misma política
  que la web (`Security::validatePassword`: 8+ caracteres, mayúscula,
  minúscula, número) y mismo hashing (bcrypt coste 12). Revoca todos los
  demás tokens de la cuenta (no el usado en esta petición), para cortar el
  acceso a cualquier token robado en cuanto el dueño real cambia su clave.
- `POST /api/v1/profile.php` — edición de datos de contacto propios, por
  lista blanca estricta por rol (nunca email/rol/id/campos administrativos):
  - estudiante: `telefonoEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante`
  - profesor: `telefonoProfesor, direccionProfesor, ciudadProfesor, codigoPostalProfesor`
  - director: `telefonoDirector, direccionDirector, ciudadDirector, codigoPostalDirector`
  - tutor: `telefonoTutor`
  - secretaria: ninguno (el esquema actual no tiene columnas de contacto
    editables para este rol, solo nombre/email/password)

---

## Admin-only endpoints

### `GET /api/v1/admin/cron-health.php` — Cron job status

Verifica el estado de los trabajos cron programados. Requiere autenticación
como administrador.

**Respuesta 200:**
```json
{
  "ok": true,
  "jobs": [
    {
      "job": "cron_backup.php",
      "status": "success",
      "last_run": "2026-07-29 03:15:42",
      "hours_ago": 2,
      "ok": true,
      "error_message": null
    },
    {
      "job": "procesar_cola_emails.php",
      "status": "success",
      "last_run": "2026-07-29 15:48:57",
      "hours_ago": 0,
      "ok": true,
      "error_message": "Procesados: 5 correos"
    }
  ]
}
```

Los trabajos se consideran `ok` si: estado es `success` Y se ejecutaron
hace menos de 25 horas.

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

## Nota técnica: cabecera `Authorization` en Apache/mod_php

En algunas configuraciones de Apache/mod_php (confirmado en Laragon local),
`$_SERVER['HTTP_AUTHORIZATION']` llega vacío aunque el cliente sí envíe la
cabecera `Authorization` — Apache la retiene antes de que PHP la vea, salvo
que el vhost tenga `CGIPassAuth On` (Apache 2.4.13+) o una regla de
`mod_rewrite` que la reexponga. `getallheaders()`/`apache_request_headers()`
sí la ven en ese mismo caso. Por eso `v1AuthHeader()` (`api/v1/_api.php`)
no confía solo en `$_SERVER['HTTP_AUTHORIZATION']`: primero prueba esa
variable y `REDIRECT_HTTP_AUTHORIZATION`, y si ambas están vacías cae a
`getallheaders()`. Todo endpoint que necesite leer la cabecera `Authorization`
directamente (fuera de `v1Auth()`, como hace el logout de `auth.php`) debe
usar `v1AuthHeader()`, nunca leer `$_SERVER['HTTP_AUTHORIZATION']` a pelo.

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
- Requiere las columnas SaaS de `configuracion_centro` (`license_token`,
  `saas_lock_features`, `saas_message`, etc. — ver `noDeploy/database.sql`)
  presentes en la base de datos — si no, `heartbeat`/`set_message`/etc.
  devuelven error o funcionan en modo degradado según la acción.
- No documentado en detalle aquí porque es un canal interno entre sistemas,
  no una API de cara al usuario — para su contrato completo, ver
  directamente `api/admin.php`.
