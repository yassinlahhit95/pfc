<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$idModulo = (int)($_GET['idModulo'] ?? 0);
$modulo = obtenerModuloPorId($idModulo);

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$profesoresAsignados = listarProfesoresDeModulo($idModulo);
$idProfesorActual = !empty($profesoresAsignados) ? $profesoresAsignados[0] : 0;

$todosLosProfesores = listarProfesores();

$titulo_pagina = "Asignar Profesor A Módulo";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>ASIGNAR PROFESOR AL MÓDULO: <?= Security::escapeHtml($modulo['nombreModulo']) ?></h1>
    <a href="verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/modulos/actualizarProfesores.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idModulo" value="<?= $idModulo ?>">

        <div class="campo ancho-total">
            <label for="idProfesor">Profesor Asignado:</label>
            <select name="idProfesor" id="idProfesor">
                <option value="">-- Sin Profesor Asignado --</option>
                <?php foreach ($todosLosProfesores as $prof) { ?>
                    <option value="<?= (int)$prof['idProfesor'] ?>" <?= ($prof['idProfesor'] == $idProfesorActual ? 'selected' : '') ?>>
                        <?= Security::escapeHtml($prof['nombreProfesor']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarProfesores" class="boton-primario" value="Guardar Cambios">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
