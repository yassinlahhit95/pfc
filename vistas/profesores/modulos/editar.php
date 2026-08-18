<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
if (!$esTutor || !$idCicloTutor) {
    header("Location: lista.php"); exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idModulo = (int)($_GET['idModulo'] ?? 0);
$modulo   = $idModulo ? obtenerModuloPorId($idModulo) : null;

if (!$modulo || !moduloPerteneceACiclo($idModulo, $idCicloTutor)) {
    header("Location: lista.php"); exit;
}

$ciclo = obtenerCicloPorId($idCicloTutor);

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$titulo_pagina = "Editar Módulo";
$seccionActual   = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Editar Módulo</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="/controladores/profesores/modulos/actualizar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idModulo" value="<?= (int)$modulo['idModulo'] ?>">
        <div class="formulario">
            <div class="campo ancho-total">
                <label for="cicloModuloDisabled">Ciclo</label>
                <input type="text" id="cicloModuloDisabled" value="<?= Security::escapeHtml($ciclo['nombreCiclo'] ?? '') ?>" disabled>
            </div>
            <div class="campo ancho-total">
                <label for="nombreModulo">Nombre del Módulo</label>
                <input type="text" id="nombreModulo" name="nombreModulo"
                       value="<?= Security::escapeHtml($modulo['nombreModulo']) ?>" required>
            </div>
            <div class="campo ancho-total">
                <label for="horasMaximas">Horas Máximas</label>
                <input type="number" id="horasMaximas" name="horasMaximas" min="1" max="2000"
                       value="<?= (int)$modulo['horasMaximas'] ?>" required>
            </div>
        </div>
        <div class="acciones">
            <input type="submit" name="actualizarModulo" class="boton-primario" value="GUARDAR CAMBIOS">
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
