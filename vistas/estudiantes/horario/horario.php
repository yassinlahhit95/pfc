<?php
require_once __DIR__ . "/../../../include/Security.php";

$idEstudiante = $_SESSION['idEstudiante'] ?? '';
if (!$idEstudiante) {
    header("Location: ../../login.php");
    exit;
}
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";

$datosEstudiante = obtenerEstudiantePorId($idEstudiante);
$idCicloHorario  = (int)($datosEstudiante['idCiclo'] ?? 0);
$ciclo           = $idCicloHorario ? obtenerCicloPorId($idCicloHorario) : null;

$horarioCeldas = $idCicloHorario ? listarHorarioPorCiclo($idCicloHorario) : [];
$puedeEditar   = false;

$tituloDelPagina = "AULAPRO | MI HORARIO";
$seccionActual = 'horario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<link rel="stylesheet" href="../../../public/css/horario-admin.css?v=<?= @filemtime(__DIR__.'/../../../public/css/horario-admin.css') ?>">

<div class="cabecera">
    <h1>MI HORARIO</h1>
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
