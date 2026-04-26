<?php
session_start();
require_once "../../../modelos/conectar.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/ciclos.php";

$id_del_estudiante = $_GET['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id_del_estudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

if (isset($_SESSION['datos_estudiante'])) {
    $estudiante = $_SESSION['datos_estudiante'];
}

$todos_los_ciclos = listarTodosLosCiclos();

$lista_de_errores = array();
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

unset($_SESSION['datos_estudiante'], $_SESSION['errores']);

$titulo_pagina = "Modificar Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Estudiante: <?php echo $estudiante['nombreEstudiante']; ?></h1>
    <a href="/pfc/vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?php echo $id_del_estudiante; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" value="<?php echo $estudiante['nombreEstudiante']; ?>">
                <?php if (isset($lista_de_errores['nombreEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailEstudiante" value="<?php echo $estudiante['emailEstudiante']; ?>">
                <?php if (isset($lista_de_errores['emailEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['emailEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo Formativo *</label>
                <select name="idCiclo">
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if ($ciclo['idCiclo'] == $estudiante['idCiclo']) { echo "selected"; } ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['idCiclo']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniEstudiante" value="<?php echo $estudiante['dniEstudiante']; ?>">
                <?php if (isset($lista_de_errores['dniEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['dniEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoEstudiante" value="<?php echo $estudiante['telefonoEstudiante']; ?>">
                <?php if (isset($lista_de_errores['telefonoEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['telefonoEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" value="<?php echo $estudiante['fechaNacimientoEstudiante']; ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección</label>
                <input type="text" name="direccionEstudiante" value="<?php echo $estudiante['direccionEstudiante']; ?>">
            </div>
            
            <div class="campo-formulario">
                <label>Ciudad</label>
                <input type="text" name="ciudadEstudiante" value="<?php echo $estudiante['ciudadEstudiante']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" value="<?php echo $estudiante['codigoPostalEstudiante']; ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones</label>
                <textarea name="observacionesEstudiante"><?php echo $estudiante['observacionesEstudiante']; ?></textarea>
            </div>
            
            <input type="hidden" name="fechaAltaEstudiante" value="<?php echo $estudiante['fechaAltaEstudiante']; ?>">
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarEstudiante" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

