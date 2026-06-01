<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_mensaje'] ?? [];

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfesor = $_SESSION['idProfesor'];
$listaDeCiclos = listarCiclosDeProfesor($idProfesor);
$idCicloSeleccionado = $_GET['idCiclo'] ?? "";

if (!empty($idCicloSeleccionado)) {
    $listaDeEstudiantes = listarEstudiantesPorCiclo($idCicloSeleccionado);
} else {
    $listaDeEstudiantes = listarEstudiantesDeProfesor($idProfesor);
}

$tituloDelPagina = "AULAPRO | NUEVO MENSAJE";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>REDACTAR MENSAJE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

<div class="panel">
    <form method="GET" class="margen-abajo">
        <div class="caja al-final espacio-grande">
            <div class="campo relleno">
                <label for="idCiclo">Filtrar Estudiantes por Ciclo:</label>
                <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos mis alumnos --</option>
                    <?php foreach ($listaDeCiclos as $ciclo) { ?>
                        <option value="<?= Security::escapeHtml($ciclo['idCiclo'] ) ?>" <?= Security::escapeHtml(($idCicloSeleccionado == $ciclo['idCiclo'] ? 'selected' : '')) ?>>
                            <?= Security::escapeHtml($ciclo['abreviaturaCiclo'] ) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <input type="reset" class="boton-secundario" value="LIMPIAR">
            </div>
        </div>
    </form>

    <div class="titulo-tarjeta" style="margin-top: 30px;">
        <h3><i class="fas fa-paper-plane"></i> NUEVO MENSAJE</h3>
    </div>

    <form action="../../../controladores/profesores/mensajes/insertar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idProfesor" value="<?= Security::escapeHtml($idProfesor ) ?>">

        <div class="campo">
            <label for="idEstudiante">Destinatario</label>
            <select name="idEstudiante" id="idEstudiante">
                <option value="">-- Seleccionar Destinatario --</option>
                <option value="1" <?= Security::escapeHtml(($datos['idEstudiante'] ?? '') == '1' ? 'selected' : '') ?>>Direccion (Administracion)</option>
                <optgroup label="Estudiantes">
                    <?php foreach ($listaDeEstudiantes as $estudiante) {
                        $selected = ($datos['idEstudiante'] ?? '') == $estudiante['idEstudiante'] ? 'selected' : '';
                    ?>
                        <option value="<?= Security::escapeHtml($estudiante['idEstudiante'] ) ?>" <?= Security::escapeHtml($selected) ?>>
                            <?= Security::escapeHtml($estudiante['nombreEstudiante'] ) ?> (<?= Security::escapeHtml($estudiante['abreviaturaCiclo'] ) ?>)
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
        </div>

        <div class="campo">
            <label for="asunto">Asunto del Mensaje</label>
            <input type="text" name="asunto" id="asunto" value="<?= Security::escapeHtml($datos['asunto'] ?? '') ?>" placeholder="Asunto del mensaje">
        </div>

        <div class="campo">
            <label for="descripcion">Mensaje</label>
            <textarea name="descripcion" id="descripcion" rows="4" placeholder="Escribe tu mensaje..."><?= Security::escapeHtml($datos['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="acciones">
            <input type="submit" name="enviarMensaje" class="boton-primario" value="ENVIAR MENSAJE">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


