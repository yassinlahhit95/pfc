<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/eventos.php";
$eventos = listarEventosProximos();

$tituloDelPagina = "AULAPRO | CALENDARIO DE EVENTOS";
$seccionActual = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>PROXIMOS EVENTOS Y FECHAS CLAVE</h1>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

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
                    <tr><td colspan="5" class="vacio">No hay eventos programados proximamente.</td></tr>
                <?php } else { ?>
                    <?php foreach ($eventos as $evento) { ?>
                    <tr>
                        <td class="texto-negrita"><?= Security::escapeHtml(date('d/m/Y', strtotime($evento['fechaEvento']))) ?></td>
                        <td><?= Security::escapeHtml(date('H:i', strtotime($evento['horaEvento']))) ?>h</td>
                        <td><b><?= Security::escapeHtml(strtoupper($evento['tituloEvento'])) ?></b></td>
                        <td><p class="texto-pequeno"><?= Security::escapeHtml($evento['descripcionEvento'] ) ?></p></td>
                        <td><?= Security::escapeHtml($evento['ubicacionEvento'] ) ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>



