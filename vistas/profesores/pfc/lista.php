<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$idProfesor = $_SESSION['idProfesor'];
$tfgs = listarTFGsPorProfesor($idProfesor);

$tituloDelPagina = "Gestión de TFGs - Portal Profesores";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Gestión de TFGs Entregados</h1>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Fecha de Subida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tfgs) { ?>
                    <?php foreach ($tfgs as $tfg) { 
                        $nombreLimpio = str_replace(' ', '_', $tfg['nombreEstudiante']);
                        $nombreDescarga = "TFG_" . $nombreLimpio . "_" . date('d-m-Y_H-i-s') . ".pdf";
                    ?>
                        <tr>
                            <td><strong><?= $tfg['nombreEstudiante'] ?></strong></td>
                            <td><?= $tfg['nombreCiclo'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG'])) ?></td>
                            <td>
                                <div class="botones-accion">
                                    <a href="../../../public/uploads/pfc/<?= $tfg['archivoTFG'] ?>" target="_blank" class="btn-accion btn-ver" download="<?= $nombreDescarga ?>" title="Descargar"><i class="fas fa-download"></i></a>
                                    <form action="../../../controladores/profesores/pfc/borrar.php" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este archivo?');" style="display:inline;">
                                        <input type="hidden" name="idEstudiante" value="<?= $tfg['idEstudiante'] ?>">
                                        <button type="submit" class="btn-accion btn-eliminar" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No hay TFGs subidos todavía.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>




