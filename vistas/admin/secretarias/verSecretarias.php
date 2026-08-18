<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/form_helpers.php';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . '/../../../modelos/secretarias.php';

$listaSecretarias = listarTodasLasSecretarias();

$titulo_pagina = 'Secretarias';
$seccion = 'secretarias';
include_once __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>Secretarias</h1>
    <div class="acciones-cabecera" style="display:flex;gap:10px;align-items:center;">
        <span class="texto-suave small"><?= count($listaSecretarias) ?> registradas</span>
        <a href="historialSecretarias.php" class="boton-secundario">
            <i class="fas fa-history"></i> HISTORIAL
        </a>
        <a href="agregarSecretaria.php" class="boton-primario">
            <i class="fas fa-plus"></i> AÑADIR SECRETARIA
        </a>
    </div>
</div>

<?php if (empty($listaSecretarias)): ?>
<div class="panel-vacio">
    <div class="panel-vacio-icono"><i class="fas fa-user-tie"></i></div>
    <p class="panel-vacio-titulo">No hay secretarias registradas</p>
    <p class="panel-vacio-desc">Añade la primera secretaria para que pueda acceder al sistema.</p>
    <a href="agregarSecretaria.php" class="boton-primario" style="margin-top:16px;"><i class="fas fa-plus"></i> Añadir secretaria</a>
</div>
<?php else: ?>

<div class="panel margen-abajo">
    <input type="text" id="buscarSecretaria" class="buscador"
           autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
           data-lpignore="true" data-1p-ignore="true" data-form-type="other"
           placeholder="Buscar por nombre o email…"
           oninput="filtrarTabla('buscarSecretaria','tablaSecretarias')">
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaSecretarias">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>EMAIL</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaSecretarias as $secretaria): ?>
                <tr>
                    <td><?= (int)$secretaria['idSecretaria'] ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--surface-2);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;border:1px solid var(--border);flex-shrink:0;">
                                <?= mb_strtoupper(mb_substr($secretaria['nombreSecretaria'], 0, 1), 'UTF-8') ?>
                            </div>
                            <b><?= Security::escapeHtml($secretaria['nombreSecretaria']) ?></b>
                        </div>
                    </td>
                    <td><?= Security::escapeHtml($secretaria['emailSecretaria']) ?></td>
                    <td>
                        <button class="btn-toggle-activo texto-estado <?= $secretaria['activoSecretaria'] ? 'verde' : 'rojo' ?>"
                                data-id="<?= (int)$secretaria['idSecretaria'] ?>"
                                data-activo="<?= (int)$secretaria['activoSecretaria'] ?>"
                                title="<?= $secretaria['activoSecretaria'] ? 'Clic para desactivar' : 'Clic para activar' ?>"
                                style="cursor:pointer;border:none;background:none;padding:0;">
                            <i class="fas <?= $secretaria['activoSecretaria'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                            <?= $secretaria['activoSecretaria'] ? 'Activa' : 'Inactiva' ?>
                        </button>
                    </td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button type="button" class="recurso-menu-btn" title="Opciones">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="modificarSecretaria.php?id=<?= (int)$secretaria['idSecretaria'] ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a class="recurso-menu-item" href="historialSecretarias.php?id=<?= (int)$secretaria['idSecretaria'] ?>">
                                    <i class="fas fa-history"></i> Ver historial
                                </a>
                                <div class="recurso-menu-sep"></div>
                                <a class="recurso-menu-item peligro" href="#"
                                   data-modal-borrar
                                   data-id="<?= (int)$secretaria['idSecretaria'] ?>"
                                   data-tipo="Secretaria"
                                   data-nombre="<?= Security::escapeHtml($secretaria['nombreSecretaria']) ?>"
                                   data-extra="<?= Security::escapeHtml($secretaria['emailSecretaria']) ?>"
                                   data-url="/controladores/admin/secretarias/borrar.php"
                                   data-campo="idSecretaria">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
var _csrfToken = <?= Security::jsonEncodeSafe(Security::generateCSRFToken()) ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (typeof iniciarPaginacion === 'function') {
        iniciarPaginacion('tablaSecretarias', 15);
    }
});

document.querySelectorAll('.btn-toggle-activo').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id     = this.dataset.id;
        var activo = this.dataset.activo === '1' ? 0 : 1;
        var self   = this;
        $.post('/controladores/admin/secretarias/toggleActivo.php',
            { idSecretaria: id, activo: activo, csrf_token: _csrfToken },
            function(res) {
                if (res.ok) {
                    self.dataset.activo = activo;
                    var isActiva = activo === 1;
                    self.className = 'btn-toggle-activo texto-estado ' + (isActiva ? 'verde' : 'rojo');
                    self.title = isActiva ? 'Clic para desactivar' : 'Clic para activar';
                    self.innerHTML = '<i class="fas ' + (isActiva ? 'fa-check-circle' : 'fa-times-circle') + '"></i> '
                                   + (isActiva ? 'Activa' : 'Inactiva');
                    if (window.Toast) Toast.show(res.msg, 'success');
                } else {
                    if (window.Toast) Toast.show(res.msg || 'Error al cambiar estado.', 'error');
                }
            }, 'json');
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
