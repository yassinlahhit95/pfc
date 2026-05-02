<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

$idEstudiante = $_SESSION['idEstudiante'];

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";

// Obtenemos los datos finales procesados desde el Modelo (MVC)
$resumenFinal = obtenerResultadosFinalesEstudiante($idEstudiante);

$tituloDelPagina = "Mis Resultados Finales - Portal Estudiantes";
$seccionActual = 'resultados_finales';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Mis Resultados Finales</h1>
    <p class="subtitulo">Ciclo: <?php echo $resumenFinal['nombreCiclo']; ?></p>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>MÃ³dulo</th>
                    <th>Media Notas (75%)</th>
                    <th>Media Retos (25%)</th>
                    <th>Nota Final</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resumenFinal['detalles_modulos'])) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay mÃ³dulos registrados en su ciclo.</td></tr>
                <?php } else { ?>
                    <?php foreach ($resumenFinal['detalles_modulos'] as $fila) { 
                        $clase = "texto-rojo";
                        if ($fila['estado'] == "Aprobado") { $clase = "texto-verde"; }
                        if ($fila['estado'] == "Pendiente") { $clase = "texto-gris"; }
                    ?>
                    <tr>
                        <td class="texto-negrita"><?php echo $fila['nombreModulo']; ?></td>
                        <td><?php echo $fila['media_notas']; ?></td>
                        <td><?php echo $fila['media_retos']; ?></td>
                        <td class="texto-negrita"><?php echo $fila['nota_final']; ?></td>
                        <td class="<?php echo $clase; ?> texto-negrita"><?php echo $fila['estado']; ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta"><h3>RESUMEN GLOBAL DEL CICLO</h3></div>
    <div class="disposicion-flexible espacio-entre-elementos alinear-centro">
        <div>
            <p class="texto-atenuado">Promedio General:</p>
            <h2 class="color-primario"><?php echo $resumenFinal['promedio_global']; ?></h2>
        </div>
        <div class="text-right">
            <p class="texto-atenuado">Estado AcadÃ©mico:</p>
            <span class="estado-bolita <?php echo ($resumenFinal['estado_global'] == 'APROBADO' ? 'activo-verde' : 'inactivo-rojo'); ?>">
                <?php echo $resumenFinal['estado_global']; ?>
            </span>
        </div>
    </div>
</div>

<div class="margen-arriba tarjeta-gris-suave">
    <p><strong>Nota:</strong> El cÃ¡lculo se basa en el 75% de las notas de evaluaciÃ³n y el 25% de la media de los retos del mÃ³dulo.</p>
    <p><strong>Estados:</strong> <span class="texto-verde">Aprobado (>= 5.0)</span>, <span class="texto-rojo">Suspenso (< 5.0)</span>, <span class="texto-gris">Pendiente (Sin notas)</span>.</p>
</div>

<?php include '../comunes/footer.php'; ?>
