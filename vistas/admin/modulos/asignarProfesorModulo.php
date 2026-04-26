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
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; padding: 10px;">
            <?php foreach ($todos_los_profesores as $prof) { 
                $checked = in_array($prof['idProfesor'], $profesores_asignados) ? "checked" : "";
            ?>
                <label style="display: flex; align-items: center; cursor: pointer; padding: 10px; border: 1px solid #eee; border-radius: 8px; transition: background 0.2s;">
                    <input type="checkbox" name="profesores[]" value="<?php echo $prof['idProfesor']; ?>" <?php echo $checked; ?> style="margin-right: 15px; width: 20px; height: 20px;">
                    <div>
                        <div class="texto-negrita"><?php echo $prof['nombreProfesor']; ?></div>
                        <div class="texto-atenuado texto-pequeno"><?php echo $prof['especialidad']; ?></div>
                    </div>
                </label>
            <?php } ?>
        </div>

        <div class="margen-arriba" style="border-top: 1px solid #eee; padding-top: 20px;">
            <button type="submit" name="actualizarProfesores" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <a href="verModulos.php" class="boton-secundario" style="margin-left: 10px;">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
