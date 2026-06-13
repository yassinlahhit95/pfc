<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$retos = listarRetosDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | RETOS";
$seccionActual   = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTIÓN DE RETOS</h1>
    <a href="agregar.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO RETO</a>
</div>

<?php if ($errores) { ?><div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div><?php } ?>
<?php if ($exito)   { ?><div class="mensaje-exito"><?= Security::escapeHtml($exito)   ?></div><?php } ?>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
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
                                            <?php foreach ($archivos as $arch): 
                                                $isPdf = ($arch['tipoArchivo'] === 'pdf');
                                                $icon = $isPdf ? 'fa-file-pdf text-danger' : 'fa-image text-primary';
                                            ?>
                                                <a href="../../../<?= $arch['rutaArchivo'] ?>" target="_blank" class="dropdown-file-item">
                                                    <i class="fas <?= $icon ?>"></i>
                                                    <span class="text-truncate"><?= Security::escapeHtml($arch['nombreArchivo']) ?></span>
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
                                        <a class="recurso-menu-item peligro" href="borrarReto.php?id=<?= (int)$reto['idReto'] ?>"><i class="fas fa-trash"></i> Eliminar</a>
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
