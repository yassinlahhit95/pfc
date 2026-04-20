<?php
session_start();
$titulo_pagina = "Nueva Reclamación - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/profesores.php";

$listaEstudiantes = listarEstudiantes();
$listaProfesores = listarProfesores();

// Errores y datos persistentes
$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_reclamaciones'])) {
    $datos = $_SESSION['datos_reclamaciones'];
}
unset($_SESSION['errores'], $_SESSION['datos_reclamaciones']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Nueva Reclamación</h1>
        <p class="subtitulo-encabezado">Registre un suceso o queja formal</p>
    </div>
    <a href="vistas/reclamaciones/verReclamaciones.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/reclamaciones/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante Implicado</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($listaEstudiantes as $est) { 
                        $selected = '';
                        if (isset($datos['idEstudiante']) && $datos['idEstudiante'] == $est['idEstudiante']) {
                            $selected = 'selected';
                        }
                        echo "<option value='{$est['idEstudiante']}' {$selected}>{$est['nombreEstudiante']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])) { echo "<p class='error-campo'>{$errores['idEstudiante']}</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Profesor que Reporta</label>
                <select name="idProfesor">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($listaProfesores as $prof) { 
                        $selected = '';
                        if (isset($datos['idProfesor']) && $datos['idProfesor'] == $prof['idProfesor']) {
                            $selected = 'selected';
                        }
                        echo "<option value='{$prof['idProfesor']}' {$selected}>{$prof['nombreProfesor']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idProfesor'])) { echo "<p class='error-campo'>{$errores['idProfesor']}</p>"; } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Asunto / Motivo corto</label>
                <?php 
                $asunto = '';
                if (isset($datos['asunto'])) {
                    $asunto = $datos['asunto'];
                }
                ?>
                <input type="text" name="asunto" value="<?php echo $asunto; ?>" placeholder="Ej: Falta de respeto, Rotura de material...">
                <?php if (isset($errores['asunto'])) { echo "<p class='error-campo'>{$errores['asunto']}</p>"; } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción detallada</label>
                <?php 
                $descripcion = '';
                if (isset($datos['descripcion'])) {
                    $descripcion = $datos['descripcion'];
                }
                ?>
                <textarea name="descripcion" rows="5"><?php echo $descripcion; ?></textarea>
                <?php if (isset($errores['descripcion'])) { echo "<p class='error-campo'>{$errores['descripcion']}</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Gravedad</label>
                <select name="gravedad">
                    <?php 
                    $gravedad = '';
                    if (isset($datos['gravedad'])) {
                        $gravedad = $datos['gravedad'];
                    }
                    ?>
                    <option value="leve" <?php if ($gravedad == 'leve') { echo 'selected'; } ?>>Leve</option>
                    <option value="moderada" <?php if ($gravedad == 'moderada') { echo 'selected'; } ?>>Moderada</option>
                    <option value="grave" <?php if ($gravedad == 'grave') { echo 'selected'; } ?>>Grave</option>
                </select>
                <?php if (isset($errores['gravedad'])) { echo "<p class='error-campo'>{$errores['gravedad']}</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha del Suceso</label>
                <?php 
                $fecha = date('Y-m-d');
                if (isset($datos['fecha'])) {
                    $fecha = $datos['fecha'];
                }
                ?>
                <input type="date" name="fecha" value="<?php echo $fecha; ?>">
                <?php if (isset($errores['fecha'])) { echo "<p class='error-campo'>{$errores['fecha']}</p>"; } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarReclamacion" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Reclamación
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>