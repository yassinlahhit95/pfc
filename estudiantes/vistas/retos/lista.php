<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$retos = listarRetos();

$tituloDelPagina = "Mis Retos - Portal Estudiantes";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Mis Retos</h1>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Lista de Retos Disponibles</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre del Reto</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Horas</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($retos) { ?>
                    <?php foreach ($retos as $reto) { ?>
                        <tr>
                            <td class="texto-negrita"><?php echo $reto['nombreReto']; ?></td>
                            <td><?php echo $reto['fechaInicio']; ?></td>
                            <td><?php echo $reto['fechaFin']; ?></td>
                            <td><?php echo $reto['horasReto']; ?> h</td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">
                            <i class="fas fa-tasks"></i> No hay retos asignados actualmente.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>