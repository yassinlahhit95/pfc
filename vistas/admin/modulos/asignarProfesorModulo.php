<?php
session_start();
$titulo_pagina = "Asignar Profesores a Módulo - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
require_once "../../../modelos/profesores.php";

if (!isset($_GET['idModulo'])) {
    header("Location: verModulos.php");
    exit;
}

$idModulo = intval($_GET['idModulo']);
$modulo = obtenerModuloPorId($idModulo);

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$profesores_asignados = obtenerProfesoresDeModulo($idModulo);
$todos_los_profesores = listarProfesores();
?>

<div class="encabezado-pagina">
    <h1>Asignar Profesores al Módulo: <?php echo $modulo['nombreModulo']; ?></h1>
    <a href="verModulos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Seleccione los profesores que impartirán este módulo</h3>
    </div>

    <form action="/pfc/controladores/admin/modulos/actualizarProfesores.php" method="POST">
        <input type="hidden" name="idModulo" value="<?php echo $idModulo; ?>">
        
        <div class="cuadricula-asignacion">
            <?php foreach ($todos_los_profesores as $prof) { 
                $checked = in_array($prof['idProfesor'], $profesores_asignados) ? "checked" : "";
            ?>
                <label class="elemento-asignacion">
                    <input type="checkbox" name="profesores[]" value="<?php echo $prof['idProfesor']; ?>" <?php echo $checked; ?> class="checkbox-extra-grande">
                    <div>
                        <div class="texto-negrita"><?php echo $prof['nombreProfesor']; ?></div>
                        <div class="texto-atenuado texto-pequeno"><?php echo $prof['especialidad']; ?></div>
                    </div>
                </label>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarProfesores" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <a href="verModulos.php" class="boton-secundario ml-10">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

