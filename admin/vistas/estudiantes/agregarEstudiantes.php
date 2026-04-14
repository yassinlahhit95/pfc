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

// Recoger datos si hubo un error anterior
$datos = $_SESSION['datos'] ?? [];
$error_nombre = $_SESSION['error_nombre'] ?? "";
$error_email = $_SESSION['error_email'] ?? "";
$error_dni = $_SESSION['error_dni'] ?? "";

// Limpiar la sesión para que no salgan errores la próxima vez
unset($_SESSION['datos'], $_SESSION['error_nombre'], $_SESSION['error_email'], $_SESSION['error_dni']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Estudiante</h1>
    <a href="vistas/estudiantes/verEstudiantes.php" class="boton-gris">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/estudiantesControlador.php" method="POST">
        <input type="hidden" name="accion" value="insertar">

        <div class="formulario-cuadricula">
            <!-- Nombre -->
            <div class="campo-formulario">
                <label>Nombre Completo</label>
                <input type="text" name="nombreEstudiante" value="<?php echo $datos['nombreEstudiante'] ?? ''; ?>">
                <?php if ($error_nombre != "") { ?>
                    <p class="error-campo"><?php echo $error_nombre; ?></p>
                <?php } ?>
            </div>

            <!-- Email -->
            <div class="campo-formulario">
                <label>Email</label>
                <input type="text" name="emailEstudiante" value="<?php echo $datos['emailEstudiante'] ?? ''; ?>">
                <?php if ($error_email != "") { ?>
                    <p class="error-campo"><?php echo $error_email; ?></p>
                <?php } ?>
            </div>

            <!-- DNI -->
            <div class="campo-formulario">
                <label>DNI</label>
                <input type="text" name="dniEstudiante" value="<?php echo $datos['dniEstudiante'] ?? ''; ?>">
                <?php if ($error_dni != "") { ?>
                    <p class="error-campo"><?php echo $error_dni; ?></p>
                <?php } ?>
            </div>

            <!-- Fecha -->
            <div class="campo-formulario">
                <label>Fecha Nacimiento (DD-MM-YYYY)</label>
                <input type="text" name="fechaNacimientoEstudiante" value="<?php echo $datos['fechaNacimientoEstudiante'] ?? ''; ?>">
            </div>

            <!-- Curso -->
            <div class="campo-formulario">
                <label>Curso</label>
                <select name="idCurso">
                    <?php foreach ($listaCursos as $c) { ?>
                        <option value="<?php echo $c['idCurso']; ?>"><?php echo $c['nombreCurso']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" class="boton-azul">Guardar Estudiante</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
