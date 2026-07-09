<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/tutores.php";

$listaTutores = listarTodosLosTutores();

$titulo_pagina = "AULAPRO | GESTIÓN DE TUTORES";
$seccion = 'tutores';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>SISTEMA PARENTAL</h1>
    <div class="acciones-cabecera" style="display:flex;gap:10px;align-items:center;">
        <span class="texto-suave small"><?= count($listaTutores) ?> familias registradas</span>
        <a href="agregarTutor.php" class="boton-primario">
            <i class="fas fa-plus"></i> AGREGAR FAMILIAR
        </a>
    </div>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaTutores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>DNI</th>
                    <th>HIJOS VINCULADOS</th>
                    <th>CORREO ELECTRÓNICO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaTutores)) { ?>
                    <tr>
                        <td colspan="6" class="vacio">No hay tutores registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaTutores as $t): 
                        $hijos = listarEstudiantesPorTutor($t['idTutor']);
                    ?>
                    <tr>
                        <td><?= Security::escapeHtml($t['idTutor']) ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:var(--surface-2);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;border:1px solid var(--border);flex-shrink:0;">
                                    <?= mb_strtoupper(mb_substr($t['nombreTutor'], 0, 1), 'UTF-8') ?>
                                </div>
                                <b><?= mb_strtoupper(Security::escapeHtml($t['nombreTutor']), 'UTF-8') ?></b>
                            </div>
                        </td>
                        <td><?= Security::escapeHtml($t['dniTutor']) ?></td>
                        <td>
                            <?php if (empty($hijos)): ?>
                                <span class="texto-estado gris">Sin vinculación</span>
                            <?php else: ?>
                                <div style="display:flex;flex-direction:column;gap:4px;">
                                    <?php foreach ($hijos as $h): ?>
                                        <span style="font-size:.83rem;"><i class="fas fa-user-graduate" style="opacity:.5;margin-right:4px;"></i><?= Security::escapeHtml($h['nombreEstudiante']) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= Security::escapeHtml($t['emailTutor']) ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="modificarTutor.php?idTutor=<?= (int)$t['idTutor'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a class="recurso-menu-item" href="#"
                                       onclick="restablecerPasswordTutor(<?= (int)$t['idTutor'] ?>, '<?= Security::escapeHtml(addslashes($t['nombreTutor'])) ?>'); return false;">
                                        <i class="fas fa-key"></i> Restablecer contraseña
                                    </a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$t['idTutor'] ?>"
                                       data-tipo="Tutor"
                                       data-nombre="<?= Security::escapeHtml($t['nombreTutor']) ?>"
                                       data-extra="<?= Security::escapeHtml($t['dniTutor']) ?>"
                                       data-url="/controladores/admin/tutores/borrar.php"
                                       data-campo="idTutor"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

<form id="form-reset-tutor" method="POST" action="../../../controladores/admin/tutores/restablecer_password.php" hidden>
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="idTutor" id="reset-tutor-id" value="">
</form>

<script>
iniciarPaginacion('tablaTutores', 15);

function restablecerPasswordTutor(idTutor, nombre) {
    var pedir = window.ModalConfirm
        ? ModalConfirm.prompt('Se generará una contraseña temporal para «' + nombre + '» y se le enviará por email. Deberá cambiarla en su primer acceso. ¿Continuar?', 'Restablecer contraseña')
        : Promise.resolve(confirm('¿Restablecer la contraseña de ' + nombre + '?'));
    pedir.then(function (ok) {
        if (!ok) return;
        document.getElementById('reset-tutor-id').value = idTutor;
        document.getElementById('form-reset-tutor').submit();
    });
}
</script>
