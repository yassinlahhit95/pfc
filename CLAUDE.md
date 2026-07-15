# AULAPRO — Project Rules for Claude

## Project Overview
School management SaaS — PHP 8.3 / Apache 2.4 on Laragon (local: `pfc.test`), production: `aulapro.yassin.agency`.
Manual FTP deployment — no CI/CD pipeline.

---

## Tech Stack
- **Backend:** PHP 8.3, MySQLi (no ORM), sessions for auth
- **Frontend:** jQuery (globally available), Font Awesome 6, vanilla JS
- **CSS:** Custom properties from `dashboard.css`, content styles in `public/css/estilo.css`
- **JS globals loaded in every page:** `core/filtros.js`, `core/paginacion.js`, `core/toast.js`, `core/modal-borrar.js` (via `vistas/comunes/footer.php`)

---

## File Structure
```
controladores/admin/     ← admin action controllers (POST handlers)
controladores/profesores/ ← professor action controllers
include/                 ← AdminGuard.php, ProfesorGuard.php, Security.php
modelos/                 ← DB query functions (one file per entity)
vistas/admin/            ← admin views (PHP templates)
vistas/profesores/       ← professor views
vistas/comunes/          ← shared nav.php + footer.php
public/css/estilo.css   ← content styles (root-level, loaded on every page)
public/css/dashboard.css ← design tokens/custom properties (root-level, loaded on every page)
public/css/features/    ← one file per feature (chat.css, mensajes.css, horario-admin.css, ...)
public/css/landing/     ← public landing page themes (base.css, tema-*.css)
public/js/core/         ← globals used across pages (filtros.js, paginacion.js, toast.js, modal-borrar.js, modal-confirm.js, menu-contextual.js, dashboard-shell.js, analytics.js, aula-digital.js)
public/js/features/     ← one file per feature (chat.js, mensajes.js, horario.js, blog-editor.js, ...)
public/js/firebase/     ← Firebase web SDK config + notifications UI
```

Note: `core/analytics.js` and `features/aula-recursos.js` compute their own root path via `document.currentScript`/`import.meta.url` and a hardcoded `../../../` — they must stay exactly 3 directories below the project root (i.e. directly under `public/js/core/` or `public/js/features/`, not nested deeper) or that path math breaks.

---

## Security — HARD RULES

- **NEVER commit `.env` or `config/db.php`** — they contain production credentials. Both are in `.gitignore`.
- **NEVER commit `config/service-account.json`** — Firebase service account.
- Every admin view must start with `require_once __DIR__ . "/../../../include/AdminGuard.php";`
- Every profesor view must start with `require_once __DIR__ . "/../../../include/ProfesorGuard.php";`
- Always escape output: `Security::escapeHtml($value)` — never raw echo user data.
- CSRF tokens: `Security::generateCSRFToken()` in forms, validate in controllers.
- Integer IDs: always cast with `(int)` before using in queries.

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

### Feature flags
- `FeatureGuard::requirePage('feature_xxx')` — used in inventario and other optional features.

---

## CSS Rules

- **Never hardcode hex colors** — always use CSS custom properties: `var(--accent)`, `var(--surface)`, `var(--bg)`, `var(--text)`, `var(--dim)`, `var(--mut)`, `var(--border)`, `var(--border-2)`, `var(--surface-2)`, `var(--shadow-sm/md/lg)`
- Dark mode is handled automatically via `[data-theme="dark"]` overriding the same properties — no separate dark mode code needed
- `estilo.css` has 20 numbered sections — add new styles to the relevant section, never append at random
- `.panel` = main card container; `.cabecera` = page header with h1 + action buttons
- `.boton-primario` / `.boton-secundario` / `.boton-peligro` — standard button classes
- `.formulario` = CSS grid form (`repeat(auto-fill, minmax(280px, 1fr))`); `.campo.ancho-total` = full-width field
- `.tabla-datos` + `.contenedor-tabla` = standard table pattern; always give table an `id` for pagination

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
Every "buscar"/filter `<input>` must carry `autocomplete="off"` — these are live client-side filters, never form fields to be re-submitted, so browser suggestion dropdowns only get in the way. For the global topbar search (`#sys-search` in each role's `nav.php`), use the fuller hardened set since `autocomplete="off"` alone doesn't stop Chrome/password managers on a field named/id'd like a search box:
```html
<input type="search" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
       data-lpignore="true" data-1p-ignore="true" data-form-type="other">
```

### Toast notifications
`Toast.show('message', 'success'|'error'|'info')` — always guard with `if (window.Toast)`.

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
Use `.panel-vacio` with `.panel-vacio-icono`, `.panel-vacio-titulo`, `.panel-vacio-desc`.

### Status chips
Use `<span class="texto-estado azul|verde|rojo|gris|naranja">text</span>`

---

## Deployment Rule
After every task, run `git status --short` and provide a deploy summary with three sections:
1. **Upload (new)** — untracked `??` files
2. **Upload (modified)** — `M` files, grouped by area
3. **Delete from server** — `D` files

Production server: `aulapro.yassin.agency` — manual FTP, no pipeline.
