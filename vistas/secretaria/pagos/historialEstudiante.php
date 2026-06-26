<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_pagos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
if (empty($idEstudiante)) {
    header("Location: verPagos.php");
    exit;
}

$estudiante = obtenerEstudiantePorId($idEstudiante);
$listaPagos = listarPagosPorEstudiante($idEstudiante);

$titulo_pagina = "AULAPRO | HISTORIAL DE PAGOS";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>HISTORIAL DE PAGOS: <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?></h1>
    <a href="verPagos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
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
                <?php if (empty($listaPagos)) { ?>
                    <tr><td colspan="4" class="vacio">No hay registros de pagos para este estudiante</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaPagos as $pago) { ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($pago['fechaPago'])) ?></td>
                        <td><span class="texto-pago"><?= ucfirst($pago['tipoPago']) ?></span></td>
                        <td><?= number_format($pago['monto'], 2) ?> €</td>
                        <td><?= !empty($pago['fechaProximoPago']) ? date('d/m/Y', strtotime($pago['fechaProximoPago'])) : '—' ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
