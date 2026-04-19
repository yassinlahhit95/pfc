<?php
session_start();
$titulo_pagina = "Agregar Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";

require_once "../../modelos/conectar.php";
require_once "../../modelos/ciclos.php";

$listaCiclos = listarTodosLosCiclos();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_estudiante'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_estudiante']);

// Variables para los campos (Forma facil para estudiantes)
$nombre = $datos['nombreEstudiante'] ?? '';
$email = $datos['emailEstudiante'] ?? '';
$dni = $datos['dniEstudiante'] ?? '';
$telefono = $datos['telefonoEstudiante'] ?? '';
$fNacimiento = $datos['fechaNacimientoEstudiante'] ?? '';
$fAlta = $datos['fechaAltaEstudiante'] ?? date('Y-m-d');
$direccion = $datos['direccionEstudiante'] ?? '';
$ciudad = $datos['ciudadEstudiante'] ?? '';
$cp = $datos['codigoPostalEstudiante'] ?? '';
$idCicloElegido = $datos['idCiclo'] ?? '';
?>

<div class="encabezado-pagina">
    <h1>Nuevo Estudiante</h1>
    <a href="vistas/estudiantes/verEstudiantes.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/estudiantes/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" value="<?php echo $nombre; ?>">
                <?php if (isset($errores['nombreEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['nombreEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailEstudiante" value="<?php echo $email; ?>">
                <?php if (isset($errores['emailEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['emailEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniEstudiante" value="<?php echo $dni; ?>">
                <?php if (isset($errores['dniEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['dniEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoEstudiante" value="<?php echo $telefono; ?>">
                <?php if (isset($errores['telefonoEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['telefonoEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Nacimiento *</label>
                <input type="date" name="fechaNacimientoEstudiante" value="<?php echo $fNacimiento; ?>">
                <?php if (isset($errores['fechaNacimientoEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['fechaNacimientoEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Alta *</label>
                <input type="date" name="fechaAltaEstudiante" value="<?php echo $fAlta; ?>">
                <?php if (isset($errores['fechaAltaEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['fechaAltaEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionEstudiante" value="<?php echo $direccion; ?>">
                <?php if (isset($errores['direccionEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['direccionEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadEstudiante" value="<?php echo $ciudad; ?>">
                <?php if (isset($errores['ciudadEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['ciudadEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalEstudiante" value="<?php echo $cp; ?>">
                <?php if (isset($errores['codigoPostalEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['codigoPostalEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo *</label>
                <select name="idCiclo">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if ($idCicloElegido == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <p class="error-campo"><?php echo $errores['idCiclo']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarEstudiante" class="boton-primario">Registrar Estudiante</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
