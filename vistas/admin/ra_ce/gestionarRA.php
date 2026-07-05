<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ra_ce.php";

FeatureGuard::requirePage('feature_ra_ce');

$idModulo = (int)($_GET['idModulo'] ?? 0);
$modulo = obtenerModuloPorId($idModulo);

if (!$modulo) {
    header("Location: ../modulos/verModulos.php");
    exit;
}

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$resultados = listarRAPorModulo($idModulo);

$titulo_pagina = "AULAPRO | GESTIONAR RA Y CE";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>GESTIONAR R.A. Y C.E.</h1>
        <p class="texto-suave">Módulo: <b><?= Security::escapeHtml($modulo['nombreModulo']) ?></b></p>
    </div>
    <div class="acciones-pagina">
        <a href="../modulos/verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
        <a href="agregarRA.php?idModulo=<?= $idModulo ?>" class="boton-primario"><i class="fas fa-plus"></i> NUEVO R.A.</a>
    </div>
</div>

<div class="panel">
    <?php if (empty($resultados)): ?>
        <div class="vacio">No se han definido Resultados de Aprendizaje para este módulo.</div>
    <?php else: ?>
        <?php foreach ($resultados as $ra): ?>
            <div style="background:var(--surface-1); border:1px solid var(--border-2); border-radius:8px; margin-bottom:20px; overflow:hidden;">
                <div style="background:var(--surface-2); padding:15px 20px; border-bottom:1px solid var(--border-2); display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <b style="font-size:16px; color:var(--text-main);">RA <?= Security::escapeHtml($ra['codigo']) ?></b> 
                        <span class="texto-suave" style="margin-left:10px;">(<?= $ra['porcentaje'] ?>% de la nota final)</span>
                        <div class="texto-suave" style="font-size:13px; margin-top:4px;"><?= Security::escapeHtml($ra['descripcion']) ?></div>
                    </div>
                    <div>
                        <a href="agregarCE.php?idRA=<?= $ra['idRA'] ?>&idModulo=<?= $idModulo ?>" class="boton-secundario" style="padding:6px 12px; font-size:12px;"><i class="fas fa-plus"></i> Añadir CE</a>
                    </div>
                </div>
                
                <?php $criterios = listarCEPorRA($ra['idRA']); ?>
                <div style="padding:15px 20px;">
                    <?php if (empty($criterios)): ?>
                        <div class="texto-suave texto-pequeno">No hay Criterios de Evaluación para este RA.</div>
                    <?php else: ?>
                        <table style="width:100%; border-collapse:collapse;">
                            <?php foreach ($criterios as $ce): ?>
                                <tr style="border-bottom:1px solid var(--border-1);">
                                    <td style="padding:8px 0; width:50px; font-weight:600; color:var(--text-main);">CE <?= Security::escapeHtml($ce['codigo']) ?></td>
                                    <td style="padding:8px 0; color:var(--text-muted); font-size:13px;"><?= Security::escapeHtml($ce['descripcion']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
