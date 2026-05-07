<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_mensaje'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_mensaje']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfesor = $_SESSION['idProfesor'];
$listaDeCiclos = obtenerCiclosDeProfesor($idProfesor);
$idCicloSeleccionado = $_GET['idCiclo'] ?? "";

if (!empty($idCicloSeleccionado)) {
    $listaDeEstudiantes = listarEstudiantesPorCiclo($idCicloSeleccionado);
} else {
    $listaDeEstudiantes = listarEstudiantesPorProfesor($idProfesor);
}

$tituloDelPagina = "Nuevo Mensaje - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Redactar Mensaje</h1>
    <a href="lista.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="GET" class="margen-abajo">
        <div class="disposicion-flexible alinear-fin separacion-grande">
            <div class="campo-formulario flexible-rellenar">
                <label for="idCiclo">Filtrar Estudiantes por Ciclo:</label>
                <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos mis alumnos --</option>
                    <?php foreach ($listaDeCiclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloSeleccionado == $ciclo['idCiclo'] ? 'selected' : '') ?>>
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-15">
                <a href="agregar.php" class="boton-secundario">LIMPIAR FILTRO</a>
            </div>
        </div>
    </form>

    <div class="titulo-tarjeta mt-30">
        <h3><i class="fas fa-paper-plane"></i> NUEVO MENSAJE</h3>
    </div>

    <form action="../../../controladores/profesores/mensajes/insertar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idProfesor" value="<?= $idProfesor ?>">

        <div class="campo-formulario">
            <label for="idEstudiante">Destinatario</label>
            <select name="idEstudiante" id="idEstudiante" class="<?= isset($errores['idEstudiante']) ? 'input-error' : '' ?>">
                <option value="">-- Seleccionar Destinatario --</option>
                <option value="1" <?= ($datos['idEstudiante'] ?? '') == '1' ? 'selected' : '' ?>>Dirección (Administración)</option>
                <optgroup label="Estudiantes">
                    <?php foreach ($listaDeEstudiantes as $estudiante) {
                        $selected = ($datos['idEstudiante'] ?? '') == $estudiante['idEstudiante'] ? 'selected' : '';
                    ?>
                        <option value="<?= $estudiante['idEstudiante'] ?>" <?= $selected ?>>
                            <?= $estudiante['nombreEstudiante'] ?> (<?= $estudiante['nombreCiclo'] ?>)
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
            <?php if (isset($errores['idEstudiante'])) { ?>
                <strong class="error-campo"><?= $errores['idEstudiante'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label for="asunto">Asunto del Mensaje</label>
            <input type="text" name="asunto" id="asunto" value="<?= $datos['asunto'] ?? '' ?>" placeholder="Escriba el motivo del mensaje..." class="<?= isset($errores['asunto']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['asunto'])) { ?>
                <strong class="error-campo"><?= $errores['asunto'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label for="descripcion">Mensaje</label>
            <textarea name="descripcion" id="descripcion" rows="6" placeholder="Escribe aquí tu mensaje (máximo 250 caracteres)..." maxlength="250" class="<?= isset($errores['descripcion']) ? 'input-error' : '' ?>"><?= $datos['descripcion'] ?? '' ?></textarea>
            <?php if (isset($errores['descripcion'])) { ?>
                <strong class="error-campo"><?= $errores['descripcion'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="enviarMensaje" class="boton-primario">
                <i class="fas fa-paper-plane"></i> ENVIAR MENSAJE
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>