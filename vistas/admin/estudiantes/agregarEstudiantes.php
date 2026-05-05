<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

$titulo_pagina = "Nuevo Estudiante - Admin";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

$todos_los_ciclos = listarTodosLosCiclos();

$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_estudiante'] ?? [];

if (!is_array($lista_de_errores)) {
    $lista_de_errores = [];
}
if (!is_array($datos)) {
    $datos = [];
}

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['errores'], $_SESSION['datos_estudiante'], $_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Nuevo Estudiante</h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario">VOLVER</a>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/estudiantes/insertar.php" method="POST">
        <div class="form-estandar">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" value="<?php if(isset($datos['nombreEstudiante'])) { echo $datos['nombreEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['nombreEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailEstudiante" value="<?php if(isset($datos['emailEstudiante'])) { echo $datos['emailEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['emailEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['emailEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniEstudiante" value="<?php if(isset($datos['dniEstudiante'])) { echo $datos['dniEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['dniEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['dniEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoEstudiante" value="<?php if(isset($datos['telefonoEstudiante'])) { echo $datos['telefonoEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['telefonoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['telefonoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Nacimiento *</label>
                <input type="date" name="fechaNacimientoEstudiante" value="<?php if(isset($datos['fechaNacimientoEstudiante'])) { echo $datos['fechaNacimientoEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['fechaNacimientoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['fechaNacimientoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionEstudiante" value="<?php if(isset($datos['direccionEstudiante'])) { echo $datos['direccionEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['direccionEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['direccionEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadEstudiante" value="<?php if(isset($datos['ciudadEstudiante'])) { echo $datos['ciudadEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['ciudadEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['ciudadEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalEstudiante" value="<?php if(isset($datos['codigoPostalEstudiante'])) { echo $datos['codigoPostalEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['codigoPostalEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['codigoPostalEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo *</label>
                <select name="idCiclo">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?php if(isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) { echo "selected"; } ?>>
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['idCiclo'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarEstudiante" class="boton-primario">REGISTRAR ESTUDIANTE</button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>



