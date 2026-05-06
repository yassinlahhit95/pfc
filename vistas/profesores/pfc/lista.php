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

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
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
                                <a href="../../../public/uploads/pfc/<?= $tfg['archivoTFG'] ?>" target="_blank" class="btn-accion btn-ver" download="<?= $nombreDescarga ?>"><i class="fas fa-download"></i></a>
                                <a href="../../../controladores/profesores/pfc/borrar.php?id=<?= $tfg['idEstudiante'] ?>" class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No hay TFGs subidos todavia.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




