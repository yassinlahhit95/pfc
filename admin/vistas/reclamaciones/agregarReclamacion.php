<?php
session_start();
$titulo_pagina = "Nueva Reclamación - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/profesores.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();

$modeloEstudiante = new estudiante($conexionBD);
$modeloProfesor = new profesor($conexionBD);

$listaEstudiantes = $modeloEstudiante->listarEstudiantesModelo();
$listaProfesores = $modeloProfesor->listarProfesoresModelo();

// Errores y datos persistentes
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_viejos'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_viejos']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Redactar Incidencia / Reclamación</h1>
    <a href="vistas/reclamaciones/verReclamaciones.php" class="boton-gris">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/reclamacionesControlador.php" method="POST">
        <input type="hidden" name="accion" value="insertar">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante Implicado</label>
                <select name="idEstudiante" class="<?php echo isset($errores['idEstudiante']) ? 'input-error' : ''; ?>">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($listaEstudiantes as $est) {
                        $selected = ($datos['idEstudiante'] ?? '') == $est['idEstudiante'] ? 'selected' : '';
                        echo "<option value='{$est['idEstudiante']}' {$selected}>{$est['nombreEstudiante']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])) echo "<p class='error-campo'>{$errores['idEstudiante']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Profesor que Reporta</label>
                <select name="idProfesor" class="<?php echo isset($errores['idProfesor']) ? 'input-error' : ''; ?>">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($listaProfesores as $prof) {
                        $selected = ($datos['idProfesor'] ?? '') == $prof['idProfesor'] ? 'selected' : '';
                        echo "<option value='{$prof['idProfesor']}' {$selected}>{$prof['nombreProfesor']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idProfesor'])) echo "<p class='error-campo'>{$errores['idProfesor']}</p>"; ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Asunto / Motivo corto</label>
                <input type="text" name="asunto" 
                       class="<?php echo isset($errores['asunto']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datos['asunto'] ?? ''); ?>" placeholder="Ej: Falta de respeto, Rotura de material...">
                <?php if (isset($errores['asunto'])) echo "<p class='error-campo'>{$errores['asunto']}</p>"; ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción detallada</label>
                <textarea name="descripcion" rows="5" class="p-10 border-radius-8 border-gray <?php echo isset($errores['descripcion']) ? 'input-error' : ''; ?>"><?php echo htmlspecialchars($datos['descripcion'] ?? ''); ?></textarea>
                <?php if (isset($errores['descripcion'])) echo "<p class='error-campo'>{$errores['descripcion']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Gravedad</label>
                <select name="gravedad">
                    <option value="leve">Leve</option>
                    <option value="moderada">Moderada</option>
                    <option value="grave">Grave</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Fecha del Suceso</label>
                <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" class="boton-azul">Registrar Reclamación</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
