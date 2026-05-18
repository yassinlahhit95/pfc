<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errs = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_mensaje'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_mensaje']);

$tituloDelPagina = "AULAPRO | NUEVO MENSAJE";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";

$idEst = $_SESSION['idEstudiante'];
$profs = listarProfesoresConModulosParaEstudiante($idEst);
?>

<div class="cabecera">
    <h1>NUEVO MENSAJE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/estudiantes/mensajes/insertar.php" method="POST" class="formulario">
        <input type="hidden" name="idEstudiante" value="<?= $idEst ?>">
        
        <div class="campo">
            <label>Destinatario (Profesor o Dirección)</label>
            <select name="idProfesor" class="<?= isset($errs['idProfesor']) ? 'input-error' : '' ?>">
                <option value="">-- Dirección (Administración) --</option>
                <?php foreach ($profs as $p) { ?>
                    <option value="<?= $p['idProfesor'] ?>" <?= ($datos['idProfesor'] ?? '') == $p['idProfesor'] ? 'selected' : '' ?>>
                        <?= $p['nombreProfesor'] . " (" . $p['nombreModulo'] . ")" ?>
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($errs['idProfesor'])) { ?>
                <strong class="error-campo"><?= $errs['idProfesor'] ?></b>
            <?php } ?>
            <small class="atenuado">Selecciona a quién quieres dirigir tu consulta.</small>
        </div>

        <div class="campo">
            <label>Asunto del Mensaje</label>
            <input type="text" name="asunto" value="<?= $datos['asunto'] ?? '' ?>" class="<?= isset($errs['asunto']) ? 'input-error' : '' ?>" placeholder="Duda sobre contenido, problema técnico...">
            <?php if (isset($errs['asunto'])) { ?>
                <strong class="error-campo"><?= $errs['asunto'] ?></b>
            <?php } ?>
        </div>

        <div class="campo">
            <label>Contenido del Mensaje</label>
            <textarea name="descripcion" rows="6" class="<?= isset($errs['descripcion']) ? 'input-error' : '' ?>" placeholder="Escribe aquí tu mensaje (máximo 250 caracteres)..." maxlength="250"><?= $datos['descripcion'] ?? '' ?></textarea>
            <?php if (isset($errs['descripcion'])) { ?>
                <strong class="error-campo"><?= $errs['descripcion'] ?></b>
            <?php } ?>
        </div>

        <div class="acciones">
            <button type="submit" name="enviarMensaje" class="boton-primario">
                <i class="fas fa-paper-plane"></i> ENVIAR MENSAJE
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
