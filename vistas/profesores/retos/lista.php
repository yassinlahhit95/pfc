<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$retos = obtenerRetosDeProfesor($idProfesor);

$tituloDelPagina = "Retos - Portal Profesores";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Gestión de Retos</h1>
    <a href="agregar.php" class="boton-primario">Nuevo Reto</a>
</div>

<?php if ($error) { ?>
    <div class="alerta-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="alerta-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Horas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($retos) { ?>
                    <?php foreach ($retos as $reto) { ?>
                        <tr>
                            <td class="texto-negrita"><?= $reto['nombreReto'] ?></td>
                            <td><?= $reto['fechaInicio'] ?></td>
                            <td><?= $reto['fechaFin'] ?></td>
                            <td><?= $reto['horasReto'] ?> h</td>
                            <td>
                                <a href="editar.php?id=<?= $reto['idReto'] ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                <a href="../../../controladores/profesores/retos/borrar.php?id=<?= $reto['idReto'] ?>" class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No hay retos registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>



