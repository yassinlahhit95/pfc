<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    $_SESSION['errores'] = "Estudiante no encontrado.";
    header("Location: lista.php");
    exit;
}

$tituloDelPagina = "AULAPRO | EDITAR ESTUDIANTE";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

$misCiclos = listarCiclosDeProfesor($idProfesor);

$datos = $_SESSION['datos_estudiante'] ?? $estudiante;

?>

<div class="cabecera">
    <h1>EDITAR ESTUDIANTE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/profesores/estudiantes/actualizar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante ) ?>">
        <div class="formulario">
            <div class="campo">
                <label for="nombreEstudiante">Nombre Completo</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= Security::escapeHtml($datos['nombreEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="emailEstudiante">Email</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= Security::escapeHtml($datos['emailEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="dniEstudiante">DNI</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= Security::escapeHtml($datos['dniEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="telefonoEstudiante">Teléfono</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= Security::escapeHtml($datos['telefonoEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= Security::escapeHtml($datos['fechaNacimientoEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= Security::escapeHtml($datos['direccionEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="ciudadEstudiante">Ciudad</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= Security::escapeHtml($datos['ciudadEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="codigoPostalEstudiante">Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= Security::escapeHtml($datos['codigoPostalEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="idCiclo">Ciclo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona un ciclo --</option>
                    <?php foreach ($misCiclos as $ciclo) { ?>
                        <option value="<?= Security::escapeHtml($ciclo['idCiclo'] ) ?>" <?php if ($datos['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                            [<?= Security::escapeHtml($ciclo['nombreNivel'] ) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo'] ) ?>
                        </option>
                    <?php } ?>
                </select>

            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarEstudiante" class="boton-primario" value="ACTUALIZAR ESTUDIANTE">
            <button type="button" class="boton-secundario" onclick="window.location.reload();">
                <i class="fas fa-eraser"></i> limpiar
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
