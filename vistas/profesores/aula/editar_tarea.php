<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../include/Security.php";

$idProfesor = $_SESSION['idProfesor'];
$idTarea = $_GET['id'] ?? null;

if (!$idTarea) {
    header("Location: tareas.php");
    exit;
}

$tarea = obtenerTareaPorIdAula($idTarea);

if (!$tarea || $tarea['idProfesor'] != $idProfesor) {
    header("Location: tareas.php");
    exit;
}

$modulos = listarModulosDeProfesor($idProfesor);
$csrfToken = Security::generateCSRFToken();

$tituloDelPagina = 'AULAPRO | EDITAR TAREA';
$seccionActual = 'aula_tareas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR TAREA</h1>
    <p class="texto-suave">Modifica los detalles de la tarea</p>
</div>

<form method="POST" action="../../../controladores/aula/actualizar_tarea.php" enctype="multipart/form-data" class="formulario-principal">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($tarea['idTarea']) ?>">

    <div class="grupo-formulario">
        <label for="idModulo">MÓDULO *</label>
        <select id="idModulo" name="idModulo" required>
            <?php foreach ($modulos as $modulo) { ?>
                <option value="<?= $modulo['idModulo'] ?>" <?= ($modulo['idModulo'] == $tarea['idModulo']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($modulo['nombreModulo']) ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="grupo-formulario">
        <label for="titulo">TÍTULO DE LA TAREA *</label>
        <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($tarea['titulo']) ?>" maxlength="200">
    </div>

    <div class="grupo-formulario">
        <label for="descripcion">DESCRIPCIÓN Y INSTRUCCIONES *</label>
        <textarea id="descripcion" name="descripcion" rows="8" required><?= htmlspecialchars($tarea['descripcion']) ?></textarea>
    </div>

    <div class="grupo-formulario">
        <label>ARCHIVO ADJUNTO</label>
        <?php if ($tarea['archivoAdjunto']) { ?>
            <p style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <strong>Archivo actual:</strong> <?= htmlspecialchars($tarea['archivoAdjunto']) ?>
            </p>
        <?php } ?>
        <input type="file" id="archivo" name="archivo" accept=".pdf,.doc,.docx,.zip,.rar,.txt">
        <span class="texto-pequeño texto-suave">Sube un nuevo archivo para reemplazarlo</span>
    </div>

    <div class="grupo-formulario">
        <label>
            <input type="checkbox" name="publicar" value="1" <?= ($tarea['publicada'] == 1) ? 'checked' : '' ?>>
            <span><strong>Publicada</strong> (Los estudiantes pueden verla)</span>
        </label>
    </div>

    <div class="grupo-botones">
        <a href="tareas.php" class="boton-secundario">CANCELAR</a>
        <button type="submit" class="boton-primario">
            <i class="fas fa-save"></i> GUARDAR CAMBIOS
        </button>
    </div>
</form>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
