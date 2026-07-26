<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . '/../../../modelos/conectar.php';

$con = obtenerConexion();
$res = mysqli_query($con, "SELECT * FROM configuracion_centro WHERE idConfig = 1 LIMIT 1");
$cfg = $res ? mysqli_fetch_assoc($res) : [];

function getEnvValue(string $key, string $default = ''): string {
    $path = __DIR__ . '/../../../.env';
    if (!file_exists($path)) return $default;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if (!$line || $line[0] === '#' || !str_contains($line, '=')) continue;
        [$envKey, $envValue] = explode('=', $line, 2);
        if (trim($envKey) === $key) {
            return trim($envValue, " \t\n\r\0\x0B\"'");
        }
    }
    return $default;
}

function updateEnvFile(array $data): bool {
    $path = __DIR__ . '/../../../.env';
    if (!file_exists($path)) return false;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) return false;
    
    foreach ($data as $key => $value) {
        $found = false;
        foreach ($lines as $i => $line) {
            $trimmed = trim($line);
            if ($trimmed !== '' && $trimmed[0] !== '#' && str_starts_with($trimmed, $key . '=')) {
                $lines[$i] = $key . '=' . $value;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $lines[] = $key . '=' . $value;
        }
    }
    
    return file_put_contents($path, implode("\n", $lines) . "\n") !== false;
}

// api/.htaccess hardcodea el dominio permitido para CORS (Access-Control-Allow-Origin)
// a propósito — es la única fuente de verdad, ver el comentario en ese fichero.
// Si aquí se cambia APP_URL (p.ej. al mover la instancia a su dominio real),
// hay que mantenerlo sincronizado o un futuro cliente web/PWA en ese dominio
// quedaría bloqueado. Mejor esfuerzo: no bloquea el guardado de credenciales
// si falla.
function updateCorsOrigin(string $appUrl): void {
    $path = __DIR__ . '/../../../api/.htaccess';
    if (!is_file($path) || !is_writable($path)) return;
    $contenido = file_get_contents($path);
    if ($contenido === false) return;
    $nuevo = preg_replace(
        '/Access-Control-Allow-Origin "[^"]*"/',
        'Access-Control-Allow-Origin "' . rtrim($appUrl, '/') . '"',
        $contenido,
        1
    );
    if ($nuevo !== null && $nuevo !== $contenido) {
        file_put_contents($path, $nuevo);
    }
}

$error_msg = null;
$success_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_creds') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_msg = "Token de seguridad inválido. Recarga la página.";
    } else {
        $url = trim($_POST['app_url'] ?? '');
        $apiKey = trim($_POST['api_key'] ?? '');
        $apiSecret = trim($_POST['api_secret'] ?? '');
        $licSecret = trim($_POST['lic_secret'] ?? '');

        if (!$url || !$apiKey || !$apiSecret || !$licSecret) {
            $error_msg = "Todos los campos de credenciales son obligatorios.";
        } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
            $error_msg = "La URL de la Instancia no es una dirección web válida.";
        } else {
            $updated = updateEnvFile([
                'APP_URL'             => rtrim($url, '/'),
                'ADMIN_API_KEY'       => $apiKey,
                'ADMIN_API_SECRET'    => $apiSecret,
                'SAAS_LICENSE_SECRET' => $licSecret
            ]);
            if ($updated) {
                updateCorsOrigin($url);
                FeatureGuard::clearCache();
                $success_msg = "Credenciales de conexión actualizadas correctamente.";
            } else {
                $error_msg = "No se pudo escribir en el archivo .env. Por favor, comprueba los permisos de escritura del archivo en el servidor.";
            }
        }
    }
}

$titulo_pagina = 'AULAPRO | ESTADO DE LA PLATAFORMA';
$seccion = 'saas_estado';
include_once __DIR__ . '/../comunes/nav.php';

// Use FeatureGuard resolved state (valid token takes priority over raw DB values)
$state = FeatureGuard::getAll();

$instanceStatus = $state['instance_status'] ?? 'active';
$locked         = (bool)($state['saas_lock_features'] ?? false);
$saasMsg        = $state['saas_message'] ?? '';
$saasType       = $state['saas_message_type'] ?? 'info';
$lastSync       = $cfg['saas_last_sync'] ?? null; // saas_last_sync is not in the token

$statusColors = [
    'active'    => ['var(--verde)',   'var(--verde-suave)',   'Activa'],
    'suspended' => ['var(--rojo)',    'var(--rojo-suave)',    'Suspendida'],
    'pending'   => ['var(--naranja)', 'var(--naranja-suave)', 'Pendiente'],
];
[$statusColor, $statusBg, $statusLabel] = $statusColors[$instanceStatus] ?? ['var(--dim)','var(--surface-2)','Desconocida'];

$msgTypeColors = [
    'info'         => ['var(--azul)',    'var(--azul-suave)',    'var(--azul-suave)',    'fa-circle-info'],
    'warning'      => ['var(--naranja)', 'var(--naranja-suave)', 'var(--naranja-suave)', 'fa-triangle-exclamation'],
    'error'        => ['var(--rojo)',    'var(--rojo-suave)',    'var(--rojo-suave)',    'fa-circle-exclamation'],
    'subscription' => ['var(--violeta)', 'var(--violeta-suave)', 'color-mix(in srgb, var(--violeta) 35%, transparent)', 'fa-credit-card'],
    'activation'   => ['var(--azul)',    'var(--azul-suave)',    'var(--azul-suave)',    'fa-key'],
];
[$msgColor, $msgBg, $msgBorder, $msgIcon] = $msgTypeColors[$saasType] ?? $msgTypeColors['info'];

$features = [
    'feature_prematricula' => ['Pre-matrícula',  'fa-user-plus',     '#4f46e5'],
    'feature_chat'         => ['Chat',            'fa-comments',      '#10b981'],
    'feature_inventario'   => ['Inventario',      'fa-boxes',         '#f59e0b'],
    'feature_subida_tfg'   => ['Entrega de TFG',  'fa-file-upload',   'var(--violeta)'],
    'feature_anuncios'     => ['Anuncios',         'fa-bullhorn',      '#f43f5e'],
    'feature_eventos'      => ['Eventos',          'fa-calendar-days', '#0ea5e9'],
    'feature_retos'        => ['Retos',            'fa-trophy',        '#f59e0b'],
    'feature_mensajes'     => ['Mensajería',       'fa-envelope',      '#6366f1'],
    'feature_pagos'        => ['Pagos',            'fa-credit-card',   '#10b981'],
    'feature_gastos'       => ['Gastos',           'fa-receipt',       '#ef4444'],
    'feature_informes'     => ['Informes PDF',     'fa-file-pdf',      '#64748b'],
    'feature_horario'      => ['Cuadro Horario',   'fa-calendar-alt',  '#4f46e5'],
    'feature_geoblock_admin'=>['Geo-Block (España)','fa-globe-europe', '#dc2626'],
    'feature_ra_ce'        => ['Eval. LOMLOE',     'fa-star-half-stroke','#f59e0b'],
    'feature_fp_dual'      => ['FP Dual',          'fa-building',      '#10b981'],
    'feature_landing'      => ['Página Web Pública','fa-globe',         '#0ea5e9'],
    'feature_fct'          => ['FCT',              'fa-briefcase',     '#0891b2'],
];

// License expiry — use subscription expiry from token (sub_exp), fall back to token expiry
$source  = $state['_source'] ?? 'grace';
$subExpTs = $state['sub_exp'] ?? null; // actual subscription end date (from saas-admin)
$tokenExpTs = ($cfg['license_token_exp'] ?? null) ? strtotime($cfg['license_token_exp']) : null;

// Show subscription expiry if available, otherwise token expiry
$expTs       = $subExpTs ?? $tokenExpTs;
$expRemaining = $expTs ? ($expTs - time()) : null;
$expExpired   = $expTs !== null && $expRemaining <= 0;

if ($expTs && !$expExpired) {
    $expDays  = (int)floor($expRemaining / 86400);
    $expHours = (int)floor(($expRemaining % 86400) / 3600);
    $expMins  = (int)floor(($expRemaining % 3600) / 60);
    if ($expRemaining > 30 * 86400)    { $expColor = 'var(--verde)';   $expBg = 'var(--verde-suave)'; }
    elseif ($expRemaining > 7 * 86400) { $expColor = 'var(--naranja)'; $expBg = 'var(--naranja-suave)'; }
    else                                { $expColor = 'var(--rojo)';    $expBg = 'var(--rojo-suave)'; }
} else {
    $expDays = $expHours = $expMins = 0;
    $expColor = $expExpired ? 'var(--rojo)' : 'var(--dim)';
    $expBg    = $expExpired ? 'var(--rojo-suave)' : 'var(--surface-2)';
}
$expLabel = $subExpTs ? 'Suscripción válida hasta' : 'Token válido hasta';

// ── Diagnóstico de APCu ──────────────────────────────────────────────────
// Cache::remember() (include/Cache.php) degrada de forma transparente a un
// array estático por-request cuando APCu no está disponible — el sitio sigue
// funcionando, pero pierde el efecto real de la caché compartida (60s TTL
// entre workers) en todos los contadores de nav, FeatureGuard, etc. Como esto
// nunca lanza un error visible, es fácil no darse cuenta de que está
// ocurriendo en producción; esta tarjeta comprueba un round-trip real
// (no solo si la extensión está cargada) para dar una respuesta definitiva.
$apcuStatus = 'missing';   // missing | disabled | broken | ok
$apcuDetail = '';
if (!function_exists('apcu_fetch')) {
    $apcuStatus = 'missing';
    $apcuDetail = 'La extensión apcu no está instalada en este servidor PHP.';
} elseif (function_exists('apcu_enabled') && !apcu_enabled()) {
    $apcuStatus = 'disabled';
    $apcuDetail = 'La extensión está instalada pero deshabilitada (revisa apc.enabled en php.ini).';
} else {
    $apcuTestKey = 'aulapro_apcu_diagnostico';
    $apcuTestVal = 'ok_' . random_int(1000, 9999);
    apcu_store($apcuTestKey, $apcuTestVal, 30);
    $found = false;
    $roundTrip = apcu_fetch($apcuTestKey, $found);
    if ($found && $roundTrip === $apcuTestVal) {
        $apcuStatus = 'ok';
        $apcuDetail = 'Round-trip de escritura/lectura verificado correctamente.';
    } else {
        $apcuStatus = 'broken';
        $apcuDetail = 'La extensión responde pero el round-trip de prueba falló — revisa apc.shm_size / permisos.';
    }
}
$apcuCards = [
    'ok'       => ['var(--verde)',   'var(--verde-suave)',   'fa-check-circle',        'Activa y funcionando'],
    'disabled' => ['var(--naranja)', 'var(--naranja-suave)', 'fa-triangle-exclamation','Instalada pero deshabilitada'],
    'broken'   => ['var(--rojo)',    'var(--rojo-suave)',    'fa-circle-exclamation',  'Falla el round-trip'],
    'missing'  => ['var(--dim)',     'var(--surface-2)',     'fa-circle-minus',        'No instalada'],
];
[$apcuColor, $apcuBg, $apcuIcon, $apcuLabel] = $apcuCards[$apcuStatus];
?>

<div class="cabecera">
  <h1>Estado de la Plataforma SaaS</h1>
  <p class="subtitulo-encabezado">Vista de solo lectura — controlada por la plataforma central.</p>
</div>

<style>
.saas-grid   { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:16px; margin-bottom:24px; }
.saas-card   { background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:20px 22px; }
.saas-kpi-head { display:flex; align-items:center; gap:12px; margin-bottom:6px; }
.saas-kpi-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.saas-kpi-label { font-size:11px; font-weight:700; color:var(--dim); text-transform:uppercase; letter-spacing:.05em; }
.saas-kpi-val { font-size:22px; font-weight:800; color:var(--text); }
.saas-kpi-sub { font-size:12px; color:var(--dim); margin-top:4px; }
.feature-row { display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--border); }
.feature-row:last-child { border-bottom:none; }
.feature-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; }
.badge-on  { background:var(--verde-suave); color:var(--verde-ink); }
.badge-off { background:var(--rojo-suave); color:var(--rojo-ink); }
.saas-message-box { border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:flex-start; gap:12px; }
.lock-banner { background:var(--naranja-suave); border:1px solid var(--naranja); border-radius:10px; padding:12px 16px; display:flex; align-items:center; gap:10px; font-size:13px; color:var(--naranja-ink); margin-bottom:20px; }
.readonly-note { font-size:11px; color:var(--mut); display:flex; align-items:center; gap:4px; margin-top:4px; }
</style>

<?php if ($saasMsg): ?>
<div class="saas-message-box" style="background:<?= $msgBg ?>;border:1px solid <?= $msgBorder ?>;">
  <span style="font-size:1.5rem;line-height:1;color:<?= $msgColor ?>;"><i class="fas <?= $msgIcon ?>"></i></span>
  <div>
    <div style="font-weight:700;color:<?= $msgColor ?>;margin-bottom:4px;">Mensaje de la plataforma</div>
    <div style="font-size:14px;color:var(--text);"><?= htmlspecialchars($saasMsg, ENT_QUOTES) ?></div>
  </div>
</div>
<?php endif; ?>

<?php if ($locked): ?>
<div class="lock-banner">
  <i class="fas fa-lock"></i>
  <span><strong>Funcionalidades bloqueadas:</strong> La plataforma SaaS controla actualmente qué módulos están activos. No se puede modificar desde aquí.</span>
</div>
<?php endif; ?>

<!-- KPI row -->
<div class="saas-grid">

  <div class="saas-card">
    <div class="saas-kpi-head">
      <div class="saas-kpi-icon" style="background:<?= $statusBg ?>;color:<?= $statusColor ?>;">
        <i class="fas fa-<?= $instanceStatus === 'active' ? 'check-circle' : ($instanceStatus === 'suspended' ? 'ban' : 'clock') ?>"></i>
      </div>
      <div>
        <div class="saas-kpi-label">Estado de la instancia</div>
        <div class="saas-kpi-val" style="color:<?= $statusColor ?>;"><?= $statusLabel ?></div>
      </div>
    </div>
    <?php if (($state['suspension_message'] ?? '')): ?>
      <p class="saas-kpi-sub" style="color:var(--rojo);"><?= htmlspecialchars($state['suspension_message'], ENT_QUOTES) ?></p>
    <?php endif; ?>
  </div>

  <div class="saas-card">
    <div class="saas-kpi-head">
      <div class="saas-kpi-icon" style="background:<?= $locked ? 'var(--naranja-suave)' : 'var(--verde-suave)' ?>;color:<?= $locked ? 'var(--naranja)' : 'var(--verde)' ?>;">
        <i class="fas fa-<?= $locked ? 'lock' : 'lock-open' ?>"></i>
      </div>
      <div>
        <div class="saas-kpi-label">Control de funcionalidades</div>
        <div class="saas-kpi-val" style="font-size:15px;color:<?= $locked ? 'var(--naranja)' : 'var(--verde)' ?>;">
          <?= $locked ? 'Bloqueado por SaaS' : 'Editable localmente' ?>
        </div>
      </div>
    </div>
    <p class="saas-kpi-sub"><?= $locked ? 'Solo el proveedor puede cambiar los módulos.' : 'El director puede activar/desactivar módulos.' ?></p>
  </div>

  <div class="saas-card">
    <div class="saas-kpi-head">
      <div class="saas-kpi-icon" style="background:var(--azul-suave);color:var(--azul);">
        <i class="fas fa-sync-alt"></i>
      </div>
      <div>
        <div class="saas-kpi-label">Última sincronización</div>
        <div class="saas-kpi-val" style="font-size:16px;">
          <?= $lastSync ? date('d/m/Y H:i', strtotime($lastSync)) : '—' ?>
        </div>
      </div>
    </div>
    <p class="saas-kpi-sub">Fecha de la última acción recibida desde SaaS.</p>
  </div>

  <!-- License expiry card -->
  <div class="saas-card">
    <div class="saas-kpi-head">
      <div class="saas-kpi-icon" style="background:<?= $expBg ?>;color:<?= $expColor ?>;">
        <i class="fas fa-<?= $expExpired ? 'exclamation-triangle' : ($source === 'grace' ? 'shield-alt' : 'hourglass-half') ?>"></i>
      </div>
      <div>
        <div class="saas-kpi-label"><?= $subExpTs ? 'Suscripción' : 'Licencia' ?></div>
        <div class="saas-kpi-val" id="exp-countdown" style="font-size:16px;color:<?= $expColor ?>;">
          <?php if ($source === 'grace'): ?>
            Sin licencia (gracia)
          <?php elseif ($expExpired): ?>
            Expirado
          <?php elseif ($expTs): ?>
            <?= $expDays > 0 ? "{$expDays}d {$expHours}h {$expMins}m" : "{$expHours}h {$expMins}m" ?>
          <?php else: ?>
            —
          <?php endif; ?>
        </div>
      </div>
    </div>
    <p class="saas-kpi-sub">
      <?php if ($expTs): ?>
        <?= $expLabel ?>: <strong><?= date('d/m/Y', $expTs) ?></strong>
      <?php elseif ($source === 'grace'): ?>
        Instancia en período de gracia — sin heartbeat recibido.
      <?php else: ?>
        Sin fecha de expiración registrada.
      <?php endif; ?>
    </p>
    <?php if ($expTs && !$expExpired && $expRemaining < 30 * 86400): ?>
    <div style="margin-top:8px;padding:6px 10px;background:<?= $expBg ?>;border-radius:7px;font-size:12px;font-weight:700;color:<?= $expColor ?>;">
      <i class="fas fa-bell"></i> Contacta con tu proveedor para renovar
    </div>
    <?php endif; ?>
  </div>

  <!-- Storage card — polled live, see #storage-script below -->
  <div class="saas-card">
    <div class="saas-kpi-head">
      <div class="saas-kpi-icon" id="storage-icon" style="background:var(--surface-2);color:var(--dim);">
        <i class="fas fa-database"></i>
      </div>
      <div>
        <div class="saas-kpi-label">Almacenamiento (R2)</div>
        <div class="saas-kpi-val" id="storage-val" style="font-size:16px;">—</div>
      </div>
    </div>
    <div style="margin-top:10px;height:6px;border-radius:4px;background:var(--surface-2);overflow:hidden;">
      <div id="storage-bar" style="height:100%;width:0%;border-radius:4px;background:var(--dim);transition:width .4s ease, background-color .4s ease;"></div>
    </div>
    <p class="saas-kpi-sub" id="storage-sub">Comprobando conexión con Cloudflare R2…</p>
  </div>

  <!-- APCu cache diagnostic — see PHP block above, real store+fetch round-trip -->
  <div class="saas-card">
    <div class="saas-kpi-head">
      <div class="saas-kpi-icon" style="background:<?= $apcuBg ?>;color:<?= $apcuColor ?>;">
        <i class="fas <?= $apcuIcon ?>"></i>
      </div>
      <div>
        <div class="saas-kpi-label">Caché compartida (APCu)</div>
        <div class="saas-kpi-val" style="font-size:16px;color:<?= $apcuColor ?>;"><?= Security::escapeHtml($apcuLabel) ?></div>
      </div>
    </div>
    <p class="saas-kpi-sub"><?= Security::escapeHtml($apcuDetail) ?></p>
    <?php if ($apcuStatus !== 'ok'): ?>
    <p class="saas-kpi-sub" style="margin-top:4px;">Sin APCu, los contadores de nav y FeatureGuard siguen funcionando pero sin caché compartida entre workers (cada petición repite la consulta).</p>
    <?php endif; ?>
  </div>

</div>

<script>
(function () {
  const iconEl = document.getElementById('storage-icon');
  const valEl  = document.getElementById('storage-val');
  const barEl  = document.getElementById('storage-bar');
  const subEl  = document.getElementById('storage-sub');
  if (!valEl) return;

  function humanBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    const units = ['KB', 'MB', 'GB', 'TB'];
    let val = bytes, i = -1;
    do { val /= 1024; i++; } while (val >= 1024 && i < units.length - 1);
    return val.toFixed(val >= 10 ? 0 : 1) + ' ' + units[i];
  }

  function refresh() {
    fetch('/controladores/admin/saas/storageUsage.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          valEl.textContent = 'No configurado';
          subEl.textContent = 'Rellena las variables R2_* en .env para activar R2 (ver noDeploy/CLOUDFLARE_R2_SETUP.md).';
          return;
        }
        const pct = data.percent;
        let color = 'var(--verde)', bg = 'var(--verde-suave)';
        if (pct >= 90)      { color = 'var(--rojo)';    bg = 'var(--rojo-suave)'; }
        else if (pct >= 70) { color = 'var(--naranja)'; bg = 'var(--naranja-suave)'; }

        valEl.textContent = humanBytes(data.bytes) + ' / ' + humanBytes(data.limitBytes);
        valEl.style.color = color;
        barEl.style.width = Math.max(pct, 1) + '%';
        barEl.style.background = color;
        if (iconEl) { iconEl.style.background = bg; iconEl.style.color = color; }
        subEl.textContent = data.objectCount + ' objeto(s) · ' + pct + '% del límite gratuito (10 GB)';
      })
      .catch(() => { subEl.textContent = 'No se pudo consultar el uso de almacenamiento.'; });
  }

  refresh();
  setInterval(refresh, 30000);
})();
</script>

<script>
(function () {
  const expTs = <?= $expTs ?? 'null' ?>;
  const el    = document.getElementById('exp-countdown');
  if (!expTs || !el) return;

  function fmt(remaining) {
    if (remaining <= 0) { el.textContent = 'Expirado'; el.style.color = 'var(--rojo)'; return; }
    const days = Math.floor(remaining / 86400);
    const hours = Math.floor((remaining % 86400) / 3600);
    const minutes = Math.floor((remaining % 3600) / 60);
    const seconds = remaining % 60;
    el.textContent = days > 0
      ? `${days}d ${hours}h ${minutes}m`
      : `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
  }

  function tick() { fmt(expTs - Math.floor(Date.now() / 1000)); }
  tick();
  setInterval(tick, 1000);
})();
</script>

<!-- Feature flags (read-only) -->
<div class="panel margen-abajo">
  <h3 class="panel-titulo" style="font-size:.85rem;font-weight:700;letter-spacing:.05em;color:var(--dim);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
    Módulos — Estado actual
    <span class="readonly-note"><i class="fas fa-info-circle"></i> Solo lectura</span>
  </h3>

  <?php foreach ($features as $key => [$fLabel, $icon, $color]): ?>
  <?php $enabled = (bool)($state[$key] ?? true); ?>
  <div class="feature-row">
    <div style="display:flex;align-items:center;gap:12px;">
      <div style="width:36px;height:36px;border-radius:9px;background:<?= $color ?>18;color:<?= $color ?>;display:flex;align-items:center;justify-content:center;">
        <i class="fas <?= $icon ?>"></i>
      </div>
      <div>
        <div style="font-weight:700;font-size:14px;color:var(--text);"><?= $fLabel ?></div>
        <div style="font-size:11px;color:var(--mut);font-family:monospace;"><?= $key ?></div>
      </div>
    </div>
    <span class="feature-badge <?= $enabled ? 'badge-on' : 'badge-off' ?>">
      <i class="fas fa-<?= $enabled ? 'check' : 'times' ?>"></i>
      <?= $enabled ? 'Activo' : 'Inactivo' ?>
    </span>
  </div>
  <?php endforeach; ?>

  <?php if ($locked): ?>
    <p style="margin-top:14px;font-size:12px;color:var(--naranja);"><i class="fas fa-lock"></i> Para modificar estos módulos, contacta con el proveedor de la plataforma.</p>
  <?php else: ?>
    <p style="margin-top:14px;font-size:12px;color:var(--dim);"><i class="fas fa-edit"></i> Puedes cambiar los módulos desde <a href="../configuracion/configuracion.php" style="color:var(--accent);font-weight:600;">Configuración del Centro</a>.</p>
  <?php endif; ?>
</div>

<!-- Credentials box for copy/paste configuration -->
<div class="panel margen-abajo" style="background:var(--surface-2);border:1.5px dashed var(--border-2);padding:22px;border-radius:14px;margin-top:20px;">
  <h3 class="panel-titulo" style="font-size:.85rem;font-weight:800;letter-spacing:.05em;color:var(--dim);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-2);display:flex;align-items:center;gap:6px;">
    <i class="fas fa-key" style="color:var(--accent);"></i>
    Credenciales de Conexión (saas-admin)
    <span class="readonly-note" style="color:var(--dim);margin-left:auto;font-size:10px;"><i class="fas fa-lock"></i> Solo Directores</span>
  </h3>
  
  <p style="font-size:12px;color:var(--dim);margin-bottom:16px;line-height:1.5;">
    Puedes ver y editar las credenciales de conexión directamente desde aquí. Asegúrate de guardar los cambios y de que coincidan con los de su panel <strong>saas-admin</strong>.
  </p>

  <?php if ($error_msg): ?>
    <div style="background:var(--rojo-suave);border:1px solid var(--rojo-suave);color:var(--rojo-ink);padding:10px 14px;border-radius:8px;font-size:12px;margin-bottom:14px;display:flex;align-items:center;gap:6px;font-family:sans-serif;">
      <i class="fas fa-triangle-exclamation"></i>
      <span><?= htmlspecialchars($error_msg, ENT_QUOTES) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($success_msg): ?>
    <div style="background:var(--verde-suave);border:1px solid var(--verde-suave);color:var(--verde-ink);padding:10px 14px;border-radius:8px;font-size:12px;margin-bottom:14px;display:flex;align-items:center;gap:6px;font-family:sans-serif;">
      <i class="fas fa-circle-check"></i>
      <span><?= htmlspecialchars($success_msg, ENT_QUOTES) ?></span>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="hidden" name="action" value="save_creds">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <div style="display:flex;flex-direction:column;gap:12px;">
      <!-- URL -->
      <div style="display:flex;flex-direction:column;gap:4px;">
        <label style="font-size:11px;font-weight:700;color:var(--dim);">URL de la Instancia</label>
        <div style="display:flex;gap:8px;align-items:center;">
          <input type="text" id="creds-url" name="app_url" value="<?= htmlspecialchars(getEnvValue('APP_URL'), ENT_QUOTES) ?>" required 
                 style="font-family:monospace;font-size:12px;padding:6px 10px;border:1px solid var(--border-2);border-radius:6px;background:var(--surface);color:var(--text);flex:1;">
          <button type="button" class="btn btn-sm btn-secundario" onclick="copyCredVal('creds-url', this)" style="padding:6px 12px;font-size:12px;height:auto;">
            <i class="fas fa-copy"></i><span class="btn-txt-mobile"> Copiar</span>
          </button>
        </div>
      </div>

      <!-- API Key -->
      <div style="display:flex;flex-direction:column;gap:4px;">
        <label style="font-size:11px;font-weight:700;color:var(--dim);">API Key</label>
        <div style="display:flex;gap:8px;align-items:center;">
          <input type="text" id="creds-apikey" name="api_key" value="<?= htmlspecialchars(getEnvValue('ADMIN_API_KEY'), ENT_QUOTES) ?>" required 
                 style="font-family:monospace;font-size:12px;padding:6px 10px;border:1px solid var(--border-2);border-radius:6px;background:var(--surface);color:var(--text);flex:1;">
          <button type="button" class="btn btn-sm btn-secundario" onclick="copyCredVal('creds-apikey', this)" style="padding:6px 12px;font-size:12px;height:auto;">
            <i class="fas fa-copy"></i><span class="btn-txt-mobile"> Copiar</span>
          </button>
        </div>
      </div>

      <!-- API Secret -->
      <div style="display:flex;flex-direction:column;gap:4px;">
        <label style="font-size:11px;font-weight:700;color:var(--dim);">API Secret</label>
        <div style="display:flex;gap:8px;align-items:center;">
          <input type="password" id="creds-apisecret" name="api_secret" autocomplete="new-password" value="<?= htmlspecialchars(getEnvValue('ADMIN_API_SECRET'), ENT_QUOTES) ?>" required 
                 style="font-family:monospace;font-size:12px;padding:6px 10px;border:1px solid var(--border-2);border-radius:6px;background:var(--surface);color:var(--text);flex:1;">
          <button type="button" class="btn btn-sm btn-secundario" onclick="togglePassView('creds-apisecret', this)" style="padding:6px 8px;height:auto;" title="Mostrar/Ocultar">
            <i class="fas fa-eye"></i>
          </button>
          <button type="button" class="btn btn-sm btn-secundario" onclick="copyCredVal('creds-apisecret', this)" style="padding:6px 12px;font-size:12px;height:auto;">
            <i class="fas fa-copy"></i><span class="btn-txt-mobile"> Copiar</span>
          </button>
        </div>
      </div>

      <!-- License Secret -->
      <div style="display:flex;flex-direction:column;gap:4px;">
        <label style="font-size:11px;font-weight:700;color:var(--dim);">SaaS License Secret (Firma)</label>
        <div style="display:flex;gap:8px;align-items:center;">
          <input type="password" id="creds-licsecret" name="lic_secret" autocomplete="new-password" value="<?= htmlspecialchars(getEnvValue('SAAS_LICENSE_SECRET'), ENT_QUOTES) ?>" required 
                 style="font-family:monospace;font-size:12px;padding:6px 10px;border:1px solid var(--border-2);border-radius:6px;background:var(--surface);color:var(--text);flex:1;">
          <button type="button" class="btn btn-sm btn-secundario" onclick="togglePassView('creds-licsecret', this)" style="padding:6px 8px;height:auto;" title="Mostrar/Ocultar">
            <i class="fas fa-eye"></i>
          </button>
          <button type="button" class="btn btn-sm btn-secundario" onclick="copyCredVal('creds-licsecret', this)" style="padding:6px 12px;font-size:12px;height:auto;">
            <i class="fas fa-copy"></i><span class="btn-txt-mobile"> Copiar</span>
          </button>
        </div>
      </div>

      <!-- Submit button -->
      <div class="acciones" style="margin-top:10px;padding-top:16px;border-top:1px solid var(--border);">
        <button type="submit" class="boton-primario">
          <i class="fas fa-save"></i> Guardar cambios
        </button>
      </div>
    </div>
  </form>
</div>

<script>
function togglePassView(id, btn) {
  const input = document.getElementById(id);
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fas fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fas fa-eye';
  }
}

function copyCredVal(id, btn) {
  const input = document.getElementById(id);
  const oldType = input.type;
  if (oldType === 'password') input.type = 'text';
  
  input.select();
  input.setSelectionRange(0, 99999);
  
  navigator.clipboard.writeText(input.value).then(() => {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => { btn.innerHTML = originalHtml; }, 2000);
  });
  
  if (oldType === 'password') input.type = 'password';
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
