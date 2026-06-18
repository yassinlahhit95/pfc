<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . '/../../../modelos/conectar.php';

$con = obtenerConexion();
$res = mysqli_query($con, "SELECT * FROM configuracion_centro WHERE idConfig = 1 LIMIT 1");
$cfg = $res ? mysqli_fetch_assoc($res) : [];

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
    'active'    => ['#10b981', '#d1fae5', 'Activa'],
    'suspended' => ['#ef4444', '#fee2e2', 'Suspendida'],
    'pending'   => ['#f59e0b', '#fef3c7', 'Pendiente'],
];
[$statusColor, $statusBg, $statusLabel] = $statusColors[$instanceStatus] ?? ['#6b7280','#f3f4f6','Desconocida'];

$msgTypeColors = [
    'info'         => ['#3b82f6', '#eff6ff', '#dbeafe', 'ℹ️'],
    'warning'      => ['#f59e0b', '#fffbeb', '#fef3c7', '⚠️'],
    'error'        => ['#ef4444', '#fef2f2', '#fee2e2', '🚨'],
    'subscription' => ['#8b5cf6', '#f5f3ff', '#ede9fe', '💳'],
    'activation'   => ['#0ea5e9', '#f0f9ff', '#e0f2fe', '🔑'],
];
[$msgColor, $msgBg, $msgBorder, $msgIcon] = $msgTypeColors[$saasType] ?? $msgTypeColors['info'];

$features = [
    'feature_prematricula' => ['Pre-matrícula',  'fa-user-plus',   '#4f46e5'],
    'feature_chat'         => ['Chat',            'fa-comments',    '#10b981'],
    'feature_inventario'   => ['Inventario',      'fa-boxes',       '#f59e0b'],
    'feature_subida_tfg'   => ['Entrega de TFG',  'fa-file-upload', '#8b5cf6'],
];

// License expiry
$licenseExp  = $cfg['license_token_exp'] ?? null;
$expTs       = $licenseExp ? strtotime($licenseExp) : null;
$source      = $state['_source'] ?? 'grace';
$expRemaining = $expTs ? ($expTs - time()) : null;
$expExpired   = $expTs !== null && $expRemaining <= 0;

if ($expTs && !$expExpired) {
    $expDays  = (int)floor($expRemaining / 86400);
    $expHours = (int)floor(($expRemaining % 86400) / 3600);
    $expMins  = (int)floor(($expRemaining % 3600) / 60);
    if ($expRemaining > 7 * 86400)     { $expColor = '#10b981'; $expBg = '#d1fae5'; }
    elseif ($expRemaining > 86400)     { $expColor = '#f59e0b'; $expBg = '#fef3c7'; }
    else                                { $expColor = '#ef4444'; $expBg = '#fee2e2'; }
} else {
    $expDays = $expHours = $expMins = 0;
    $expColor = $expExpired ? '#ef4444' : '#6b7280';
    $expBg    = $expExpired ? '#fee2e2' : '#f3f4f6';
}
?>

<div class="cabecera">
  <h1>Estado de la Plataforma SaaS</h1>
  <p class="texto-suave">Vista de solo lectura — controlada por la plataforma central.</p>
</div>

<style>
.saas-grid   { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:16px; margin-bottom:24px; }
.saas-card   { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:20px 22px; }
.saas-kpi-head { display:flex; align-items:center; gap:12px; margin-bottom:6px; }
.saas-kpi-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.saas-kpi-label { font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; }
.saas-kpi-val { font-size:22px; font-weight:800; color:#111827; }
.saas-kpi-sub { font-size:12px; color:#6b7280; margin-top:4px; }
.feature-row { display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f3f4f6; }
.feature-row:last-child { border-bottom:none; }
.feature-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; }
.badge-on  { background:#d1fae5; color:#065f46; }
.badge-off { background:#fee2e2; color:#991b1b; }
.saas-message-box { border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:flex-start; gap:12px; }
.lock-banner { background:#fef3c7; border:1px solid #fbbf24; border-radius:10px; padding:12px 16px; display:flex; align-items:center; gap:10px; font-size:13px; color:#92400e; margin-bottom:20px; }
.readonly-note { font-size:11px; color:#9ca3af; display:flex; align-items:center; gap:4px; margin-top:4px; }
</style>

<?php if ($saasMsg): ?>
<div class="saas-message-box" style="background:<?= $msgBg ?>;border:1px solid <?= $msgBorder ?>;">
  <span style="font-size:1.5rem;line-height:1;"><?= $msgIcon ?></span>
  <div>
    <div style="font-weight:700;color:<?= $msgColor ?>;margin-bottom:4px;">Mensaje de la plataforma</div>
    <div style="font-size:14px;color:#374151;"><?= htmlspecialchars($saasMsg, ENT_QUOTES) ?></div>
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
      <p class="saas-kpi-sub" style="color:#ef4444;"><?= htmlspecialchars($state['suspension_message'], ENT_QUOTES) ?></p>
    <?php endif; ?>
  </div>

  <div class="saas-card">
    <div class="saas-kpi-head">
      <div class="saas-kpi-icon" style="background:<?= $locked ? '#fef3c7' : '#f0fdf4' ?>;color:<?= $locked ? '#d97706' : '#16a34a' ?>;">
        <i class="fas fa-<?= $locked ? 'lock' : 'lock-open' ?>"></i>
      </div>
      <div>
        <div class="saas-kpi-label">Control de funcionalidades</div>
        <div class="saas-kpi-val" style="font-size:15px;color:<?= $locked ? '#d97706' : '#16a34a' ?>;">
          <?= $locked ? 'Bloqueado por SaaS' : 'Editable localmente' ?>
        </div>
      </div>
    </div>
    <p class="saas-kpi-sub"><?= $locked ? 'Solo el proveedor puede cambiar los módulos.' : 'El director puede activar/desactivar módulos.' ?></p>
  </div>

  <div class="saas-card">
    <div class="saas-kpi-head">
      <div class="saas-kpi-icon" style="background:#eff6ff;color:#3b82f6;">
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
        <div class="saas-kpi-label">Tiempo restante de licencia</div>
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
        Expira el <strong><?= date('d/m/Y \a \l\a\s H:i', $expTs) ?></strong>
      <?php elseif ($source === 'grace'): ?>
        Instancia en período de gracia — sin heartbeat recibido.
      <?php else: ?>
        Sin fecha de expiración registrada.
      <?php endif; ?>
    </p>
    <?php if ($expTs && !$expExpired && $expRemaining < 3 * 86400): ?>
    <div style="margin-top:8px;padding:6px 10px;background:<?= $expBg ?>;border-radius:7px;font-size:12px;font-weight:700;color:<?= $expColor ?>;">
      <i class="fas fa-bell"></i> Contacta con tu proveedor para renovar
    </div>
    <?php endif; ?>
  </div>

</div>

<script>
(function () {
  const expTs = <?= $expTs ?? 'null' ?>;
  const el    = document.getElementById('exp-countdown');
  if (!expTs || !el) return;

  function fmt(remaining) {
    if (remaining <= 0) { el.textContent = 'Expirado'; el.style.color = '#ef4444'; return; }
    const d = Math.floor(remaining / 86400);
    const h = Math.floor((remaining % 86400) / 3600);
    const m = Math.floor((remaining % 3600) / 60);
    const s = remaining % 60;
    el.textContent = d > 0
      ? `${d}d ${h}h ${m}m`
      : `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
  }

  function tick() { fmt(expTs - Math.floor(Date.now() / 1000)); }
  tick();
  setInterval(tick, 1000);
})();
</script>

<!-- Feature flags (read-only) -->
<div class="panel margen-abajo">
  <h3 class="panel-titulo" style="font-size:.85rem;font-weight:700;letter-spacing:.05em;color:#6b7280;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;">
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
        <div style="font-weight:700;font-size:14px;color:#111827;"><?= $fLabel ?></div>
        <div style="font-size:11px;color:#9ca3af;font-family:monospace;"><?= $key ?></div>
      </div>
    </div>
    <span class="feature-badge <?= $enabled ? 'badge-on' : 'badge-off' ?>">
      <i class="fas fa-<?= $enabled ? 'check' : 'times' ?>"></i>
      <?= $enabled ? 'Activo' : 'Inactivo' ?>
    </span>
  </div>
  <?php endforeach; ?>

  <?php if ($locked): ?>
    <p style="margin-top:14px;font-size:12px;color:#d97706;"><i class="fas fa-lock"></i> Para modificar estos módulos, contacta con el proveedor de la plataforma.</p>
  <?php else: ?>
    <p style="margin-top:14px;font-size:12px;color:#6b7280;"><i class="fas fa-edit"></i> Puedes cambiar los módulos desde <a href="../configuracion/configuracion.php" style="color:#4f46e5;font-weight:600;">Configuración del Centro</a>.</p>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
