<?php
session_start();
$titulo_pagina = "Nueva Reclamación - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

require_once "../../modelos/estudiantes.php";
require_once "../../modelos/profesores.php";

$modeloEstudiante = new estudiante();
$modeloProfesor = new profesor();

$listaEstudiantes = $modeloEstudiante->listarEstudiantesModelo();
$listaProfesores = $modeloProfesor->listarProfesoresModelo();

// Errores y datos persistentes
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_reclamaciones'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_reclamaciones']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Redactar Incidencia / Reclamación</h1>
        <p class="subtitulo-encabezado">Registre una nueva queja o parte de disciplina</p>
    </div>
    <a href="verReclamaciones.php" class="boton-secundario">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/reclamaciones/insertar.php" method="POST">
        <input type="hidden" name="accion" value="insertar">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante Implicado</label>
                <select name="idEstudiante">
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
                <select name="idProfesor">
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
                       value="<?php echo $datos['asunto'] ?? ''; ?>" placeholder="Ej: Falta de respeto, Rotura de material...">
                <?php if (isset($errores['asunto'])) echo "<p class='error-campo'>{$errores['asunto']}</p>"; ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción detallada</label>
                <textarea name="descripcion" rows="5"><?php echo $datos['descripcion'] ?? ''; ?></textarea>
                <?php if (isset($errores['descripcion'])) echo "<p class='error-campo'>{$errores['descripcion']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Gravedad</label>
                <select name="gravedad">
                    <option value="leve" <?php echo ($datos['gravedad'] ?? '') == 'leve' ? 'selected' : ''; ?>>Leve</option>
                    <option value="moderada" <?php echo ($datos['gravedad'] ?? '') == 'moderada' ? 'selected' : ''; ?>>Moderada</option>
                    <option value="grave" <?php echo ($datos['gravedad'] ?? '') == 'grave' ? 'selected' : ''; ?>>Grave</option>
                </select>
                <?php if (isset($errores['gravedad'])) echo "<p class='error-campo'>{$errores['gravedad']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha del Suceso</label>
                <input type="date" name="fecha" value="<?php echo $datos['fecha'] ?? date('Y-m-d'); ?>">
                <?php if (isset($errores['fecha'])) echo "<p class='error-campo'>{$errores['fecha']}</p>"; ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarReclamacion" class="boton-primario">Registrar Reclamación</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
