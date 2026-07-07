<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/configuracion.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";

$idProfesor = $_SESSION['idProfesor'];
$tfgs = listarTFGsPorProfesor($idProfesor);
$cfg = obtenerConfiguracionCentro();
$entregaAbierta = (bool)($cfg['feature_subida_tfg'] ?? 1);
$saasLocked = FeatureGuard::isLocked();

$calificacionesTFG = [];
foreach ($tfgs as $tfg) {
    $calificacionesTFG[$tfg['idEstudiante']] = obtenerCalificacionTFG($tfg['idEstudiante']);
}

$tituloDelPagina = "AULAPRO | GESTION DE TFGS";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTION DE TFGS ENTREGADOS</h1>
</div>

<div class="panel margen-abajo">
    <div class="feature-card">
        <div class="feature-info">
            <i class="fas fa-file-upload feature-icon" style="color: #8b5cf6;"></i>
            <div>
                <div class="feature-label">Entrega de TFG</div>
                <div class="feature-desc">
                    <?= $entregaAbierta ? 'Los estudiantes pueden subir su TFG.' : 'La entrega está cerrada para los estudiantes.' ?>
                </div>
            </div>
        </div>
        <?php if ($saasLocked): ?>
            <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:.8rem;font-weight:700;background:#f3f4f6;color:#6b7280;white-space:nowrap;"><i class="fas fa-lock"></i> <?= $entregaAbierta ? 'Activo' : 'Inactivo' ?></span>
        <?php else: ?>
        <label class="switch">
            <input type="checkbox" id="toggle-subida-tfg" <?= $entregaAbierta ? 'checked' : '' ?>>
            <span class="slider round"></span>
        </label>
        <?php endif; ?>
    </div>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Fecha de Subida</th>
                    <th>Nota TFG</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tfgs)) { ?>
                    <?php foreach ($tfgs as $tfg) {
                        $notaTFG = $calificacionesTFG[$tfg['idEstudiante']];
                    ?>
                        <tr>
                            <td><?= Security::escapeHtml($tfg['nombreEstudiante'] ) ?></td>
                            <td><?= Security::escapeHtml($tfg['nombreCiclo'] ) ?></td>
                            <td><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG']))) ?></td>
                            <td>
                                <?php if (!empty($notaTFG)) { ?>
                                    <?php if ($notaTFG['nota'] >= 5) { ?>
                                        <span class="texto-verde texto-negrita"><?= Security::escapeHtml($notaTFG['nota'] ) ?></span>
                                    <?php } else { ?>
                                        <span class="texto-rojo texto-negrita"><?= Security::escapeHtml($notaTFG['nota'] ) ?></span>
                                    <?php } ?>
                                <?php } else { ?>
                                    <span class="texto-suave">Sin calificar</span>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="botones-accion">
                                      <a href="../../../controladores/comunes/verTFG.php?id=<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>&modo=descarga" target="_blank" class="btn-accion btn-ver"><i class="fas fa-download"></i></a>
                                      <button type="button" class="btn-accion btn-editar" onclick="toggleFormCalificar('form-<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>')">
                                          <i class="fas fa-star"></i>
                                      </button>
                                    <a href="borrarPfc.php?id=<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>" class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></a>
                                </div>

                                <div id="form-<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>" style="display: none; margin-top: 10px;">
                                    <form action="../../../controladores/profesores/pfc/calificar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>">
                                        <div class="campo">
                                            <label>Nota (0-10):</label>
                                            <input type="text" name="nota" value="<?= Security::escapeHtml(!empty($notaTFG) ? $notaTFG['nota'] : '') ?>" placeholder="Ej: 7.5" class="input-pequeno">
                                        </div>
                                        <div class="campo">
                                            <label>Observaciones:</label>
                                            <textarea name="observaciones" rows="2" placeholder="Comentarios opcionales..."><?= Security::escapeHtml(!empty($notaTFG) ? $notaTFG['observaciones'] : '') ?></textarea>
                                        </div>
                                        <div class="campo">
                                            <label>
                                                <input type="checkbox" name="notificarEstudiante" value="1">
                                                Notificar al estudiante por email y push
                                            </label>
                                        </div>
                                        <input type="submit" name="calificarTFG" class="boton-primario" value="Guardar Nota">
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="vacio">No hay TFGs subidos todavia.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleFormCalificar(idFormulario) {
    $('#' + idFormulario).toggle();
}

const toggleEl = document.getElementById('toggle-subida-tfg');
if (toggleEl) {
    toggleEl.addEventListener('change', function() {
        var estado = this.checked ? 1 : 0;
        var toggle = this;
        fetch('../../../controladores/profesores/pfc/toggle_subida.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'estado=' + estado + '&csrf_token=<?= Security::generateCSRFToken() ?>'
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status !== 'success') {
                toggle.checked = !toggle.checked;
                Toast.show(data.message || 'No se pudo actualizar la configuración.', 'error');
            } else {
                var desc = toggle.closest('.feature-card').querySelector('.feature-desc');
                desc.textContent = toggle.checked
                    ? 'Los estudiantes pueden subir su TFG.'
                    : 'La entrega está cerrada para los estudiantes.';
            }
        })
        .catch(function() {
            toggle.checked = !toggle.checked;
            Toast.show('Error de red.', 'error');
        });
    });
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>



