<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_eventos');
require_once __DIR__ . "/../../../modelos/eventos.php";

$eventos = listarEventosProximos();

$titulo_pagina = 'AulaPro Familias — Eventos del Centro';
$seccion       = 'eventos';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
  <h1>Próximos Eventos y Fechas Clave</h1>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($eventos)) { ?>
                    <tr><td colspan="5" class="vacio">No hay eventos programados próximamente.</td></tr>
                <?php } else { ?>
                    <?php foreach ($eventos as $evento) { ?>
                    <tr>
                        <td class="texto-negrita"><?= Security::escapeHtml(date('d/m/Y', strtotime($evento['fechaEvento']))) ?></td>
                        <td><?= Security::escapeHtml(date('H:i', strtotime($evento['horaEvento']))) ?>h</td>
                        <td><b><?= Security::escapeHtml(strtoupper($evento['tituloEvento'])) ?></b></td>
                        <td><p class="texto-pequeno"><?= Security::escapeHtml($evento['descripcionEvento']) ?></p></td>
                        <td><?= Security::escapeHtml($evento['ubicacionEvento']) ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
