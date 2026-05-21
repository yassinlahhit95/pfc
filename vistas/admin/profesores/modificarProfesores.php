<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id_profesor = $_GET['idProfesor'] ?? '';
$profesor = obtenerProfesorPorId($id_profesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

$listaCiclos = listarTodosLosCiclos();
$todosLosModulos = listarModulos();

$datos_sesion = $_SESSION['datos_profesor'] ?? null;
if ($datos_sesion) {
    $profesor = $datos_sesion + $profesor;
    $ciclos_marcados = $datos_sesion['ciclos'] ?? [];
    $modulos_marcados = $datos_sesion['modulos'] ?? [];
} else {
    $ciclos_marcados = [];
    $ciclosBD = listarCiclosTutorizadosProfesor($id_profesor);
    foreach ($ciclosBD as $cicloItem) {
        $ciclos_marcados[] = $cicloItem['idCiclo'];
    }
    $modulos_marcados = listarIdsModulosDeProfesor($id_profesor);
}

$titulo_pagina = "AULAPRO | MODIFICAR PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MODIFICAR PROFESOR: <?= $profesor['nombreProfesor'] ?></h1>
    </div>
    <a href="verProfesores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/profesores/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?= $id_profesor ?>">
        
        <div class="formulario">
            <div class="campo">
                <label>Nombre Completo</label>
                <input type="text" name="nombreProfesor" value="<?= $profesor['nombreProfesor'] ?>">
                
            </div>

            <div class="campo">
                <label>Email</label>
                <input type="email" name="emailProfesor" value="<?= $profesor['emailProfesor'] ?>">
                
            </div>

            <div class="campo">
                <label>DNI</label>
                <input type="text" name="dniProfesor" value="<?= $profesor['dniProfesor'] ?>">
                
            </div>

            <div class="campo">
                <label>Teléfono</label>
                <input type="text" name="telefonoProfesor" value="<?= $profesor['telefonoProfesor'] ?>">
                
            </div>

            <div class="campo ancho-total">
                <label>Dirección</label>
                <input type="text" name="direccionProfesor" value="<?= $profesor['direccionProfesor'] ?>">
            </div>

            <div class="campo">
                <label>Ciudad</label>
                <input type="text" name="ciudadProfesor" value="<?= $profesor['ciudadProfesor'] ?>">
            </div>

            <div class="campo">
                <label>Código Postal</label>
                <input type="text" name="codigoPostalProfesor" value="<?= $profesor['codigoPostalProfesor'] ?>">
            </div>

            <div class="campo">
                <label>Fecha de Nacimiento</label>
                <input type="date" name="fechaNacimientoProfesor" value="<?= $profesor['fechaNacimientoProfesor'] ?>">
            </div>

            <div class="campo">
                <label>Fecha de Alta (en centro)</label>
                <input type="date" name="fechaAltaProfesor" value="<?= $profesor['fechaAltaProfesor'] ?>">
            </div>

            <div class="campo ancho-total">
                <label>Observaciones / Curriculum Vitae (Resumen)</label>
                <textarea name="observacionesProfesor" rows="3"><?= $profesor['observacionesProfesor'] ?></textarea>
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 style="margin-bottom: 15px;"><i class="fas fa-layer-group"></i> 1. Seleccionar Ciclos</h4>
                <div class="checks scroll-v200">
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <label class="check-item">
                            <input type="checkbox" name="ciclos[]" value="<?= $ciclo['idCiclo'] ?>" class="check-ciclo"
                                <?php if (in_array($ciclo['idCiclo'], $ciclos_marcados)) { echo 'checked'; } ?>>
                            <span><?= $ciclo['nombreCiclo'] ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 style="margin-bottom: 15px;"><i class="fas fa-book"></i> 2. Seleccionar Módulos</h4>
                <div id="contenedor-modulos-dinamico" class="checks scroll-v400 bg-gris-suave">
                    <p id="msg-seleccionar-ciclo" class="texto-suave" style="text-align: center; padding: 20px;">
                        Seleccione primero uno o varios ciclos para ver sus módulos disponibles.
                    </p>

                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <div class="grupo-modulos oculto" style="margin-bottom: 15px;" id="grupo-ciclo-<?= $ciclo['idCiclo'] ?>">
                            <p class="texto-negrita color-primario" style="margin-bottom: 5px;">
                                <?= $ciclo['nombreCiclo'] ?>
                            </p>
                            
                            <?php foreach ($todosLosModulos as $modulo) { ?>
                                <?php if ($modulo['idCiclo'] == $ciclo['idCiclo']) { ?>
                                    <label class="check-item" style="padding-left: 10px;">
                                        <input type="checkbox" name="modulos[]" value="<?= $modulo['idModulo'] ?>"
                                            <?php if (in_array($modulo['idModulo'], $modulos_marcados)) { echo 'checked'; } ?>>
                                        <span><?= $modulo['nombreModulo'] ?></span>
                                    </label>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="acciones" style="margin-top: 25px;">
            <input type="submit" name="actualizarProfesor" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script src="../../../public/js/profesores-form.js"></script>

<?php include '../comunes/footer.php'; ?>
