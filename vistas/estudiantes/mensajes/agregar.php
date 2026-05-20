<?php
session_start();

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_mensaje'] ?? [];

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";

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

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/estudiantes/mensajes/insertar.php" method="POST" class="formulario">
        <input type="hidden" name="idEstudiante" value="<?= $idEst ?>">

        <div class="campo">
            <label>Destinatario (Profesor o Dirección)</label>
            <select name="idProfesor">
                <option value="">-- Dirección (Administración) --</option>
                <?php foreach ($profs as $p) { ?>
                    <option value="<?= $p['idProfesor'] ?>" <?= ($datos['idProfesor'] ?? '') == $p['idProfesor'] ? 'selected' : '' ?>>
                        <?= $p['nombreProfesor'] . " (" . $p['nombreModulo'] . ")" ?>
                    </option>
                <?php } ?>
            </select>
            <span class="texto-suave">Selecciona a quién quieres dirigir tu consulta.</span>
        </div>

        <div class="campo">
            <label>Asunto del Mensaje</label>
            <input type="text" name="asunto" value="<?= $datos['asunto'] ?? '' ?>" placeholder="Ej: Consulta sobre nota">
        </div>

        <div class="campo">
            <label>Contenido del Mensaje</label>
            <textarea name="descripcion" rows="5" placeholder="Escribe tu mensaje aquí..." maxlength="250"><?= $datos['descripcion'] ?? '' ?></textarea>
        </div>

        <div class="acciones">
            <input type="submit" name="enviarMensaje" class="boton-primario" value="ENVIAR MENSAJE">
            <input type="reset" class="boton-secundario" value="Limpiar">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
