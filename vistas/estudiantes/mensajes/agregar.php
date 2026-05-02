<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

$tituloDelPagina = "Nuevo Mensaje - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);

// Obtenemos los profesores asignados con sus m�dulos para el select
$listaDeProfesores = obtenerProfesoresConModulosParaEstudiante($idEstudiante);
?>

<div class="encabezado-pagina">
    <h1>Nuevo Mensaje</h1>
    <a href="lista.php" class="boton-secundario">? Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/estudiantes/mensajes/insertar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idEstudiante" value="<?= $idEstudiante ?>">
        
        <div class="campo-formulario">
            <label>Destinatario (Profesor o Direcci�n)</label>
            <select name="idProfesor">
                <option value="">-- Direcci�n (Administraci�n) --</option>
                <?php foreach ($listaDeProfesores as $profesor) { ?>
                    <option value="<?= $profesor['idProfesor'] ?>">
                        <?= $profesor['nombreProfesor'] . " (" . $profesor['nombreModulo'] . ")" ?>
                    </option>
                <?php } ?>
            </select>
            <small class="texto-atenuado">Selecciona a qui�n quieres dirigir tu consulta.</small>
        </div>

        <div class="campo-formulario">
            <label>Asunto del Mensaje</label>
            <input type="text" name="asunto" placeholder="Duda sobre contenido, problema t�cnico...">
        </div>

        <div class="campo-formulario">
            <label>Contenido del Mensaje</label>
            <textarea name="descripcion" rows="6" placeholder="Escribe aqu� tu mensaje (m�ximo 250 caracteres)..." maxlength="250"></textarea>
        </div>

        <div class="form-acciones">
            <button type="submit" name="enviarMensaje" class="boton-primario">
                <i class="fas fa-paper-plane"></i> ENVIAR MENSAJE
            </button>
            <button type="button" class="boton-secundario px-25" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
            <a href="lista.php" class="boton-secundario ml-10">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


