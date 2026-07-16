<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/rgpd.php";

$titulo_pagina = "Cumplimiento RGPD";
$seccion       = "rgpd";

$exito   = $_SESSION['exito']   ?? null;
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$eliminaciones = listarEliminacionesRGPD(20);
$csrfToken     = Security::generateCSRFToken();

$todosEstudiantes = listarEstudiantes();

require_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-shield-alt"></i> Cumplimiento RGPD</h1>
    <p class="subtitulo">Portabilidad (Art.&nbsp;20), Supresión (Art.&nbsp;17) y retención de logs</p>
  </div>
</div>

<div class="grid-2col margen-abajo">

  <!-- ── Art. 20: Exportar datos ── -->
  <div class="panel">
    <div class="panel-titulo-seccion">
      <i class="fas fa-file-export"></i> Art. 20 — Portabilidad de datos
    </div>
    <p style="font-size:.875rem;color:var(--dim);margin-bottom:1rem;">
      Exporta todos los datos personales de un estudiante en formato JSON
      (perfil, notas, pagos, mensajes, consentimientos).
    </p>
    <form method="GET" action="../../../controladores/admin/rgpd/exportar.php" class="formulario" style="grid-template-columns:1fr auto;">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="campo">
        <label for="exportar-select">Estudiante</label>
        <select id="exportar-select" name="idEstudiante" required onchange="filtrarRGPD('exportar')">
          <option value="">— Seleccionar estudiante —</option>
          <?php foreach ($todosEstudiantes as $estudiante): ?>
          <option value="<?= (int)$estudiante['idEstudiante'] ?>">
            <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
            <?= !empty($estudiante['dniEstudiante']) ? '(' . Security::escapeHtml($estudiante['dniEstudiante']) . ')' : '' ?>
          </option>
          <?php endforeach; ?>
        </select>
        <input type="text" id="exportar-buscar" placeholder="Buscar por nombre o DNI…" oninput="filtrarRGPD('exportar')" autocomplete="off"
               style="margin-top:6px;width:100%;padding:6px 10px;border:1px solid var(--border-2);border-radius:7px;font-size:.875rem;background:var(--surface);color:var(--text);">
      </div>
      <div class="campo" style="align-self:end;">
        <button type="submit" class="boton-primario">
          <i class="fas fa-download"></i> Exportar JSON
        </button>
      </div>
    </form>
  </div>

  <!-- ── Art. 17: Supresión ── -->
  <div class="panel">
    <div class="panel-titulo-seccion">
      <i class="fas fa-user-slash"></i> Art. 17 — Derecho al olvido
    </div>
    <p style="font-size:.875rem;color:var(--dim);margin-bottom:1rem;">
      Elimina permanentemente todos los datos de un estudiante. Se guarda un
      registro de evidencia conforme al Art.&nbsp;5(2) RGPD.
    </p>
    <form id="form-rgpd-borrar" method="POST" action="../../../controladores/admin/rgpd/borrar.php" class="formulario">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="campo">
        <label for="borrar-select">Estudiante</label>
        <select id="borrar-select" name="idEstudiante" required onchange="filtrarRGPD('borrar')">
          <option value="">— Seleccionar estudiante —</option>
          <?php foreach ($todosEstudiantes as $estudiante): ?>
          <option value="<?= (int)$estudiante['idEstudiante'] ?>">
            <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
            <?= !empty($estudiante['dniEstudiante']) ? '(' . Security::escapeHtml($estudiante['dniEstudiante']) . ')' : '' ?>
          </option>
          <?php endforeach; ?>
        </select>
        <input type="text" id="borrar-buscar" placeholder="Buscar por nombre o DNI…" oninput="filtrarRGPD('borrar')" autocomplete="off"
               style="margin-top:6px;width:100%;padding:6px 10px;border:1px solid var(--border-2);border-radius:7px;font-size:.875rem;background:var(--surface);color:var(--text);">
      </div>
      <div class="campo ancho-total">
        <label for="borrar-motivo">Motivo (obligatorio)</label>
        <textarea id="borrar-motivo" name="motivo" rows="3" required
          placeholder="Ej: Solicitud del interesado vía email de fecha 20/06/2026, referencia RT-0042."></textarea>
      </div>
      <div class="campo">
        <label for="borrar-pass">Tu contraseña de administrador</label>
        <input type="password" id="borrar-pass" name="adminPassword" autocomplete="current-password" required>
      </div>
      <div class="campo ancho-total">
        <button type="submit" class="boton-peligro">
          <i class="fas fa-trash-alt"></i> Eliminar permanentemente
        </button>
      </div>
    </form>
  </div>

</div>

<!-- ── Retención de logs ── -->
<div class="panel margen-abajo">
  <div class="panel-titulo-seccion">
    <i class="fas fa-broom"></i> Retención de logs — LOPDGDD
  </div>
  <p style="font-size:.875rem;color:var(--dim);margin-bottom:1rem;">
    La Ley Orgánica 3/2018 (LOPDGDD) establece un plazo mínimo de conservación
    de registros de actividad de <strong>3 años</strong>. Usa esta herramienta
    para purgar entradas antiguas del log.
  </p>
  <form id="form-purgar" method="POST" action="../../../controladores/admin/rgpd/purgar_logs.php"
        style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <label style="font-size:.875rem;">Eliminar logs con más de</label>
    <input type="number" name="years" value="3" min="3" max="20"
           style="width:80px;" class="campo-input">
    <label style="font-size:.875rem;">años de antigüedad</label>
    <button type="submit" class="boton-secundario" id="btn-purgar">
      <i class="fas fa-database"></i> Purgar registros
    </button>
  </form>
</div>

<!-- ── Historial de eliminaciones RGPD ── -->
<div class="panel">
  <div class="panel-titulo-seccion">
    <i class="fas fa-history"></i> Historial de eliminaciones (evidencia RGPD)
  </div>
  <?php if (empty($eliminaciones)): ?>
  <div class="panel-vacio">
    <div class="panel-vacio-icono"><i class="fas fa-shield-check"></i></div>
    <div class="panel-vacio-titulo">Sin eliminaciones registradas</div>
    <div class="panel-vacio-desc">Las eliminaciones RGPD aparecerán aquí con su evidencia.</div>
  </div>
  <?php else: ?>
  <div class="contenedor-tabla">
    <table class="tabla-datos" id="tablaRGPD">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Admin</th>
          <th>Entidad</th>
          <th>ID Registro</th>
          <th>Descripción</th>
          <th>Motivo</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($eliminaciones as $eliminacion): ?>
        <tr>
          <td><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($eliminacion['fecha']))) ?></td>
          <td><?= Security::escapeHtml($eliminacion['nombreDirector'] ?? 'Admin #'.$eliminacion['idAdmin']) ?></td>
          <td><span class="texto-estado azul"><?= Security::escapeHtml($eliminacion['entidad']) ?></span></td>
          <td><?= (int)$eliminacion['idRegistro'] ?></td>
          <td><?= Security::escapeHtml($eliminacion['descripcion']) ?></td>
          <td style="max-width:300px;white-space:normal;"><?= Security::escapeHtml($eliminacion['motivo']) ?></td>
          <td><?= Security::escapeHtml($eliminacion['ip'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . "/../comunes/footer.php"; ?>
<script>
(function () {
    // Filter select options by search text
    window.filtrarRGPD = function(prefix) {
        var q    = (document.getElementById(prefix + '-buscar').value || '').toLowerCase();
        var sel  = document.getElementById(prefix + '-select');
        Array.from(sel.options).forEach(function(o) {
            if (!o.value) return; // keep placeholder
            o.hidden = q && o.text.toLowerCase().indexOf(q) === -1;
        });
        // Reset selection if current choice is hidden
        if (sel.selectedOptions[0] && sel.selectedOptions[0].hidden) sel.value = '';
    };

    // Confirm hard-delete
    $('#form-rgpd-borrar').on('submit', function (e) {
        var id = $('#borrar-select').val();
        var motivo = $('#borrar-motivo').val().trim();
        if (!id || !motivo) return;
        
        if (window.ModalConfirm) {
            e.preventDefault();
            var $form = $(this);
            ModalConfirm.prompt('¿Eliminar PERMANENTEMENTE todos los datos del estudiante #' + id + '?\n\nEsta acción no se puede deshacer. Se guardará un registro de evidencia.').then(function(res) {
                if (res) $form[0].submit();
            });
        } else {
            if (!confirm('¿Eliminar PERMANENTEMENTE todos los datos del estudiante #' + id + '?\n\nEsta acción no se puede deshacer. Se guardará un registro de evidencia.')) {
                e.preventDefault();
            }
        }
    });

    // Confirm log purge
    $('#form-purgar').on('submit', function (e) {
        var years = $(this).find('[name=years]').val();
        
        if (window.ModalConfirm) {
            e.preventDefault();
            var $form = $(this);
            ModalConfirm.prompt('¿Eliminar todos los registros de log de más de ' + years + ' años? Esta acción no se puede deshacer.').then(function(res) {
                if (res) $form[0].submit();
            });
        } else {
            if (!confirm('¿Eliminar todos los registros de log de más de ' + years + ' años? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        }
    });

    iniciarPaginacion('tablaRGPD', 15);
}());
</script>
