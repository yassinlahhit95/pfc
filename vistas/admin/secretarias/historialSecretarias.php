<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/Paginator.php";
require_once __DIR__ . "/../../../modelos/secretarias.php";
require_once __DIR__ . "/../../../modelos/log.php";

// Paginación servidor (LIMIT/OFFSET) — esta tabla crece indefinidamente
// (una fila por cada acción de cada secretaría), así que nunca se trae
// todo a la vez como hace la paginación cliente en el resto del panel.
$porPagina      = 20;
$totalHistorial = contarHistorialSecretarias();
$totalPaginas   = max(1, (int)ceil($totalHistorial / $porPagina));
$pagina         = max(1, min($totalPaginas, (int)($_GET['pagina'] ?? 1)));
$historial      = listarHistorialSecretarias(null, $porPagina, ($pagina - 1) * $porPagina);

$titulo_pagina = 'Historial de Secretarias';
$seccion = 'secretarias'; // Mantener activa la pestaña de secretarias
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1>Historial de Secretarias</h1>
        <p class="subtitulo-encabezado">Registro de las últimas acciones realizadas por el personal de secretaría</p>
    </div>
    <a href="verSecretarias.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver a Secretarias</a>
</div>

<div class="panel">
    <?php if (empty($historial)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-history"></i></div>
        <p class="panel-vacio-titulo">Historial vacío</p>
        <p class="panel-vacio-desc">Aún no hay acciones registradas en el sistema.</p>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaHistorial">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Secretaria</th>
                    <th>Acción</th>
                    <th>Módulo / Entidad</th>
                    <th>Detalles Adicionales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $entrada): ?>
                <tr>
                    <td style="white-space:nowrap;"><?= date('d/m/Y H:i:s', strtotime($entrada['fecha'])) ?></td>
                    <td><b><?= Security::escapeHtml($entrada['nombreSecretaria'] ?? 'Secretaria #' . $entrada['idSecretaria']) ?></b></td>
                    <td>
                        <span class="texto-estado azul"><?= Security::escapeHtml($entrada['accion']) ?></span>
                    </td>
                    <td><?= Security::escapeHtml($entrada['entidad']) ?></td>
                    <td><small class="texto-suave"><?= Security::escapeHtml($entrada['detalles']) ?></small></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= Paginator::render($pagina, $totalHistorial, $porPagina, function ($p) {
        return '?pagina=' . $p;
    }) ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
