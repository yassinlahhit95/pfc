<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_eventos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/eventos.php";
$eventos = listarEventosProximos();

$titulo_pagina = "Calendario de Eventos";
$seccionActual = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Proximos Eventos y Fechas Clave</h1>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tabla-eventos-est">
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
                    <tr><td colspan="5" class="vacio">No hay eventos programados proximamente.</td></tr>
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

<?php include '../comunes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    iniciarPaginacion('tabla-eventos-est', 15);
});
</script>
