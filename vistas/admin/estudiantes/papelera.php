<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$estudiantesEliminados = listarEstudiantesEliminados();

$titulo_pagina = 'Papelera de Estudiantes';
$seccion = 'estudiantes';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-trash-alt" style="color:var(--rojo);margin-right:8px;"></i>Papelera de Estudiantes</h1>
        <p class="subtitulo-encabezado">Estudiantes eliminados. Puedes restaurarlos con todos sus datos y notas.</p>
    </div>
    <div class="acciones-pagina">
        <a href="verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver a Estudiantes</a>
    </div>
</div>

<div class="panel">
    <?php if (empty($estudiantesEliminados)): ?>
        <div style="text-align:center;padding:60px 20px;color:var(--dim);">
            <i class="fas fa-check-circle" style="font-size:3rem;color:var(--verde);display:block;margin-bottom:16px;"></i>
            <h3 style="margin:0 0 8px;color:var(--text);">La papelera está vacía</h3>
            <p style="margin:0;">No hay estudiantes eliminados en este momento.</p>
        </div>
    <?php else: ?>
        <div class="contenedor-tabla">
            <table class="tabla-datos" id="tabla-papelera-estudiantes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE COMPLETO</th>
                        <th>EMAIL</th>
                        <th>CICLO</th>
                        <th>NIVEL</th>
                        <th>FECHA ELIMINACIÓN</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantesEliminados as $estudiante): ?>
                    <tr>
                        <td><?= (int)$estudiante['idEstudiante'] ?></td>
                        <td><b><?= mb_strtoupper(Security::escapeHtml($estudiante['nombreEstudiante']), 'UTF-8') ?></b></td>
                        <td><?= Security::escapeHtml($estudiante['emailEstudiante']) ?></td>
                        <td><?= mb_strtoupper(Security::escapeHtml($estudiante['nombreCiclo'] ?? '—'), 'UTF-8') ?></td>
                        <td>
                            <span class="texto-estado <?= ($estudiante['idNivel'] ?? 0) == 2 ? 'verde' : 'azul' ?>">
                                <?= ($estudiante['idNivel'] ?? 0) == 2 ? 'Grado Superior' : 'Grado Medio' ?>
                            </span>
                        </td>
                        <td>
                            <?= !empty($estudiante['fecha_eliminacion'])
                                ? date('d/m/Y H:i', strtotime($estudiante['fecha_eliminacion']))
                                : '<span class="texto-suave">—</span>' ?>
                        </td>
                        <td>
                            <form method="POST" action="../../../controladores/admin/estudiantes/restaurar.php"
                                  data-ajax-confirm="¿Restaurar a «<?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>»? Todos sus datos y notas quedarán intactos.">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                <input type="hidden" name="idEstudiante" value="<?= (int)$estudiante['idEstudiante'] ?>">
                                <button type="submit" class="boton-primario boton-pequeno" title="Restaurar estudiante">
                                    <i class="fas fa-undo"></i> Restaurar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p style="margin-top:16px;padding:0 4px;color:var(--dim);font-size:.85rem;">
            <i class="fas fa-info-circle"></i>
            Total: <?= count($estudiantesEliminados) ?> estudiante<?= count($estudiantesEliminados) !== 1 ? 's' : '' ?> en papelera.
            Al restaurar, todos sus datos, notas y archivos quedarán disponibles de nuevo.
        </p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    iniciarPaginacion('tabla-papelera-estudiantes', 15);
});
</script>
