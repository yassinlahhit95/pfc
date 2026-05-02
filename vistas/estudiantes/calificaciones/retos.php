<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

$tituloDelPagina = "Mis Notas de Retos - Portal Estudiantes";
$seccionActual = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/retos.php";

$id_estudiante = $_SESSION['idEstudiante'];
$mis_notas_retos = listarCalificacionesRetoPorEstudiante($id_estudiante);
?>

<div class="encabezado-pagina">
    <h1>Mis Calificaciones en Retos</h1>
</div>

<?php if ($error) : ?>
    <div class="alerta-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito) : ?>
    <div class="alerta-exito"><?= $exito ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
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
                <?php if (empty($mis_notas_retos)) : ?>
                    <tr>
                        <td colspan="4" class="sin-datos">Aún no tienes calificaciones registradas en retos.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($mis_notas_retos as $nota) : ?>
                    <tr>
                        <td><strong><?= $nota['nombreReto'] ?></strong></td>
                        <td><?= $nota['fechaInicio'] ?></td>
                        <td><?= $nota['fechaFin'] ?></td>
                        <td class="texto-negrita color-primario font-size-11">
                            <?= $nota['nota'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="margen-arriba tarjeta-gris-suave">
    <p class="subtitulo">Consulta tus notas obtenidas en los retos y proyectos</p>
    <p>Estas notas son finales y contribuyen al 25% de la calificaciÃ³n global del mÃ³dulo asociado.</p>
</div>

<?php include '../comunes/footer.php'; ?>

