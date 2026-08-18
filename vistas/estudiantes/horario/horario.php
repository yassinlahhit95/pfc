<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/AssetMin.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idEstudiante = (int)$_SESSION['idEstudiante'];
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";

$datosEstudiante = obtenerEstudiantePorId($idEstudiante);
$idCicloHorario  = (int)($datosEstudiante['idCiclo'] ?? 0);
$ciclo           = $idCicloHorario ? obtenerCicloPorId($idCicloHorario) : null;

$horarioCeldas = $idCicloHorario ? listarHorarioPorCiclo($idCicloHorario) : [];
$puedeEditar   = false;

$titulo_pagina = "Mi Horario";
$seccionActual = 'horario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../../public/css/features/horario-admin.css') ?>">

<div class="cabecera">
    <h1>Mi Horario</h1>
    <?php if ($ciclo) { ?>
        <span class="texto-dirigido"><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></span>
    <?php } ?>
    <?php if ($idCicloHorario) { ?>
    <form method="POST" action="../../../controladores/estudiantes/informes/generarHorario.php" target="_blank" style="display:inline;margin-left:12px;">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <button type="submit" class="btn-exportar-horario">
            <i class="fas fa-print"></i> Imprimir PDF
        </button>
    </form>
    <?php } ?>
</div>

<?php if (!$idCicloHorario) { ?>
    <div class="panel"><p class="vacio">No tienes un ciclo asignado. Contacta con administración.</p></div>
<?php } else { ?>
<div class="horario-contenido horario-solo-lectura">
    <?php include __DIR__ . "/../../../include/horario-tabla.php"; ?>
</div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
