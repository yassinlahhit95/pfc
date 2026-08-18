<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_retos');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor   = $_SESSION['idProfesor'];
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
$retos = ($esTutor && $idCicloTutor)
    ? listarRetosPorCiclo($idCicloTutor)
    : listarRetosDeProfesor($idProfesor);

$titulo_pagina = "Retos";
$seccionActual   = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Gestión de Retos</h1>
    <a href="agregar.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO RETO</a>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tabla-retos-profesores">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Materiales</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Horas</th>
                    <th style="text-align:right;width:60px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($retos) { ?>
                    <?php foreach ($retos as $reto) {
                        $archivos = obtenerArchivosReto($reto['idReto']);
                    ?>
                        <tr>
                            <td class="texto-negrita"><?= Security::escapeHtml($reto['nombreReto']) ?></td>
                            <td>
                                <?php if (empty($archivos)): ?>
                                    <span class="texto-suave small">Sin adjuntos</span>
                                <?php else: ?>
                                    <div class="materiales-container">
                                        <a href="../../../controladores/comunes/descargar_zip_reto.php?id=<?= $reto['idReto'] ?>" class="materiales-main-btn">
                                            <i class="fas fa-file-archive"></i> ZIP
                                        </a>
                                        <div class="materiales-dropdown">
                                            <div class="small fw-bold mb-2 px-2 text-muted text-uppercase" style="font-size: 9px;">Archivos:</div>
                                            <?php foreach ($archivos as $archivo):
                                                $isPdf = ($archivo['tipoArchivo'] === 'pdf');
                                                $icon = $isPdf ? 'fa-file-pdf text-danger' : 'fa-image text-primary';
                                            ?>
                                                <a href="../../../controladores/comunes/ver_archivo_reto.php?id=<?= (int)$archivo['idArchivo'] ?>" target="_blank" class="dropdown-file-item">
                                                    <i class="fas <?= $icon ?>"></i>
                                                    <span class="text-truncate"><?= Security::escapeHtml($archivo['nombreArchivo']) ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                            <hr class="my-2 opacity-10">
                                            <a href="../../../controladores/comunes/descargar_zip_reto.php?id=<?= $reto['idReto'] ?>" class="dropdown-file-item fw-bold">
                                                <i class="fas fa-download"></i> Todo (.zip)
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= Security::escapeHtml(date('d/m/Y', strtotime($reto['fechaInicio']))) ?></td>
                            <td><?= Security::escapeHtml(date('d/m/Y', strtotime($reto['fechaFin']))) ?></td>
                            <td><?= Security::escapeHtml($reto['horasReto']) ?> h</td>
                            <td style="text-align:right;">
                                <div class="recurso-menu-wrap">
                                    <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                    <div class="recurso-menu">
                                        <a class="recurso-menu-item" href="editar.php?id=<?= (int)$reto['idReto'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                        <div class="recurso-menu-sep"></div>
                                        <a class="recurso-menu-item peligro" href="#"
                                           data-modal-borrar
                                           data-id="<?= (int)$reto['idReto'] ?>"
                                           data-tipo="Reto"
                                           data-nombre="<?= Security::escapeHtml($reto['nombreReto']) ?>"
                                           data-url="/controladores/profesores/retos/borrar.php"
                                           data-campo="idReto"><i class="fas fa-trash"></i> Eliminar</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="vacio">No hay retos registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    iniciarPaginacion('tabla-retos-profesores', 15);
});
</script>
