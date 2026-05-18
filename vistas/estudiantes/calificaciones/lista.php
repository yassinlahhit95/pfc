<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";

$id = $_SESSION['idEstudiante'];

$resumen = obtenerResultadosFinalesEstudiante($id); 

$tituloDelPagina = "AULAPRO | MIS CALIFICACIONES";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MIS CALIFICACIONES</h1>
    <div class="resumen-global">
        <div class="item-resumen">
            <span class="etiqueta">PROMEDIO GLOBAL:</span>
            <span class="valor <?= is_numeric($resumen['promedio_global']) && $resumen['promedio_global'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                <?= $resumen['promedio_global'] ?>
            </span>
        </div>
        <div class="item-resumen">
            <span class="etiqueta">ESTADO GLOBAL:</span>
            <span class="valor badge <?= $resumen['estado_global'] == 'APROBADO' ? 'badge-exito' : ($resumen['estado_global'] == 'SUSPENSO' ? 'badge-error' : 'badge-alerta') ?>">
                <?= $resumen['estado_global'] ?>
            </span>
        </div>
    </div>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
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
                            <td><strong><?= $detalle['nombreModulo'] ?></strong></td>
                            <td><?= $detalle['media_notas'] ?></td>
                            <td><?= $detalle['media_retos'] ?></td>
                            <td class="texto-negrita"><?= $detalle['nota_final'] ?></td>
                            <td>
                                <span class="badge <?= $isAprobado ? 'badge-exito' : ($detalle['estado'] == 'Suspenso' ? 'badge-error' : 'badge-alerta') ?>">
                                    <?= mb_strtoupper($detalle['estado'], 'UTF-8') ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">
                            No hay calificaciones registradas o módulos asignados.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.resumen-global {
    display: flex;
    gap: 30px;
    margin-top: 15px;
    background: #f8f9fa;
    padding: 15px 25px;
    border-radius: 10px;
    border-left: 5px solid #3498db;
}
.item-resumen {
    display: flex;
    flex-direction: column;
}
.item-resumen .etiqueta {
    font-size: 0.75rem;
    color: #7f8c8d;
    font-weight: 700;
    margin-bottom: 2px;
}
.item-resumen .valor {
    font-size: 1.4rem;
    font-weight: 900;
}
.badge {
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 700;
}
.badge-exito { background: #d4edda; color: #155724; }
.badge-error { background: #f8d7da; color: #721c24; }
.badge-alerta { background: #fff3cd; color: #856404; }
</style>

<?php include '../comunes/footer.php'; ?>





