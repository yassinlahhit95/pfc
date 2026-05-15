<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/ciclos.php";

$tituloDelPagina = "AULAPRO | NUEVO ESTUDIANTE";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

$mis_ciclos = obtenerCiclosDeProfesor($idProfesor);

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_estudiante'] ?? [];
$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['errores'], $_SESSION['datos_estudiante'], $_SESSION['error'], $_SESSION['exito']);

$nivelActual = '';
if (!empty($datos['idCiclo'])) {
    foreach ($mis_ciclos as $c) {
        if ($c['idCiclo'] == $datos['idCiclo']) {
            $nivelActual = $c['idNivel'];
            break;
        }
    }
}
?>

<div class="encabezado-pagina">
    <h1>NUEVO ESTUDIANTE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/estudiantes/insertar.php" method="POST">
        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="nombreEstudiante">Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= $datos['nombreEstudiante'] ?? '' ?>">
                <?php if (isset($errores['nombreEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="emailEstudiante">Email *</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= $datos['emailEstudiante'] ?? '' ?>">
                <?php if (isset($errores['emailEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['emailEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="dniEstudiante">DNI *</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= $datos['dniEstudiante'] ?? '' ?>">
                <?php if (isset($errores['dniEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['dniEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="telefonoEstudiante">Teléfono *</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= $datos['telefonoEstudiante'] ?? '' ?>">
                <?php if (isset($errores['telefonoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento *</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= $datos['fechaNacimientoEstudiante'] ?? '' ?>">
                <?php if (isset($errores['fechaNacimientoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaNacimientoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="direccionEstudiante">Dirección *</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= $datos['direccionEstudiante'] ?? '' ?>">
                <?php if (isset($errores['direccionEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['direccionEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="ciudadEstudiante">Ciudad *</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= $datos['ciudadEstudiante'] ?? '' ?>">
                <?php if (isset($errores['ciudadEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['ciudadEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="codigoPostalEstudiante">Código Postal *</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= $datos['codigoPostalEstudiante'] ?? '' ?>">
                <?php if (isset($errores['codigoPostalEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['codigoPostalEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idCiclo">Ciclo *</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona un ciclo --</option>
                    <optgroup label="Grado Medio">
                        <?php foreach ($mis_ciclos as $ciclo) { ?>
                            <?php if ($ciclo['idNivel'] == 1) { ?>
                                <option value="<?= $ciclo['idCiclo'] ?>" <?php if (isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                                    <?= $ciclo['nombreCiclo'] ?>
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </optgroup>
                    <optgroup label="Grado Superior">
                        <?php foreach ($mis_ciclos as $ciclo) { ?>
                            <?php if ($ciclo['idNivel'] == 2) { ?>
                                <option value="<?= $ciclo['idCiclo'] ?>" <?php if (isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                                    <?= $ciclo['nombreCiclo'] ?>
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </optgroup>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="curso">Curso *</label>
                <select name="curso" id="curso">
                    <option value="1" <?php if (isset($datos['curso']) && $datos['curso'] == 1) { echo 'selected'; } ?>>1º Curso</option>
                    <option value="2" <?php if (isset($datos['curso']) && $datos['curso'] == 2) { echo 'selected'; } ?>>2º Curso</option>
                </select>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarEstudiante" class="boton-primario">
                <i class="fas fa-save"></i> REGISTRAR ESTUDIANTE
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>


<?php include __DIR__ . '/../comunes/footer.php'; ?>
