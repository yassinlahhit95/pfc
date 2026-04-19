<?php
session_start();
require_once "../../modelos/conectar.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/ciclos.php";

$idDelEstudiante = $_GET['idEstudiante'] ?? 0;
$datosEstudianteBD = obtenerEstudiantePorId($idDelEstudiante);

if (!$datosEstudianteBD) {
    header("Location: verEstudiantes.php");
    exit;
}

$listaTodosLosCiclos = listarTodosLosCiclos();

// Datos y errores
$datos = $_SESSION['datos_estudiante'] ?? $datosEstudianteBD;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['datos_estudiante'], $_SESSION['errores']);

// Variables simples
$nombre = $datos['nombreEstudiante'];
$email = $datos['emailEstudiante'];
$idCicloElegido = $datos['idCiclo'];
$dni = $datos['dniEstudiante'];
$telefono = $datos['telefonoEstudiante'];
$fNacimiento = $datos['fechaNacimientoEstudiante'];
$idEstadoElegido = $datos['idEstado'];
$direccion = $datos['direccionEstudiante'];
$ciudad = $datos['ciudadEstudiante'];
$cp = $datos['codigoPostalEstudiante'];
$observaciones = $datos['observacionesEstudiante'];
$fAlta = $datos['fechaAltaEstudiante'];

$titulo_pagina = "Modificar Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Estudiante: <?php echo $nombre; ?></h1>
    <a href="vistas/estudiantes/verEstudiantes.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?php echo $idDelEstudiante; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" value="<?php echo $nombre; ?>">
                <?php if (isset($errores['nombre'])) { echo "<p class='error-campo'>".$errores['nombre']."</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailEstudiante" value="<?php echo $email; ?>">
                <?php if (isset($errores['email'])) { echo "<p class='error-campo'>".$errores['email']."</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo Formativo *</label>
                <select name="idCiclo">
                    <?php foreach ($listaTodosLosCiclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if ($ciclo['idCiclo'] == $idCicloElegido) { echo 'selected'; } ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniEstudiante" value="<?php echo $dni; ?>">
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoEstudiante" value="<?php echo $telefono; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" value="<?php echo $fNacimiento; ?>">
            </div>

            <div class="campo-formulario">
                <label>Estado</label>
                <select name="idEstado">
                    <option value="1" <?php if ($idEstadoElegido == 1) echo 'selected'; ?>>Activo</option>
                    <option value="2" <?php if ($idEstadoElegido == 2) echo 'selected'; ?>>Inactivo</option>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección</label>
                <input type="text" name="direccionEstudiante" value="<?php echo $direccion; ?>">
            </div>
            
            <div class="campo-formulario">
                <label>Ciudad</label>
                <input type="text" name="ciudadEstudiante" value="<?php echo $ciudad; ?>">
            </div>

            <div class="campo-formulario">
                <label>Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" value="<?php echo $cp; ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones</label>
                <textarea name="observacionesEstudiante"><?php echo $observaciones; ?></textarea>
            </div>
            
            <input type="hidden" name="fechaAltaEstudiante" value="<?php echo $fAlta; ?>">
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarEstudiante" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
