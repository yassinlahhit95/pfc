<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";

$idEstudiante = $_SESSION['idEstudiante'];

$resumen = obtenerResultadosFinalesEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | MIS CALIFICACIONES";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS CALIFICACIONES</h1>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>DETALLE POR MÓDULO (75% EXÁMENES | 25% RETOS)</h3>
    </div>

    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Media Exámenes (75%)</th>
                    <th>Media Retos (25%)</th>
                    <th>Nota Final</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resumen['detalles_modulos'])) { ?>
                    <?php foreach ($resumen['detalles_modulos'] as $detalle) {
                        $isAprobado = ($detalle['estado'] == 'Aprobado');
                    ?>
                        <tr>
                            <td><b><?= Security::escapeHtml($detalle['nombreModulo'] ) ?></b></td>
                            <td><?= Security::escapeHtml($detalle['media_notas'] ) ?></td>
                            <td><?= Security::escapeHtml($detalle['media_retos'] ) ?></td>
                            <td class="texto-negrita"><?= Security::escapeHtml($detalle['nota_final'] ) ?></td>
                            <td>
                                <span class="badge <?= Security::escapeHtml($isAprobado ? 'badge-exito' : ($detalle['estado'] == 'Suspenso' ? 'badge-error' : 'badge-alerta')) ?>">
                                    <?= Security::escapeHtml(strtoupper($detalle['estado'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="vacio">No hay calificaciones registradas o módulos asignados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="resumen-global" style="border-left: none; margin-top: 20px;">
        <div class="item-resumen">
            <span class="nombre">PROMEDIO GLOBAL:</span>
            <span class="valor <?= Security::escapeHtml(is_numeric($resumen['promedio_global']) && $resumen['promedio_global'] >= 5 ? 'texto-verde' : 'texto-rojo') ?>">
                <?= Security::escapeHtml($resumen['promedio_global'] ) ?>
            </span>
        </div>
        <div class="item-resumen">
            <span class="nombre">ESTADO GLOBAL:</span>
            <span class="badge <?= Security::escapeHtml($resumen['estado_global'] == 'APROBADO' ? 'badge-exito' : ($resumen['estado_global'] == 'SUSPENSO' ? 'badge-error' : 'badge-alerta')) ?>">
                <?= Security::escapeHtml($resumen['estado_global'] ) ?>
            </span>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>


