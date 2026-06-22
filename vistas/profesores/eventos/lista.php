<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_eventos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/eventos.php";
$eventos = listarEventosProximos();

$tituloDelPagina = "AULAPRO | CALENDARIO DE EVENTOS";
$seccionActual = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>PROXIMOS EVENTOS DEL CENTRO</h1>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Evento</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($eventos)) { ?>
                    <tr><td colspan="4" class="vacio">No hay eventos programados.</td></tr>
                <?php } else { ?>
                    <?php foreach ($eventos as $evento) { ?>
                    <tr>
                        <td class="texto-negrita"><?= Security::escapeHtml(date('d/m/Y', strtotime($evento['fechaEvento']))) ?></td>
                        <td><?= Security::escapeHtml(date('H:i', strtotime($evento['horaEvento']))) ?>h</td>
                        <td>
                            <b><?= Security::escapeHtml($evento['tituloEvento'] ) ?></b><br>
                            <span class="texto-suave"><?= Security::escapeHtml($evento['descripcionEvento'] ) ?></span>
                        </td>
                        <td><?= Security::escapeHtml($evento['ubicacionEvento'] ) ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>



