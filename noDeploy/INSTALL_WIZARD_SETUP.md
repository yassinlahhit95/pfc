# Asistente de instalación (`install/`) — guía paso a paso

Esta guía explica cómo y cuándo usar el asistente de instalación que vive en
`install/`. **No es para tu instancia actual** (`aulapro.yassin.agency`) —
esa ya tiene una cuenta de director y el asistente se bloquea solo en cuanto
detecta eso. Es para poner en marcha una **instancia nueva y separada** de
AulaPro: un cliente/centro nuevo, con su propio hosting/subdominio y su
propia base de datos, partiendo de cero.

---

## 0. Qué es y cómo se protege

Es una máquina de estados de 5 pasos (`$_SESSION['install_step']`), cada uno
en `install/steps/N_nombre.php`. No se puede saltar pasos: cada uno solo
avanza al siguiente si el anterior se completó con éxito.

Antes de mostrar cualquier pantalla, comprueba dos candados independientes
(`install/lib/helpers.php::installIsLocked()`) — si cualquiera de los dos se
cumple, redirige directamente a `/vistas/login.php` sin mostrar nada del
asistente:

1. Existe el fichero `install/.installed` (se crea al completar el paso 5).
2. La tabla `directores` ya tiene ≥1 fila.

El segundo candado es un cinturón de seguridad adicional: aunque el fichero
`.installed` se pierda, se borre o no llegue a subirse al servidor, el
asistente se sigue bloqueando solo con que ya exista un director en la base
de datos.

---

## 1. Antes de empezar — qué necesitas preparado

- El código completo subido al servidor/subdominio nuevo, **incluyendo la
  carpeta `install/`** (se sube igual que cualquier otro despliegue normal).
- Una base de datos MySQL/MariaDB **vacía o desechable** — el paso 2 importa
  el esquema completo (`noDeploy/database.sql`) sin preguntar; no apuntes
  esto a una base de datos con tablas que ya te importen.
- Credenciales de conexión a esa base de datos (host, usuario, contraseña,
  nombre de la BD).
- Un email y contraseña (mínimo 8 caracteres) para la primera cuenta de
  administrador/director.
- Datos básicos del centro a mano: nombre, ciudad, curso escolar actual,
  email de contacto.
- Que **no exista ya** un `.env` con una base de datos configurada — si el
  asistente detecta que ya hay un director, no te dejará entrar (ver
  sección 0).

---

## 2. Los 5 pasos

Visita `https://<dominio-nuevo>/install/` — al no existir `.env` ni ningún
director todavía, el asistente te dejará entrar directamente en el paso 1.

### Paso 1 — Entorno
Comprobación automática de versión de PHP y extensiones necesarias. No hay
nada que rellenar — si algo falta, te lo indica ahí mismo y no puedes
avanzar hasta corregirlo en el servidor.

### Paso 2 — Base de datos
El único paso que toca de verdad el sistema de ficheros y la base de datos
antes de crear ninguna cuenta:
- **Host**, **Usuario**, **Contraseña**, **Nombre de la base de datos**
- **URL pública del centro** (opcional aquí — se puede rellenar más
  adelante)

Al enviar el formulario: prueba la conexión → importa el esquema completo
desde `noDeploy/database.sql` → escribe `.env` con esos datos (más
`APP_ENV=production`). Puede tardar unos segundos en importar — no cierres
la página mientras se procesa.

### Paso 3 — Cuenta de administrador
- **Nombre**, **Email**, **Contraseña** (mínimo 8 caracteres), confirmación
  de contraseña

Crea la primera cuenta de director/administrador del centro.

### Paso 4 — Datos del centro
- **Nombre del centro**, **Ciudad**, **Curso escolar** (pre-rellenado con el
  actual), **Email del centro**

Rellena la fila inicial de `configuracion_centro`.

### Paso 5 — Funciones
Checkboxes para elegir qué módulos activar (chat, pagos, inventario,
anuncios, eventos, retos, mensajería, etc.) — todos marcados por defecto.
Al completar este paso:
- Se guarda la configuración de funciones elegida.
- Se llama a `lockInstall()` → se crea `install/.installed` → el asistente
  queda bloqueado para siempre (hasta que, en su caso, se borre esa fila de
  `directores` Y el fichero, algo que normalmente nunca hace falta hacer).

Al terminar, redirige a `/vistas/login.php` con la cuenta de administrador
ya lista para entrar.

---

## 3. Después de instalar

- `.env` es ahora el único mecanismo de configuración — no hace falta tocar
  `config/db.php` en ningún caso.
- Si esta nueva instancia también va a usar almacenamiento en Cloudflare R2,
  sigue `noDeploy/CLOUDFLARE_R2_SETUP.md` a continuación (es un paso
  totalmente independiente y opcional, `.env` sigue funcionando sin R2
  configurado).
- Considera, como precaución extra opcional (no obligatoria — el candado de
  `directores` ya protege esto), borrar la carpeta `install/` del servidor
  una vez confirmado que todo funciona.

---

## Solución de problemas

- **El asistente redirige directo a login y no me deja entrar** — ya hay un
  director en la base de datos, o ya existe `install/.installed`. Es el
  comportamiento esperado en cualquier instancia ya instalada (incluida
  `aulapro.yassin.agency`).
- **Falla la importación del esquema en el paso 2** — revisa que el usuario
  de la base de datos tenga permisos para crear tablas (`CREATE`), no solo
  `SELECT`/`INSERT`.
- **Quiero reinstalar desde cero en la misma base de datos** — vacía o borra
  la tabla `directores` (o la base de datos entera) y borra
  `install/.installed` manualmente; solo entonces el asistente se
  desbloqueará de nuevo. Hazlo únicamente si estás seguro de que esa
  instancia no tiene datos reales que perder.
