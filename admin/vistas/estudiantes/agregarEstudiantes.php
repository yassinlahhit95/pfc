<?php
session_start();
$titulo_pagina = "Nuevo Estudiante";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/cursos.php";

$con = new Conexion();
$conexion = $con->conectar();
$modeloCursos = new curso($conexion);
$listaCursos = $modeloCursos->listarCursosModelo();

// Recoger datos y errores
$datos = $_SESSION['datos_estudiante'] ?? [];
$errores = $_SESSION['errores'] ?? [];

// Limpiar la sesión
unset($_SESSION['datos_estudiante'], $_SESSION['errores']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Estudiante</h1>
    <a href="vistas/estudiantes/verEstudiantes.php" class="boton-gris">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/estudiantesControlador.php" method="POST">
        <input type="hidden" name="accion" value="insertar">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" value="<?php echo htmlspecialchars($datos['nombreEstudiante'] ?? ''); ?>">
                <?php if (isset($errores['nombreEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['nombreEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailEstudiante" value="<?php echo htmlspecialchars($datos['emailEstudiante'] ?? ''); ?>">
                <?php if (isset($errores['emailEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['emailEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniEstudiante" value="<?php echo htmlspecialchars($datos['dniEstudiante'] ?? ''); ?>">
                <?php if (isset($errores['dniEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['dniEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoEstudiante" value="<?php echo htmlspecialchars($datos['telefonoEstudiante'] ?? ''); ?>">
                <?php if (isset($errores['telefonoEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['telefonoEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Nacimiento *</label>
                <input type="date" name="fechaNacimientoEstudiante" value="<?php echo htmlspecialchars($datos['fechaNacimientoEstudiante'] ?? ''); ?>">
                <?php if (isset($errores['fechaNacimientoEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['fechaNacimientoEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Alta *</label>
                <input type="date" name="fechaAltaEstudiante" value="<?php echo htmlspecialchars($datos['fechaAltaEstudiante'] ?? date('Y-m-d')); ?>">
                <?php if (isset($errores['fechaAltaEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['fechaAltaEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionEstudiante" value="<?php echo htmlspecialchars($datos['direccionEstudiante'] ?? ''); ?>">
                <?php if (isset($errores['direccionEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['direccionEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadEstudiante" value="<?php echo htmlspecialchars($datos['ciudadEstudiante'] ?? ''); ?>">
                <?php if (isset($errores['ciudadEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['ciudadEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalEstudiante" value="<?php echo htmlspecialchars($datos['codigoPostalEstudiante'] ?? ''); ?>">
                <?php if (isset($errores['codigoPostalEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['codigoPostalEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Nivel *</label>
                <input type="text" name="nivelEstudiante" value="<?php echo htmlspecialchars($datos['nivelEstudiante'] ?? ''); ?>" placeholder="Ej: 1º Bachillerato">
                <?php if (isset($errores['nivelEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['nivelEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Curso *</label>
                <select name="idCurso">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($listaCursos as $c) { ?>
                        <option value="<?php echo $c['idCurso']; ?>" <?php echo (isset($datos['idCurso']) && $datos['idCurso'] == $c['idCurso']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['nombreCurso']); ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idCurso'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idCurso']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Estado *</label>
                <select name="idEstado">
                    <option value="">-- Seleccionar --</option>
                    <option value="1" <?php echo (isset($datos['idEstado']) && $datos['idEstado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                    <option value="2" <?php echo (isset($datos['idEstado']) && $datos['idEstado'] == 2) ? 'selected' : ''; ?>>Inactivo</option>
                </select>
                <?php if (isset($errores['idEstado'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idEstado']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones</label>
                <textarea name="observacionesEstudiante" rows="3"><?php echo htmlspecialchars($datos['observacionesEstudiante'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarEstudiante" class="boton-azul">Guardar Estudiante</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
