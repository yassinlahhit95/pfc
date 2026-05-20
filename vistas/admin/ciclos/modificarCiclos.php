<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$id_ciclo = $_GET['idCiclo'] ?? '';
$ciclo = obtenerCicloPorId($id_ciclo);

if (!$ciclo) {
    header("Location: verCiclos.php");
    exit;
}

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();

// Obtenemos los profesores que ya estan en este ciclo
$profesores_marcados = listarProfesoresDeUnCiclo($id_ciclo);

// Si hay datos en la sesion (por un error), los usamos. Si no, usamos los de la BD.
$datos_sesion = $_SESSION['datos_ciclos'] ?? null;
if ($datos_sesion) {
    $ciclo = array_merge($ciclo, $datos_sesion);
    $profesores_marcados = $datos_sesion['profesores'] ?? [];
}

$titulo_pagina = "AULAPRO | MODIFICAR CICLO";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MODIFICAR CICLO: <?= $ciclo['nombreCiclo'] ?></h1>
    </div>
    <a href="verCiclos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/ciclos/actualizar.php">
        <input type="hidden" name="idCiclo" value="<?= $id_ciclo ?>">
        
        <div class="formulario">
            <div class="campo">
                <label for="nombreCiclo">Nombre del Ciclo</label>
                <input type="text" id="nombreCiclo" name="nombreCiclo" value="<?= $ciclo['nombreCiclo'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="abreviaturaCiclo">Abreviatura</label>
                <input type="text" id="abreviaturaCiclo" name="abreviaturaCiclo" maxlength="10" value="<?= $ciclo['abreviaturaCiclo'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="idNivel">Nivel Formativo</label>
                <select id="idNivel" name="idNivel">
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?= $nivel['idNivel'] ?>" <?php if (($ciclo['idNivel'] ?? '') == $nivel['idNivel']) { ?>selected<?php } ?>>
                            <?= $nivel['nombreNivel'] ?>
                        </option>
                    <?php } ?>
                </select>
                
            </div>

            <div class="campo">
                <label for="precioCiclo">Precio Total del Ciclo (€)</label>
                <input type="number" id="precioCiclo" name="precioCiclo" step="0.01" value="<?= $ciclo['precioCiclo'] ?? '' ?>">
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 style="margin-bottom: 15px;">Asignar Tutores/Profesores</h4>
                <div class="checks scroll-v200">
                    <?php foreach ($listaProfesores as $prof) { ?>
                        <label class="check-item">
                            <input type="checkbox" name="profesores[]" value="<?= $prof['idProfesor'] ?>"
                                <?php if (in_array($prof['idProfesor'], $profesores_marcados)) { ?>checked<?php } ?>>
                            <span><?= $prof['nombreProfesor'] ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarCiclo" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
