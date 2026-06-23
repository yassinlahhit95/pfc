<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/pagos.php";
$pagos = listarTodosLosPagos();

$titulo_pagina = 'AULAPRO | PAGOS';
$seccion = 'pagos';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>GESTIÓN DE PAGOS</h1>
    <a href="agregarPago.php" class="boton-primario"><i class="fas fa-plus"></i> Registrar Pago</a>
</div>

<div class="panel margen-abajo">
    <div class="caja">
        <div class="campo relleno">
            <label for="buscarPago">Buscar</label>
            <input type="text" id="buscarPago" placeholder="Estudiante, tipo de pago...">
        </div>
    </div>
</div>

<div class="panel">
    <?php if (empty($pagos)): ?>
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-euro-sign"></i></div>
            <div class="panel-vacio-titulo">Sin pagos registrados</div>
            <div class="panel-vacio-desc">Registra el primer pago para comenzar.</div>
        </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaPagos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Tipo</th>
                    <th>Importe</th>
                    <th>Fecha pago</th>
                    <th>Próximo pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $p): ?>
                <tr>
                    <td><b><?= Security::escapeHtml($p['nombreEstudiante']) ?></b></td>
                    <td><?= Security::escapeHtml($p['nombreCiclo']) ?></td>
                    <td><span class="texto-estado azul"><?= Security::escapeHtml($p['tipoPago']) ?></span></td>
                    <td><b><?= number_format((float)$p['monto'], 2, ',', '.') ?> €</b></td>
                    <td><?= date('d/m/Y', strtotime($p['fechaPago'])) ?></td>
                    <td><?= !empty($p['fechaProximoPago']) ? date('d/m/Y', strtotime($p['fechaProximoPago'])) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
filtrarTabla('buscarPago', 'tablaPagos');
iniciarPaginacion('tablaPagos', 15);
</script>
