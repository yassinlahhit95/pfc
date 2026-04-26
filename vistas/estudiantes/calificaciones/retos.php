<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

$tituloDelPagina = "Mis Notas de Retos - Portal Estudiantes";
$seccionActual = 'notas_retos';
include_once "../comunes/nav.php";

require_once "../../../modelos/retos.php";

$id_estudiante = $_SESSION['idEstudiante'];
$mis_notas_retos = listarCalificacionesRetoPorEstudiante($id_estudiante);
?>

<div class="encabezado-pagina">
    <h1>Mis Calificaciones en Retos</h1>
    <p class="subtitulo">Consulta tus notas obtenidas en los retos y proyectos</p>
</div>

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
                <?php if (empty($mis_notas_retos)) { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">Aún no tienes calificaciones registradas en retos.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($mis_notas_retos as $nota) { ?>
                    <tr>
                        <td><strong><?php echo $nota['nombreReto']; ?></strong></td>
                        <td><?php echo $nota['fechaInicio']; ?></td>
                        <td><?php echo $nota['fechaFin']; ?></td>
                        <td class="texto-negrita" style="font-size: 1.1em; color: var(--color-primario);">
                            <?php echo $nota['nota']; ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="margen-arriba tarjeta-gris-suave">
    <p><i class="fas fa-info-circle"></i> Estas notas son finales y contribuyen al 25% de la calificación global del módulo asociado.</p>
</div>

<?php include '../comunes/footer.php'; ?>

