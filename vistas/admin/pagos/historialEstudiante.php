<?php
session_start();
require_once "../../../modelos/pagos.php";
require_once "../../../modelos/estudiantes.php";

$idEstudiante = isset($_GET['idEstudiante']) ? $_GET['idEstudiante'] : '';
if (empty($idEstudiante)) {
    header("Location: verPagosGeneral.php");
    exit;
}

$estudiante = obtenerEstudiantePorId($idEstudiante);
$pagos = obtenerPagosPorEstudiante($idEstudiante);

$titulo_pagina = "Historial de Pagos - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Historial de Pagos: <?php echo $estudiante['nombreEstudiante']; ?></h1>
    <a href="verPagosGeneral.php" class="boton-secundario">Volver a General</a>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Próximo Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pagos)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay registros de pagos para este estudiante</td></tr>
                <?php } else { ?>
                    <?php foreach ($pagos as $p) { ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($p['fechaPago'])); ?></td>
                        <td><span class="etiqueta-pago"><?php echo ucfirst($p['tipoPago']); ?></span></td>
                        <td><?php echo number_format($p['monto'], 2); ?> €</td>
                        <td><?php echo date('d/m/Y', strtotime($p['fechaProximoPago'])); ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
