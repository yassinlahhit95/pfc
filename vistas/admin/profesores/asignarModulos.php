<?php
session_start();
$titulo_pagina = "Asignar Módulos a Profesor - Super Admin";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (!isset($_GET['idProfesor'])) {
    header("Location: verProfesores.php");
    exit;
}

$idProfesor = intval($_GET['idProfesor']);
$profesor = obtenerProfesorPorId($idProfesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

$modulos_asignados = obtenerIdsModulosDeProfesor($idProfesor);
$todos_los_modulos = listarModulos(); // Incluye nombre de ciclo por el JOIN
$ciclos = listarTodosLosCiclos();

// Organizar módulos por ciclo para mejor UX
$modulos_por_ciclo = [];
foreach ($todos_los_modulos as $m) {
    $modulos_por_ciclo[$m['nombreCiclo']][] = $m;
}
?>

<div class="encabezado-pagina">
    <h1>Asignar Módulos: <?php echo $profesor['nombreProfesor']; ?></h1>
    <a href="verProfesores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Seleccione los módulos que impartirá este profesor</h3>
    </div>

    <form action="/pfc/controladores/admin/profesores/actualizarModulos.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?php echo $idProfesor; ?>">
        
        <?php foreach ($modulos_por_ciclo as $nombreCiclo => $modulos) { ?>
            <div class="seccion-asignacion margen-abajo">
                <h4 class="borde-abajo-primario color-primario">
                    <i class="fas fa-layer-group"></i> <?php echo $nombreCiclo; ?>
                </h4>
                <div class="cuadricula-asignacion">
                    <?php foreach ($modulos as $mod) { 
                        $checked = in_array($mod['idModulo'], $modulos_asignados) ? "checked" : "";
                    ?>
                        <label class="elemento-asignacion">
                            <input type="checkbox" name="modulos[]" value="<?php echo $mod['idModulo']; ?>" <?php echo $checked; ?> class="checkbox-grande">
                            <span><?php echo $mod['nombreModulo']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div class="margen-arriba">
            <button type="submit" name="actualizarModulos" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Asignaciones
            </button>
            <a href="verProfesores.php" class="boton-secundario ml-10">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

