<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idEstudiante = $_GET['idEstudiante'] ?? 0;
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    $_SESSION['error'] = "Estudiante no encontrado.";
    header("Location: lista.php");
    exit;
}

$tituloDelPagina = "AULAPRO | EDITAR ESTUDIANTE";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

$mis_ciclos = listarCiclosDeProfesor($idProfesor);

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_estudiante'] ?? $estudiante;
$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['errores'], $_SESSION['datos_estudiante'], $_SESSION['error'], $_SESSION['exito']);

?>

<div class="cabecera">
    <h1>EDITAR ESTUDIANTE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/profesores/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $idEstudiante ?>">
        <div class="formulario">
            <div class="campo">
                <label for="nombreEstudiante">Nombre Completo</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= $datos['nombreEstudiante'] ?? '' ?>">
                <?php if (isset($errores['nombreEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="emailEstudiante">Email</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= $datos['emailEstudiante'] ?? '' ?>">
                <?php if (isset($errores['emailEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['emailEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="dniEstudiante">DNI</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= $datos['dniEstudiante'] ?? '' ?>">
                <?php if (isset($errores['dniEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['dniEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="telefonoEstudiante">Teléfono</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= $datos['telefonoEstudiante'] ?? '' ?>">
                <?php if (isset($errores['telefonoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= $datos['fechaNacimientoEstudiante'] ?? '' ?>">
                <?php if (isset($errores['fechaNacimientoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaNacimientoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= $datos['direccionEstudiante'] ?? '' ?>">
                <?php if (isset($errores['direccionEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['direccionEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="ciudadEstudiante">Ciudad</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= $datos['ciudadEstudiante'] ?? '' ?>">
                <?php if (isset($errores['ciudadEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['ciudadEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="codigoPostalEstudiante">Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= $datos['codigoPostalEstudiante'] ?? '' ?>">
                <?php if (isset($errores['codigoPostalEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['codigoPostalEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="idCiclo">Ciclo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona un ciclo --</option>
                    <?php foreach ($mis_ciclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?php if ($datos['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                            [<?= $ciclo['nombreNivel'] ?>] <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarEstudiante" class="boton-primario" value="ACTUALIZAR ESTUDIANTE">
            <button type="button" class="boton-secundario" onclick="window.location.reload();">
                <i class="fas fa-eraser"></i> REVERTIR CAMBIOS
            </button>
        </div>
    </form>
</div>


<?php include __DIR__ . '/../comunes/footer.php'; ?>
