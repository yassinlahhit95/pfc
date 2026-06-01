<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

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

<div class="cabecera">
    <h1>MIS RESULTADOS FINALES</h1>
    <p class="subtitulo">Ciclo: <?= Security::escapeHtml($resumenFinal['nombreCiclo'] ) ?></p>
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
                    <th>Módulo</th>
                    <th>Media Notas (75%)</th>
                    <th>Media Retos (25%)</th>
                    <th>Nota Final</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resumenFinal['detalles_modulos'])) { ?>
                    <tr><td colspan="5" class="vacio">No hay módulos registrados en su ciclo.</td></tr>
                <?php } else { ?>
                    <?php foreach ($resumenFinal['detalles_modulos'] as $fila) { ?>
                    <tr>
                        <td class="texto-negrita"><?= Security::escapeHtml($fila['nombreModulo'] ) ?></td>
                        <td><?= Security::escapeHtml($fila['media_notas'] ) ?></td>
                        <td><?= Security::escapeHtml($fila['media_retos'] ) ?></td>
                        <td class="texto-negrita"><?= Security::escapeHtml($fila['nota_final'] ) ?></td>
                        <td>
                            <span class="badge <?= Security::escapeHtml($fila['estado'] == 'Aprobado' ? 'badge-exito' : ($fila['estado'] == 'Suspenso' ? 'badge-error' : 'badge-alerta')) ?>">
                                <?= Security::escapeHtml(strtoupper($fila['estado'])) ?>
                            </span>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="texto-negrita color-primario">TFG : TRABAJO FIN DE GRADO</td>
                        <td colspan="2" class="texto-suave">Calificación Directa</td>
                        <td class="texto-negrita color-primario">
                            <?= Security::escapeHtml($resumenFinal['nota_tfg'] !== null ? $resumenFinal['nota_tfg'] : ' ') ?>
                        </td>
                        <td class="texto-negrita">
                            <?php
                            if ($resumenFinal['nota_tfg'] === null) echo '<span class="badge badge-alerta">PENDIENTE</span>';
                            elseif ($resumenFinal['nota_tfg'] >= 5) echo '<span class="badge badge-exito">APROBADO</span>';
                            else echo '<span class="badge badge-error">SUSPENSO</span>';
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta"><h3>RESUMEN GLOBAL DEL CICLO</h3></div>
    <div class="caja espacio-entre-elementos alinear-centro">
        <div>
            <p class="texto-suave">Promedio General:</p>
            <h2 class="color-primario"><?= Security::escapeHtml($resumenFinal['promedio_global'] ) ?></h2>
        </div>
        <div style="text-align: right;">
            <p class="texto-suave">Estado Académico:</p>
            <span class="badge <?= Security::escapeHtml($resumenFinal['estado_global'] == 'APROBADO' ? 'badge-exito' : ($resumenFinal['estado_global'] == 'SUSPENSO' ? 'badge-error' : 'badge-alerta')) ?>">
                <?= Security::escapeHtml($resumenFinal['estado_global'] ) ?>
            </span>
        </div>
    </div>
</div>

<div class="margen-arriba tarjeta-gris-suave">
    <p><b>Nota:</b> El calculo se basa en el 75% de las notas de evaluacion y el 25% de la media de los retos del modulo.</p>
    <p><b>Estados:</b> <span class="texto-verde">Aprobado (>= 5.0)</span>, <span class="texto-rojo">Suspenso (< 5.0)</span>, <span class="texto-gris">Pendiente (Sin notas)</span>.</p>
</div>

<?php include '../comunes/footer.php'; ?>



