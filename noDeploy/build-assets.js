// Bundles + minifies the CSS/JS that every single page loads unconditionally
// (public/js/core/* wired in vistas/comunes/footer.php, dashboard.css +
// estilo.css wired in each vistas/{role}/comunes/nav.php <head>).
//
// Feature-specific CSS/JS (public/css/features/*, public/js/features/*) is
// intentionally NOT bundled here — those load conditionally per feature flag
// / per page, so combining them would ship unused code to pages that don't
// need it. This targets the one set of assets every single page pays for.
//
// Run: npm run build:assets — then commit the two output files, same as any
// other manually-deployed asset (this project has no CI/CD, see CLAUDE.md).
const esbuild = require('esbuild');
const fs = require('fs');

const CORE_JS = [
  'public/js/core/dashboard-shell.js',
  'public/js/core/onboarding-tour.js',
  'public/js/core/filtros.js',
  'public/js/core/paginacion.js',
  'public/js/core/modal-borrar.js',
  'public/js/core/modal-confirm.js',
  'public/js/core/toast.js',
  'public/js/core/upload-overlay.js',
  'public/js/core/tooltip.js',
];

// Must match the <link> order in nav.php: dashboard.css defines the design
// tokens, estilo.css and the rest consume them — reversing this order would
// break every var(--*) lookup. notificaciones.css / onboarding-tour.css are
// loaded unconditionally by all 5 nav.php; aula-digital.css by 4 of 5
// (everything but tutores) — still safe to bundle, the unused rules just
// don't match anything on the one role that doesn't need them.
const CORE_CSS = [
  'public/css/dashboard.css',
  'public/css/estilo.css',
  'public/css/features/notificaciones.css',
  'public/css/features/onboarding-tour.css',
  'public/css/features/aula-digital.css',
];

// Feature CSS/JS that loads CONDITIONALLY (behind a FeatureGuard check, or
// only on specific pages) — deliberately NOT bundled with the core set
// above, since combining it would ship unused code to pages/roles that
// don't have that feature on. Minified individually instead, as separate
// sibling .min files next to the source (source stays authored/readable;
// nothing here overwrites it).
const FEATURE_FILES = [
  ['public/css/features/chat-widget.css', 'css'],
  ['public/css/features/mensajes.css', 'css'],
  ['public/css/features/horario-admin.css', 'css'],
  ['public/css/features/login.css', 'css'],
  ['public/css/features/legal.css', 'css'],
  ['public/css/features/chat.css', 'css'],
  ['public/css/features/calendario.css', 'css'],
  ['public/js/features/chat.js', 'js'],
  ['public/js/features/chat-widget.js', 'js'],
  ['public/js/features/mensajes.js', 'js'],
  ['public/js/features/horario.js', 'js'],
  ['public/js/features/calendario.js', 'js'],
  ['public/js/core/notificaciones-dashboard.js', 'js'],
];

function bundle(files, outFile, loader) {
  // Joined with ';\n' so a missing trailing semicolon in one JS file can't
  // merge two statements across a file boundary (classic concatenation bug).
  const sep = loader === 'js' ? ';\n' : '\n';
  const combined = files.map(f => fs.readFileSync(f, 'utf8')).join(sep);
  const result = esbuild.transformSync(combined, { loader, minify: true, target: 'es2018' });
  fs.writeFileSync(outFile, result.code);
  const kb = (Buffer.byteLength(result.code) / 1024).toFixed(1);
  console.log(`built ${outFile} — ${kb} KB (from ${files.length} files)`);
}

function minifyOne(file, loader) {
  if (!fs.existsSync(file)) { console.log(`skip ${file} — not found`); return; }
  const src = fs.readFileSync(file, 'utf8');
  const result = esbuild.transformSync(src, { loader, minify: true, target: 'es2018' });
  const outFile = file.replace(/\.(css|js)$/, '.min.$1');
  fs.writeFileSync(outFile, result.code);
  const before = (Buffer.byteLength(src) / 1024).toFixed(1);
  const after = (Buffer.byteLength(result.code) / 1024).toFixed(1);
  console.log(`built ${outFile} — ${before} KB -> ${after} KB`);
}

bundle(CORE_JS, 'public/js/core/bundle.min.js', 'js');
bundle(CORE_CSS, 'public/css/bundle.min.css', 'css');

for (const [file, loader] of FEATURE_FILES) {
  minifyOne(file, loader);
}
