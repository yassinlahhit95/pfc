<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/tutores.php";

$listaTutores = listarTodosLosTutores();
$hijosPorTutor = listarHijosPorTutores(array_column($listaTutores, 'idTutor'));

$titulo_pagina = "AULAPRO | SISTEMA PARENTAL";
$seccion = 'tutores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>SISTEMA PARENTAL</h1>
    <div class="acciones-cabecera" style="display:flex;gap:10px;align-items:center;">
        <span class="texto-suave small"><?= count($listaTutores) ?> familias registradas</span>
    </div>
</div>

<div class="panel margen-abajo">
    <div class="formulario">
        <div class="campo ancho-total">
            <label for="filtroTutores">BUSCAR</label>
            <input type="text" id="filtroTutores" placeholder="Buscar por nombre, DNI, email o estudiante..."
                   autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other">
        </div>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaTutores">
            <thead>
                <tr>
                    <th>NOMBRE COMPLETO</th>
                    <th>DNI</th>
                    <th>HIJOS VINCULADOS</th>
                    <th>CORREO ELECTRÓNICO</th>
                    <th>TELÉFONO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaTutores)) { ?>
                    <tr>
                        <td colspan="6" class="vacio">No hay familiares registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaTutores as $tutor):
                        $hijos = $hijosPorTutor[$tutor['idTutor']] ?? [];
                    ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:var(--surface-2);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;border:1px solid var(--border);flex-shrink:0;">
                                    <?= mb_strtoupper(mb_substr($tutor['nombreTutor'], 0, 1), 'UTF-8') ?>
                                </div>
                                <b><?= mb_strtoupper(Security::escapeHtml($tutor['nombreTutor']), 'UTF-8') ?></b>
                            </div>
                        </td>
                        <td><?= Security::escapeHtml($tutor['dniTutor']) ?></td>
                        <td>
                            <?php if (empty($hijos)): ?>
                                <span class="texto-estado gris">Sin vinculación</span>
                            <?php else: ?>
                                <div style="display:flex;flex-direction:column;gap:4px;">
                                    <?php foreach ($hijos as $hijo): ?>
                                        <span style="font-size:.83rem;"><i class="fas fa-user-graduate" style="opacity:.5;margin-right:4px;"></i><?= Security::escapeHtml($hijo['nombreEstudiante']) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= Security::escapeHtml($tutor['emailTutor']) ?></td>
                        <td><?= $tutor['telefonoTutor'] !== '' && $tutor['telefonoTutor'] !== null ? Security::escapeHtml($tutor['telefonoTutor']) : '—' ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="#"
                                       onclick="restablecerPasswordTutor(<?= (int)$tutor['idTutor'] ?>, '<?= Security::escapeHtml(addslashes($tutor['nombreTutor'])) ?>'); return false;">
                                        <i class="fas fa-key"></i> Restablecer contraseña
                                    </a>
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>

<form id="form-reset-tutor" method="POST" action="../../../controladores/secretaria/tutores/restablecer_password.php" hidden>
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="idTutor" id="reset-tutor-id" value="">
</form>

<script>
iniciarPaginacion('tablaTutores', 15);
// Filtrado en vivo: se ejecuta en cada pulsación
document.getElementById('filtroTutores').addEventListener('input', function () {
    filtrarTabla('filtroTutores', 'tablaTutores');
});

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
