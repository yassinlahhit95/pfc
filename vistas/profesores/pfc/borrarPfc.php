<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . '/../../../modelos/tfg.php';
require_once __DIR__ . '/../../../modelos/estudiantes.php';
$id = (int)($_GET['id'] ?? 0);
if (!estudiantePerteneceAProfesor($id, $_SESSION['idProfesor'])) {
    header("Location: lista.php"); exit;
}
$registro = obtenerTFGporEstudiante($id);
if (!$registro) { header("Location: lista.php"); exit; }
$tituloDelPagina = 'AULAPRO | CONFIRMAR ELIMINACIÓN';
$seccionActual = '';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div><h1>Eliminar TFG</h1></div>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="panel panel-peligro" style="max-width:520px;">
    <div class="peligro-header">
        <div class="peligro-icono"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <p class="peligro-titulo">Confirmar eliminación</p>
            <p class="peligro-subtitulo">Estás a punto de eliminar el TFG del siguiente estudiante:</p>
        </div>
    </div>

    <div class="peligro-registro">
        <i class="fas fa-file-alt"></i>
        <?= Security::escapeHtml($registro['nombreEstudiante']) ?>
    </div>

    <div class="peligro-aviso">
        <i class="fas fa-exclamation-circle"></i>
        El archivo del TFG será eliminado permanentemente y no se puede deshacer.
    </div>

    <div class="peligro-acciones">
        <a href="lista.php" class="boton-secundario"><i class="fas fa-times"></i> Cancelar</a>
        <form method="POST" action="../../../controladores/profesores/pfc/borrar.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" value="<?= (int)$id ?>">
            <button type="submit" class="boton-peligro"><i class="fas fa-trash"></i> Sí, eliminar</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
