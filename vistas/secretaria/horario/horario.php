<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$ciclos  = listarTodosLosCiclos();
$niveles = listarNiveles();

$idNivelFiltro = isset($_GET['nivel']) ? (int)$_GET['nivel'] : 0;
$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($ciclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
    : $ciclos;

$idCicloHorario = isset($_GET['ciclo']) ? (int)$_GET['ciclo'] : (int)($ciclosFiltrados[0]['idCiclo'] ?? 0);

$horarioCeldas = $idCicloHorario ? listarHorarioPorCiclo($idCicloHorario) : [];
$franjasActuales = $idCicloHorario ? obtenerFranjasHorario($idCicloHorario) : [];
$puedeEditar = false;

$titulo_pagina = "AULAPRO | HORARIO";
$seccion = 'horario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CUADRO HORARIO</h1>
    <form method="GET" class="horario-selector-form">
        <label for="nivel">Nivel:</label>
        <select name="nivel" id="nivel" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php foreach ($niveles as $n): ?>
            <option value="<?= Security::escapeHtml($n['idNivel']) ?>"
                <?= ((int)$n['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                <?= Security::escapeHtml($n['nombreNivel']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <label for="ciclo">Ciclo:</label>
        <select name="ciclo" id="ciclo" onchange="this.form.submit()">
            <?php foreach ($ciclosFiltrados as $c): ?>
            <option value="<?= Security::escapeHtml($c['idCiclo']) ?>"
                <?= ($c['idCiclo'] == $idCicloHorario) ? 'selected' : '' ?>>
                <?= Security::escapeHtml($c['nombreCiclo']) ?> (<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php if ($idCicloHorario): ?>
    <form method="POST" action="../../../controladores/admin/informes/generarHorario.php" target="_blank" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idCiclo" value="<?= (int)$idCicloHorario ?>">
        <button type="submit" class="boton-secundario">
            <i class="fas fa-print"></i> Imprimir PDF
        </button>
    </form>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="../../../public/css/horario-admin.css?v=<?= @filemtime(__DIR__."/../../../public/css/horario-admin.css") ?>">

<?php if (empty($ciclosFiltrados)): ?>
<div class="panel"><p class="vacio">No hay ciclos para el nivel seleccionado.</p></div>
<?php else: ?>
<div class="horario-workspace" id="horarioApp">
    <div class="horario-contenido" style="width:100%;">
        <?php include __DIR__ . "/../../../include/horario-tabla.php"; ?>
    </div>
</div>
<?php endif; ?>

<?php include '../comunes/footer.php'; ?>
