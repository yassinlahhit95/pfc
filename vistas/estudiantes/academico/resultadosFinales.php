<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

$idEstudiante = $_SESSION['idEstudiante'];

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";

$resumenFinal = obtenerResultadosFinalesEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | MIS RESULTADOS FINALES";
$seccionActual = 'resultados_finales';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MIS RESULTADOS FINALES</h1>
    <p class="subtitulo">Ciclo: <?= $resumenFinal['nombreCiclo'] ?></p>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Media Notas (75%)</th>
                    <th>Media Retos (25%)</th>
                    <th>Nota Final</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resumenFinal['detalles_modulos'])) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay módulos registrados en su ciclo.</td></tr>
                <?php } else { ?>
                    <?php foreach ($resumenFinal['detalles_modulos'] as $fila) { 
                        $clase = "texto-rojo";
                        if ($fila['estado'] == "Aprobado") { $clase = "texto-verde"; }
                        if ($fila['estado'] == "Pendiente") { $clase = "texto-gris"; }
                    ?>
                    <tr>
                        <td class="texto-negrita"><?= $fila['nombreModulo'] ?></td>
                        <td><?= $fila['media_notas'] ?></td>
                        <td><?= $fila['media_retos'] ?></td>
                        <td class="texto-negrita"><?= $fila['nota_final'] ?></td>
                        <td class="<?= $clase ?> texto-negrita"><?= $fila['estado'] ?></td>
                    </tr>
                    <?php } ?>
                    <!-- TFG -->
                    <tr>
                        <td class="texto-negrita color-primario">TFG — TRABAJO FIN DE GRADO</td>
                        <td colspan="2" class="texto-atenuado">Calificación Directa</td>
                        <td class="texto-negrita color-primario">
                            <?= $resumenFinal['nota_tfg'] !== null ? $resumenFinal['nota_tfg'] : '—' ?>
                        </td>
                        <td class="texto-negrita">
                            <?php 
                            if ($resumenFinal['nota_tfg'] === null) echo '<span class="texto-gris">Pendiente</span>';
                            elseif ($resumenFinal['nota_tfg'] >= 5) echo '<span class="texto-verde">Aprobado</span>';
                            else echo '<span class="texto-rojo">Suspenso</span>';
                            ?>
                        </td>
                    </tr>
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
            <h2 class="color-primario"><?= $resumenFinal['promedio_global'] ?></h2>
        </div>
        <div class="text-right">
            <p class="texto-atenuado">Estado Académico:</p>
            <span class="estado-bolita <?= ($resumenFinal['estado_global'] == 'APROBADO' ? 'activo-verde' : 'inactivo-rojo') ?>">
                <?= $resumenFinal['estado_global'] ?>
            </span>
        </div>
    </div>
</div>

<div class="margen-arriba tarjeta-gris-suave">
    <p><strong>Nota:</strong> El cálculo se basa en el 75% de las notas de evaluación y el 25% de la media de los retos del módulo.</p>
    <p><strong>Estados:</strong> <span class="texto-verde">Aprobado (>= 5.0)</span>, <span class="texto-rojo">Suspenso (< 5.0)</span>, <span class="texto-gris">Pendiente (Sin notas)</span>.</p>
</div>

<?php include '../comunes/footer.php'; ?>




