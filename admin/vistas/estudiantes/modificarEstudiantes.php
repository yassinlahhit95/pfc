<?php
session_start();
require_once "../../modelos/conexion.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/cursos.php";

$id = $_GET['id'] ?? 0;
$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();

$modeloEstudiante = new estudiante($conexionBD);
$datosEstudianteBD = $modeloEstudiante->obtenerEstudiantePorIdModelo($id);

if (!$datosEstudianteBD) {
    header("Location: verEstudiantes.php");
    exit;
}

$modeloCurso = new curso($conexionBD);
$listaCursos = $modeloCurso->listarCursosModelo();

// --- LÓGICA DE ERRORES Y PERSISTENCIA ---
$errores = $_SESSION['errores'] ?? [];
$datosViejos = $_SESSION['datos_viejos'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_viejos']);

// Si hay datos viejos (porque falló el guardado), usamos esos. 
// Si no, usamos los de la base de datos.
// Convertimos la fecha de BD (YYYY-MM-DD) a DD-MM-YYYY para mostrarla
$fechaMostrar = $datosEstudianteBD['fechaNacimientoEstudiante'];
$partes = explode("-", $fechaMostrar);
$fechaFormateada = $partes[2] . "-" . $partes[1] . "-" . $partes[0];

$titulo_pagina = "Modificar Estudiante - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Estudiante: <?php echo htmlspecialchars($datosEstudianteBD['nombreEstudiante']); ?></h1>
    <a href="vistas/estudiantes/verEstudiantes.php" class="boton-gris">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/estudiantesControlador.php" method="POST">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idEstudiante" value="<?php echo $id; ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo</label>
                <input type="text" name="nombreEstudiante" 
                       class="<?php echo isset($errores['nombreEstudiante']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['nombreEstudiante'] ?? $datosEstudianteBD['nombreEstudiante']); ?>">
                <?php if (isset($errores['nombreEstudiante'])) echo "<p class='error-campo'>{$errores['nombreEstudiante']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Correo Electrónico</label>
                <input type="text" name="emailEstudiante" 
                       class="<?php echo isset($errores['emailEstudiante']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['emailEstudiante'] ?? $datosEstudianteBD['emailEstudiante']); ?>">
                <?php if (isset($errores['emailEstudiante'])) echo "<p class='error-campo'>{$errores['emailEstudiante']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono</label>
                <input type="text" name="telefonoEstudiante" 
                       value="<?php echo htmlspecialchars($datosViejos['telefonoEstudiante'] ?? $datosEstudianteBD['telefonoEstudiante']); ?>">
            </div>

            <div class="campo-formulario">
                <label>DNI</label>
                <input type="text" name="dniEstudiante" 
                       class="<?php echo isset($errores['dniEstudiante']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['dniEstudiante'] ?? $datosEstudianteBD['dniEstudiante']); ?>">
                <?php if (isset($errores['dniEstudiante'])) echo "<p class='error-campo'>{$errores['dniEstudiante']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Nacimiento (DD-MM-YYYY)</label>
                <input type="text" name="fechaNacimientoEstudiante" 
                       class="<?php echo isset($errores['fechaNacimientoEstudiante']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['fechaNacimientoEstudiante'] ?? $fechaFormateada); ?>">
                <?php if (isset($errores['fechaNacimientoEstudiante'])) echo "<p class='error-campo'>{$errores['fechaNacimientoEstudiante']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Curso</label>
                <select name="idCurso">
                    <?php foreach ($listaCursos as $c) { 
                        $idActual = $datosViejos['idCurso'] ?? $datosEstudianteBD['idCurso'];
                        $selected = ($idActual == $c['idCurso']) ? 'selected' : '';
                        echo "<option value='{$c['idCurso']}' {$selected}>{$c['nombreCurso']}</option>";
                    } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Estado</label>
                <select name="idEstado">
                    <?php 
                    $estadoActual = $datosViejos['idEstado'] ?? $datosEstudianteBD['idEstado'];
                    ?>
                    <option value="1" <?php echo $estadoActual == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="2" <?php echo $estadoActual == 2 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección</label>
                <input type="text" name="direccionEstudiante" 
                       value="<?php echo htmlspecialchars($datosViejos['direccionEstudiante'] ?? $datosEstudianteBD['direccionEstudiante']); ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" class="boton-azul">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
