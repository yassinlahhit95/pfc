<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (!isset($_GET['idProfesor'])) {
    header("Location: verProfesores.php");
    exit;
}

$idProfesor = $_GET['idProfesor'];
$profesor = obtenerProfesorPorId($idProfesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

$modulos_del_profesor = listarIdsModulosDeProfesor($idProfesor);
$todos_los_modulos = listarModulos();
$lista_ciclos = listarTodosLosCiclos();

$titulo_pagina = "AULAPRO | ASIGNAR MÓDULOS A PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>ASIGNAR MÓDULOS: <?= $profesor['nombreProfesor'] ?></h1>
    </div>
    <a href="verProfesores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Seleccione los módulos que impartirá este profesor</h3>
    </div>

    <form action="../../../controladores/admin/profesores/actualizarModulos.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idProfesor" value="<?= $idProfesor ?>">
        
        <?php foreach ($lista_ciclos as $ciclo) { ?>
            <div class="seccion-asignacion" style="margin-bottom: 30px;">
                <h4 class="borde-abajo-primario color-primario" style="margin-bottom: 15px;">
                    <i class="fas fa-layer-group"></i> <?= $ciclo['nombreCiclo'] ?>
                </h4>
                
                <div class="cuadricula-asignacion">
                    <?php foreach ($todos_los_modulos as $modulo) { ?>
                        <?php if ($modulo['idCiclo'] == $ciclo['idCiclo']) { ?>
                            
                            <label class="elemento-asignacion">
                                <input type="checkbox" name="modulos[]" value="<?= $modulo['idModulo'] ?>" 
                                    class="checkbox-grande"
                                    <?php if (in_array($modulo['idModulo'], $modulos_del_profesor)) { echo "checked"; } ?>>
                                <span><?= $modulo['nombreModulo'] ?></span>
                            </label>

                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div style="margin-top: 25px;">
            <input type="submit" name="actualizarModulos" class="boton-primario" value="GUARDAR ASIGNACIONES">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
