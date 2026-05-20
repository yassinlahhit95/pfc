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

<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <form method="GET" class="margen-abajo">
        <div class="caja al-final espacio-grande">
            <div class="campo relleno">
                <label for="idCiclo">Filtrar Estudiantes por Ciclo:</label>
                <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos mis alumnos --</option>
                    <?php foreach ($listaDeCiclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloSeleccionado == $ciclo['idCiclo'] ? 'selected' : '') ?>>
                            <?= $ciclo['abreviaturaCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <a href="agregar.php" class="boton-secundario">LIMPIAR FILTRO</a>
            </div>
        </div>
    </form>

    <div class="titulo-tarjeta" style="margin-top: 30px;">
        <h3><i class="fas fa-paper-plane"></i> NUEVO MENSAJE</h3>
    </div>

    <form action="../../../controladores/profesores/mensajes/insertar.php" method="POST" class="formulario">
        <input type="hidden" name="idProfesor" value="<?= $idProfesor ?>">

        <div class="campo">
            <label for="idEstudiante">Destinatario</label>
            <select name="idEstudiante" id="idEstudiante" class="<?= isset($errores['idEstudiante']) ? 'input-error' : '' ?>">
                <option value="">-- Seleccionar Destinatario --</option>
                <option value="1" <?= ($datos['idEstudiante'] ?? '') == '1' ? 'selected' : '' ?>>Direccion (Administracion)</option>
                <optgroup label="Estudiantes">
                    <?php foreach ($listaDeEstudiantes as $estudiante) {
                        $selected = ($datos['idEstudiante'] ?? '') == $estudiante['idEstudiante'] ? 'selected' : '';
                    ?>
                        <option value="<?= $estudiante['idEstudiante'] ?>" <?= $selected ?>>
                            <?= $estudiante['nombreEstudiante'] ?> (<?= $estudiante['abreviaturaCiclo'] ?>)
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
            <?php if (isset($errores['idEstudiante'])) { ?>
                <strong class="error-campo"><?= $errores['idEstudiante'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="asunto">Asunto del Mensaje</label>
            <input type="text" name="asunto" id="asunto" value="<?= $datos['asunto'] ?? '' ?>" placeholder="Escriba el motivo del mensaje..." class="<?= isset($errores['asunto']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['asunto'])) { ?>
                <strong class="error-campo"><?= $errores['asunto'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="descripcion">Mensaje</label>
            <textarea name="descripcion" id="descripcion" rows="6" placeholder="Escribe aqui tu mensaje (maximo 250 caracteres)..." maxlength="250" class="<?= isset($errores['descripcion']) ? 'input-error' : '' ?>"><?= $datos['descripcion'] ?? '' ?></textarea>
            <?php if (isset($errores['descripcion'])) { ?>
                <strong class="error-campo"><?= $errores['descripcion'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones">
            <input type="submit" name="enviarMensaje" class="boton-primario" value="ENVIAR MENSAJE">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
