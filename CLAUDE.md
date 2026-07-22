# AULAPRO — Project Rules for Claude

## Project Overview
School management SaaS — PHP 8.3 / Apache 2.4 on Laragon (local: `pfc.test`), production: `aulapro.yassin.agency`.
Manual FTP deployment — no CI/CD pipeline.

---

## Tech Stack
- **Backend:** PHP 8.3, MySQLi (no ORM), sessions for auth, 5 roles: admin, profesor, secretaría, estudiante, tutor
- **Frontend:** jQuery (globally available), Font Awesome 6, vanilla JS
- **CSS:** Custom properties from `dashboard.css`, content styles in `public/css/estilo.css`
- **JS globals loaded in every page:** `core/filtros.js`, `core/paginacion.js`, `core/toast.js`, `core/modal-borrar.js`, `core/modal-confirm.js`, `core/upload-overlay.js` (via `vistas/comunes/footer.php`)

---

## File Structure
```
controladores/admin/       ← admin action controllers (POST handlers)
controladores/profesores/  ← professor action controllers
controladores/secretaria/  ← secretaría action controllers
controladores/estudiantes/ ← student action controllers
controladores/comunes/     ← controllers shared across roles (not role-namespaced)
include/                   ← AdminGuard.php, ProfesorGuard.php, SecretariaGuard.php,
                              EstudianteGuard.php, TutorGuard.php, BotGuard.php,
                              SuspensionGuard.php, FeatureGuard.php, Security.php,
                              HtmlSanitizer.php, ImageOptimizer.php
modelos/                   ← DB query functions (one file per entity)
vistas/admin/               vistas/profesores/       vistas/secretaria/
vistas/estudiantes/         vistas/tutores/           ← per-role views (PHP templates)
vistas/comunes/            ← shared nav.php + footer.php + chat_widget.php
public/css/estilo.css      ← content styles (root-level, loaded on every page, 23 numbered sections)
public/css/dashboard.css   ← design tokens/custom properties (root-level, loaded on every page)
public/css/features/       ← one file per feature (chat.css, mensajes.css, horario-admin.css, ...)
public/js/core/            ← globals used across pages (filtros.js, paginacion.js, toast.js,
                              modal-borrar.js, modal-confirm.js, upload-overlay.js, menu-contextual.js,
                              dashboard-shell.js, analytics.js, aula-digital.js)
public/js/features/        ← one file per feature (chat.js, mensajes.js, horario.js, blog-editor.js, ...)
public/js/firebase/        ← Firebase web SDK config + notifications UI
landing-system/            ← public landing page: templates, sections, themes — see own section below
noDeploy/database.sql      ← schema source of truth (see DB Migrations below)
```

Note: `core/analytics.js` and `features/aula-recursos.js` compute their own root path via `document.currentScript`/`import.meta.url` and a hardcoded `../../../` — they must stay exactly 3 directories below the project root (i.e. directly under `public/js/core/` or `public/js/features/`, not nested deeper) or that path math breaks.

---

## Security — HARD RULES

- **NEVER commit `.env` or `config/db.php`** — they contain production credentials. Both are in `.gitignore`.
- **NEVER commit `config/service-account.json`** — Firebase service account.
- Every view must start with the matching role guard: `AdminGuard.php`, `ProfesorGuard.php`, `SecretariaGuard.php`, `EstudianteGuard.php`, or `TutorGuard.php` (`require_once __DIR__ . "/../../../include/XxxGuard.php";`). Guards already validate CSRF on POST and handle the AJAX-vs-redirect response split — don't re-check the session yourself.
- Feature-gated pages/endpoints also need `FeatureGuard`, and the two methods are **not interchangeable**:
  - `FeatureGuard::requirePage('feature_xxx')` — full page loads; renders `vistas/feature_disabled.php` and exits.
  - `FeatureGuard::requireJson('feature_xxx')` — AJAX-only endpoints (delete, upload, any handler that only ever returns JSON); returns a JSON 403 and exits. Using `requirePage` here would return raw HTML to a `fetch()` call.
- Always escape output: `Security::escapeHtml($value)` — never raw echo user data. The one exception is content that was sanitized with `HtmlSanitizer::clean()` at save time (see below) — comment inline why it's safe, e.g. `/* ya saneado con HtmlSanitizer al guardar */`.
- CSRF tokens: `Security::generateCSRFToken()` in forms, validate in controllers with `Security::validateCSRFToken()`.
- **`Security::validateCSRFToken($token = null, $rotate = true)` defaults to rotating** (deletes `$_SESSION['csrf_token']` on success, forcing a fresh one on next page load). That's correct for a classic one-shot POST-then-redirect controller, but **wrong** for any endpoint called repeatedly via AJAX from a page that never reloads (e.g. the landing builder — reorder, save section, save ajustes, toggle visibility, publish, upload — all fire against one shared hidden `#lb-csrf` value for the entire page session). Using the default there means the *first* successful action silently deletes the token, and every action after it fails with "Token de seguridad inválido" even though nothing is actually wrong — this exact bug shipped across nearly every landing-builder controller and was only caught once a real click sequence hit it. Guards (`AdminGuard.php` etc.) already validate CSRF once per request with `rotate=false` specifically for this reason; a controller re-validating on top of that must also pass `Security::validateCSRFToken(null, false)`, matching `borrar.php`/`subir_imagen_contenido.php` in the blog/ofertaCiclos comunes controllers. Only use the rotating default for endpoints that redirect to a freshly-rendered page afterward.
- Integer IDs: always cast with `(int)` before using in queries.
- **Rich-text / WYSIWYG fields** (anything edited via `blog-editor.js` or similar) must go through `HtmlSanitizer::clean()` (HTMLPurifier-backed, whitelists safe tags + YouTube/Vimeo iframe embeds) before being stored. Never store raw `contenteditable` HTML.
- **Public "get by slug/id" queries must filter on published state server-side** (`WHERE publicado = 1`), e.g. `obtenerPostPorSlug()`, `obtenerCicloLandingPorSlug()`. Never rely on a slug/id being hard-to-guess as the only protection — that's an IDOR waiting to happen the moment someone shares a draft link.
- **File uploads**: MIME allow-list checked via `mime_content_type()` (not the client-supplied extension/MIME), random filename (`bin2hex(random_bytes(6))`), run through `ImageOptimizer::optimize()`, and delete the old file with `@unlink()` when replacing/removing. Each content type gets its own `public/uploads/<tipo>/` directory (`blog/`, `landing/`, `ofertaCiclos/`, ...) — don't reuse another feature's folder.
- **Audit logging must match the actor's actual role** — `registrarAccion()` (`modelos/log.php`) logs against `$_SESSION['idAdmin']`; a secretaría action logged this way records `idAdmin = NULL` (silently unattributed). Secretaría controllers must call `registrarAccionSecretaria()` instead, which writes to `historial_secretarias` keyed on `$_SESSION['idSecretaria']`. Pick the right one per the file's own role, not by copy-pasting from an admin controller.

---

## Avoiding duplication across roles (admin + secretaría)

Several features (blog, catálogo de ciclos) are usable by both admin and secretaría with **identical logic**, differing only in which `Guard` runs and which role-prefixed path a redirect points at. The correct pattern — do **not** hand-duplicate the controller/view across both role folders:

**Controllers**: put the real logic in `controladores/comunes/<feature>/<accion>_impl.php`, which expects a `$rolBase` variable (`'admin'` | `'secretaria'`) to already be set for building redirect paths. Each role gets a thin (~5 line) wrapper at `controladores/{admin,secretaria}/<feature>/<accion>.php`:
```php
<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";       // or SecretariaGuard.php
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_xxx');                        // or requireJson for AJAX-only
$rolBase = 'admin';                                               // or 'secretaria'
require __DIR__ . '/../../comunes/<feature>/<accion>_impl.php';
```

**Views**: same idea — shared body at `vistas/comunes/<feature>/_<pagina>.php`, thin per-role wrapper handles the guard, data fetch, and the role's own `nav.php` (nav differs meaningfully per role — it can't be shared), then `require`s the shared body. `vistas/admin/comunes/footer.php` already does exactly this (`<?php require __DIR__ . '/../../comunes/footer.php'; ?>`) — follow that model.

Shared helper functions (e.g. image-upload helpers) live in the `admin` copy; the `secretaria` copy is a one-line shim: `require_once __DIR__ . '/../../admin/<feature>/insertar_helpers.php';`.

---

## DB Migrations

`noDeploy/database.sql` is the schema source of truth (the old `migrate_db.php` incremental-migration file was removed — don't reference it in new code or comments). New tables/columns for a **fresh install** go directly into the relevant `CREATE TABLE` statement in `noDeploy/database.sql`.

For an **existing** database (production, or any dev DB created before the change), also add a standalone SQL file under `noDeploy/migrations/`, named with the next incremented 3-digit number (e.g. `noDeploy/migrations/007_whatever_you_added.sql` following `006_add_saas_control_columns.sql`) containing just the `ALTER TABLE`/`CREATE TABLE IF NOT EXISTS` statements needed to bring an existing schema up to date. All prior reference-only migration SQL (including what used to live under `landing-system/sql/`) has been consolidated here under this same numbering — don't scatter new ones elsewhere. These files are never executed automatically; they're applied manually, in numeric order, and should say up top which existing-DB scenario they're for. `noDeploy/migrations/aplicar_todas_produccion.sql` is a maintained concatenation of every numbered migration in order — update it too whenever you add a new one, so it stays a valid single-command way to bring an existing DB fully up to date.

Never use a raw `CREATE TABLE IF NOT EXISTS` inline in a model file on every request (this bit us once already — `registrarAccionSecretaria()` in `modelos/log.php` did this for `historial_secretarias` long after that table was already guaranteed by the schema, silently paying a metadata-query cost on every secretaría action for no reason). If a table might not exist yet on some deployments, that's what a `noDeploy/migrations/*.sql` file is for, not per-request runtime DDL.

**Naming**: if a new table is conceptually close to an existing one (e.g. a landing/marketing table vs. an internal academic table), give it a distinct table name **and** a distinct primary key name up front (`landing_ciclos.idLandingCiclo`, not `idCiclo`) — don't let two unrelated entities share an ambiguous name/column just because they're topically similar. It's cheap to get right on day one and expensive to rename later.

**Slugs**: reuse the transliteration + uniqueness-loop pattern already established (`generarSlugBlog` in `modelos/blog.php`, `generarSlugCiclo` in `modelos/landingCiclos.php`) for any new sluggable content type — lowercase, strip accents, `[^a-z0-9]+` → `-`, then loop-query for uniqueness against the table, excluding the current row's own id on update.

---

## PHP Patterns

### View variables
- Admin views: `$titulo_pagina` and `$seccion`
- Profesor views: `$tituloDelPagina` and `$seccionActual`
- Flash messages: `$errores` (array or string) and `$exito` (string) — read from `$_SESSION`, unset immediately

### Controller pattern
```php
require_once __DIR__ . "/../../../include/AdminGuard.php";
// detect AJAX:
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
// ... do work, set $_SESSION['exito'] or $_SESSION['errores'] ...
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/xxx/verXxx.php");
exit;
```

---

## CSS Rules

- **Never hardcode hex colors** — always use CSS custom properties: `var(--accent)`, `var(--surface)`, `var(--bg)`, `var(--text)`, `var(--dim)`, `var(--mut)`, `var(--border)`, `var(--border-2)`, `var(--surface-2)`, `var(--shadow-sm/md/lg)`
- Dark mode is handled automatically via `[data-theme="dark"]` overriding the same properties — no separate dark mode code needed
- `estilo.css` has 23 numbered sections — add new styles to the relevant section, never append at random
- `.panel` = main card container; `.cabecera` = page header with h1 + action buttons
- `.boton-primario` / `.boton-secundario` / `.boton-peligro` — standard button classes
- `.formulario` = CSS grid form (`repeat(auto-fill, minmax(280px, 1fr))`); `.campo.ancho-total` = full-width field
- `.tabla-datos` + `.contenedor-tabla` = standard table pattern; always give table an `id` for pagination
- **Equal-specificity cascade trap**: a later rule with the same specificity always wins, even across unrelated concerns. Concretely: `.lp-anim.visto { transform: none }` (scroll-reveal reset) and `.lp-tarjeta:hover { transform: translateY(-4px) }` are both 2-selector rules — the one later in the file wins outright, silently cancelling the other's effect on any element that has both classes. When adding a "resets state" rule that a component's own `:hover`/`:focus` needs to keep winning against, scope the reset with `:not(:hover)` rather than trusting source order.

---

## JS Patterns

### Pagination
Every list table must have `iniciarPaginacion('tableId', 15)` after `footer.php`. After custom filters, call `resetearPaginacion('tableId')`.

### Delete modal
List page delete links use `data-modal-borrar` attributes — never `onclick="confirm()"` or links to a `borrarXxx.php` page:
```html
<a class="recurso-menu-item peligro" href="#"
   data-modal-borrar
   data-id="<?= (int)$entity['idEntity'] ?>"
   data-tipo="EntityType"
   data-nombre="<?= Security::escapeHtml($entity['nombre']) ?>"
   data-extra="<?= Security::escapeHtml($entity['abreviatura'] ?? '') ?>"
   data-url="/controladores/admin/entity/borrar.php"
   data-campo="idEntity">
   <i class="fas fa-trash"></i> Eliminar
</a>
```

### Filters
Use `filtrarTabla(inputId, tableId)` or `filtrarTablaMulti(tableId)` from `filtros.js`. Custom filter functions must call `resetearPaginacion(tableId)` at the end.

### Search/filter inputs — no browser autocomplete
**Every single "buscar"/filter `<input>` — topbar search, table filters, chat contact/conversation search, all of them — must carry the full hardened set below, not just `autocomplete="off"`.** `autocomplete="off"` alone does *not* stop Chrome/Edge/password managers on a field it recognizes as a search box (by `id`, `placeholder`, or `type="search"`) — it silently re-enables suggestions. This was tried field-by-field with plain `autocomplete="off"` across ~28 files and the autofill dropdown kept coming back on whichever pages hadn't been touched yet, because the weaker attribute doesn't actually work on this class of input. There is no "simple case" exception anymore — always use:
```html
<input type="search" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
       data-lpignore="true" data-1p-ignore="true" data-form-type="other">
```
These are live client-side filters, never form fields meant to be re-submitted, so suggestion dropdowns only ever get in the way — there's no downside to always applying the full set.

### Toast notifications
`Toast.show('message', 'success'|'error'|'info')` — always guard with `if (window.Toast)`.

### Upload overlay
`UploadOverlay.show('mensaje')` / `.hide()` (`core/upload-overlay.js`) — blocks the page and blurs the background while a file upload is in flight (async AJAX uploads and full multipart form submits alike). Always guard with `if (window.UploadOverlay)`. Wired into `blog-editor.js`'s image-insert upload and its form-submit handler already; reuse it for any new async or multipart file upload rather than a bare Toast, which doesn't block interaction.

---

## UI Patterns

### List page structure
```
cabecera (h1 + action buttons)
→ panel.margen-abajo (filters)
→ panel (table + pagination)
```

### Action menus (⋮)
```html
<div class="recurso-menu-wrap">
  <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
  <div class="recurso-menu">
    <a class="recurso-menu-item" href="...">...</a>
    <div class="recurso-menu-sep"></div>
    <a class="recurso-menu-item peligro" href="#" data-modal-borrar ...>Eliminar</a>
  </div>
</div>
```

### Empty states
Three deliberate patterns exist for three different contexts — pick the one that matches the shape of what's empty, don't force everything into one:
- **`.panel-vacio`** (with `.panel-vacio-icono`, `.panel-vacio-titulo`, `.panel-vacio-desc`) — replaces an entire panel/section when there's nothing to show at all (no table, no headers). Use when the list itself is the whole panel.
- **`.vacio`** — a single `<td colspan="N" class="vacio">Mensaje</td>` inside `<tbody>`, used when the table structure (headers, filters) should stay visible even with zero rows. This is actually the more common pattern across the app (list pages under a persistent table/filter bar) — don't treat it as a deviation from `.panel-vacio`, it's the right choice whenever a table shell already exists above it.
- **`.inbox-empty`** (with `.inbox-empty-ico`) — mensajería-specific, used identically across all 4 role variants of `mensajes/lista.php`. Don't reuse outside that feature; it's styled in `public/css/features/mensajes.css`, not `estilo.css`.

### Status chips
Use `<span class="texto-estado azul|verde|rojo|gris|naranja">text</span>`

---

## `landing-system/` — public landing page

Self-contained subsystem for the center's public site (`index.php` at project root, `/vistas/blog.php`, `/vistas/ciclos.php`, `/vistas/contacto.php`). Its own README (`landing-system/README.md`) covers the day-to-day workflow for adding a template/section; the points below are things that aren't obvious from reading that file alone.

- `landing-system/engine/secciones.php` is the **single source of truth** for section field schemas — it drives the admin builder's generated form (`landing-builder.js`), the server-side sanitizer (`landing_sanear_contenido()`), and the default content when a section is added. Add a field once here; the form and sanitizer pick it up automatically.
- CSS custom properties are prefixed `--lp-*` (e.g. `--lp-acento`, `--lp-texto`, `--lp-nav-texto`, `--lp-nav-fondo`) — a **separate token system** from the dashboard's `--accent`/`--text`/etc., because the public landing needs a per-center customizable palette (`colorAcento`) independent of the app's own theme.
- **Use `--lp-nav-texto`, not `--lp-texto`, for anything rendered on the nav/topbar/mobile-drawer background specifically.** They're equal in 3 of the 4 built-in themes, which is exactly what let a bug slip through unnoticed for a while: the "Universidad" theme sets `--lp-nav-fondo` to a solid accent color, but drawer text was styled with `--lp-texto` (the *page* body-text token) instead of `--lp-nav-texto` — producing near-invisible dark text on a dark-blue drawer, only in that one theme.
- Public content tables (`blog_posts`, `landing_ciclos`) are intentionally **separate from any internal/academic table of the same conceptual subject** (e.g. `landing_ciclos` vs. the academic `ciclos` table used for enrollment/grades) — marketing content and operational data have different lifecycles, different editors (secretaría vs. admin-only), and mixing them risks exposing internal fields publicly. New public content types should follow the same split.
- Landing-only sanitization helpers: `landing_img_url()` (resolves an uploaded filename to a public URL, or passes through an already-absolute URL), `landing_url_segura()` (only allows `#anchor`, same-origin `/path`, or `https://` URLs in admin-configurable link fields — rejects `javascript:`/`data:`/protocol-relative URLs).
- `.htaccess` in this folder blocks direct web access to its `.php` files (the public site always enters through `/index.php`, `/vistas/blog.php`, `/vistas/ciclos.php`, never a file inside `landing-system/` directly) — **`.htaccess` files cannot contain `<Directory>` blocks** (Apache only allows those in main server/vhost config); use `<FilesMatch>` instead, wrapped in the `<IfModule mod_authz_core.c>...<IfModule !mod_authz_core.c>` dual-compat pattern the other `.htaccess` files in this repo already use, so it degrades gracefully on Apache installs without `mod_access_compat`.

---

## `.gitignore` gotcha

Git cannot re-include (`!pattern`) a file whose **parent directory** is excluded by an earlier `dirname/` pattern — this bit us once already: `public/uploads/.htaccess` (the file that disables PHP execution for every upload subfolder) plus two subfolder `.htaccess` files sat untracked and invisible to `git status` for an unknown period, despite `.gitignore` lines that looked like they were tracking them. When adding a new "ignore everything except X" rule for a directory, use the two-step form — un-ignore the directory itself, *then* re-ignore its contents except the file you want tracked:
```gitignore
public/uploads/*
!public/uploads/nuevacarpeta/
public/uploads/nuevacarpeta/*
!public/uploads/nuevacarpeta/.htaccess
```
Never trust a single `!public/uploads/sub/archivo` line to work — verify with `git check-ignore -v <path>` (should print nothing / exit 1) and `git status --short` (should show `??` for a not-yet-added file) before assuming it's tracked.

---

## Deployment Rule
After every task, run `git status --short` and provide a deploy summary with three sections:
1. **Upload (new)** — untracked `??` files
2. **Upload (modified)** — `M` files, grouped by area
3. **Delete from server** — `D` files

Production server: `aulapro.yassin.agency` — manual FTP, no pipeline.
