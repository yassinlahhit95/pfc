<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = $_GET['idEstudiante'] ?? '';
if (empty($idEstudiante)) {
    header("Location: verPagosGeneral.php");
    exit;
}

$estudiante = obtenerEstudiantePorId($idEstudiante);
$pagos = obtenerPagosPorEstudiante($idEstudiante);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "AULAPRO | HISTORIAL DE PAGOS";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>HISTORIAL DE PAGOS: <?= $estudiante['nombreEstudiante'] ?></h1>
    <a href="verPagosGeneral.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

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
                <?php if (empty($pagos) || is_numeric($pagos)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay registros de pagos para este estudiante</td></tr>
                <?php } else { ?>
                    <?php foreach ($pagos as $p) { ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($p['fechaPago'])) ?></td>
                        <td><span class="etiqueta-pago"><?= ucfirst($p['tipoPago']) ?></span></td>
                        <td><?= number_format($p['monto'], 2) ?> €</td>
                        <td><?= date('d/m/Y', strtotime($p['fechaProximoPago'])) ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




