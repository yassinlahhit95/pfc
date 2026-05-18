<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$idModulo = $_GET['idModulo'] ?? 0;
$modulo = obtenerModuloPorId(intval($idModulo));

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$profesores_asignados = listarProfesoresDeModulo(intval($idModulo));
$idProfesorActual = !empty($profesores_asignados) ? $profesores_asignados[0] : 0;

$todos_los_profesores = listarProfesores();

$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);

$titulo_pagina = "AULAPRO | ASIGNAR PROFESOR A MÓDULO";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>ASIGNAR PROFESOR AL MÓDULO: <?= $modulo['nombreModulo'] ?></h1>
    <a href="verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Seleccione el profesor que impartirá este módulo</h3>
    </div>

    <form action="../../../controladores/admin/modulos/actualizarProfesores.php" method="POST" class="formulario">
        <input type="hidden" name="idModulo" value="<?= $idModulo ?>">

        <div class="campo">
            <label>Profesor Asignado:</label>
            <select name="idProfesor">
                <option value="">-- Sin Profesor Asignado --</option>
                <?php foreach ($todos_los_profesores as $prof) { ?>
                    <option value="<?= $prof['idProfesor'] ?>" <?= ($prof['idProfesor'] == $idProfesorActual ? 'selected' : '') ?>>    
                        <?= $prof['nombreProfesor'] ?>
                    </option>
                <?php } ?>
            </select>
            <small class="atenuado">Un módulo solo puede tener un profesor responsable.</small>
        </div>

        <div class="acciones">
            <button type="submit" name="actualizarProfesores" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
