<?php
session_start();

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

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
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
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
                        <td><b><?= $nota['nombreReto'] ?></b></td>
                        <td><?= $nota['fechaInicio'] ?></td>
                        <td><?= $nota['fechaFin'] ?></td>
                        <td class="texto-negrita color-primario" style="font-size: 1.1em;">
                            <?= $nota['nota'] ?>
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

