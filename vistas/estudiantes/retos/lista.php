<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_retos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idEstudiante = $_SESSION['idEstudiante'];

require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$datosEstudiante = obtenerEstudiantePorId($idEstudiante);
if (!$datosEstudiante) {
    $_SESSION['errores'] = "ERROR AL RECUPERAR DATOS DEL ESTUDIANTE.";
    header("Location: ../inicio/dashboard.php");
    exit;
}

$idCiclo = $datosEstudiante['idCiclo'] ?? 0;
$nombreCiclo = $datosEstudiante['nombreCiclo'] ?? 'SIN ASIGNAR';

$retos = ($idCiclo > 0) ? listarRetosPorCiclo($idCiclo) : [];

$tituloDelPagina = "AULAPRO | MIS RETOS";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS RETOS</h1>
    <p class="subtitulo">Retos asignados a tu ciclo: <?= Security::escapeHtml($nombreCiclo) ?></p>
</div>


<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Lista de Retos Disponibles</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre del Reto</th>
                    <th>Materiales</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Horas Estimadas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($retos)) { ?>
                    <tr>
                        <td colspan="5" class="vacio">No hay retos registrados para este ciclo formativo.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($retos as $reto) { 
                        $archivos = obtenerArchivosReto($reto['idReto']);
                    ?>
                        <tr>
                            <td class="texto-negrita"><?= Security::escapeHtml(strtoupper($reto['nombreReto'])) ?></td>
                            <td>
                                <?php if (empty($archivos)): ?>
                                    <span class="texto-suave small">Sin adjuntos</span>
                                <?php else: ?>
                                    <div class="materiales-container">
                                        <a href="../../../controladores/comunes/descargar_zip_reto.php?id=<?= $reto['idReto'] ?>" class="materiales-main-btn">
                                            <i class="fas fa-file-archive"></i> ZIP
                                        </a>
                                        <div class="materiales-dropdown">
                                            <div class="small fw-bold mb-2 px-2 text-muted text-uppercase" style="font-size: 9px;">Recursos:</div>
                                            <?php foreach ($archivos as $archivo):
                                                $isPdf = ($archivo['tipoArchivo'] === 'pdf');
                                                $icon = $isPdf ? 'fa-file-pdf text-danger' : 'fa-image text-primary';
                                            ?>
                                                <a href="../../../<?= Security::escapeHtml($archivo['rutaArchivo']) ?>" target="_blank" class="dropdown-file-item">
                                                    <i class="fas <?= $icon ?>"></i>
                                                    <span class="text-truncate"><?= Security::escapeHtml($archivo['nombreArchivo']) ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                            <hr class="my-2 opacity-10">
                                            <a href="../../../controladores/comunes/descargar_zip_reto.php?id=<?= $reto['idReto'] ?>" class="dropdown-file-item fw-bold">
                                                <i class="fas fa-download"></i> Descargar Todo (.zip)
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= Security::escapeHtml(date('d/m/Y', strtotime($reto['fechaInicio']))) ?></td>
                            <td><?= Security::escapeHtml(date('d/m/Y', strtotime($reto['fechaFin']))) ?></td>
                            <td><?= Security::escapeHtml($reto['horasReto']) ?> h</td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
