<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ra_ce.php";

FeatureGuard::requirePage('feature_ra_ce');

$idModulo = (int)($_GET['idModulo'] ?? 0);
$modulo = obtenerModuloPorId($idModulo);

if (!$modulo) {
    header("Location: lista.php");
    exit;
}

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$resultados = listarRAPorModulo($idModulo);

$tituloDelPagina = "AULAPRO | EVALUAR RA / CE";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>EVALUAR RESULTADOS DE APRENDIZAJE</h1>
        <p class="texto-suave">Módulo: <b><?= Security::escapeHtml($modulo['nombreModulo']) ?></b></p>
    </div>
    <div class="acciones-pagina">
        <a href="lista.php?idModulo=<?= $idModulo ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
    </div>
</div>

<div class="panel">
    <?php if (empty($resultados)): ?>
        <div class="vacio">No se han definido Resultados de Aprendizaje para este módulo en el panel de administración.</div>
    <?php else: ?>
        <div style="padding:20px; text-align:center;">
            <p>La interfaz de calificación de <b>Criterios de Evaluación</b> estará disponible próximamente en la siguiente actualización.</p>
            <p class="texto-suave">Este módulo permitirá evaluar individualmente a cada estudiante según los porcentajes definidos para cada RA en el currículo LOMLOE.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
