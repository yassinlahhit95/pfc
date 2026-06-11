<?php
require_once __DIR__ . "/../../../include/Security.php";

// Merged into lista.php — redirect permanently
header("Location: lista.php", true, 301);
exit;

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$mis_notas_retos = listarCalificacionesRetoPorEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | MIS NOTAS DE RETOS";
$seccionActual = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS CALIFICACIONES EN RETOS</h1>
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
                    <th>Nombre del Reto</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Mi Nota</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mis_notas_retos)) { ?>
                    <tr>
                        <td colspan="4" class="vacio">Aun no tienes calificaciones registradas en retos.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($mis_notas_retos as $nota) { ?>
                    <tr>
                        <td><b><?= Security::escapeHtml($nota['nombreReto'] ) ?></b></td>
                        <td><?= Security::escapeHtml($nota['fechaInicio'] ) ?></td>
                        <td><?= Security::escapeHtml($nota['fechaFin'] ) ?></td>
                        <td class="texto-negrita color-primario" style="font-size: 1.1em;">
                            <?= Security::escapeHtml($nota['nota'] ) ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="margen-arriba tarjeta-gris-suave">
    <p class="subtitulo">Consulta tus notas obtenidas en los retos y proyectos</p>
    <p>Estas notas son finales y contribuyen al 25% de la calificación global del módulo asociado.</p>
</div>

<?php include '../comunes/footer.php'; ?>



