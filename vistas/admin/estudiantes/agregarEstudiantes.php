<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$lista_de_errores = [];
$lista_de_errores = ($_SESSION['errores'] ?? 0);

$datos = [];
$datos = ($_SESSION['datos_estudiante'] ?? 0);

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['errores'], $_SESSION['datos_estudiante'], $_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Nuevo Estudiante</h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario">Volver</a>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/estudiantes/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" value="<?php if(isset($datos['nombreEstudiante'])) { echo $datos['nombreEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['nombreEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['nombreEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailEstudiante" value="<?php if(isset($datos['emailEstudiante'])) { echo $datos['emailEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['emailEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['emailEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniEstudiante" value="<?php if(isset($datos['dniEstudiante'])) { echo $datos['dniEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['dniEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['dniEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoEstudiante" value="<?php if(isset($datos['telefonoEstudiante'])) { echo $datos['telefonoEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['telefonoEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['telefonoEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Nacimiento *</label>
                <input type="date" name="fechaNacimientoEstudiante" value="<?php if(isset($datos['fechaNacimientoEstudiante'])) { echo $datos['fechaNacimientoEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['fechaNacimientoEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['fechaNacimientoEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionEstudiante" value="<?php if(isset($datos['direccionEstudiante'])) { echo $datos['direccionEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['direccionEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['direccionEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadEstudiante" value="<?php if(isset($datos['ciudadEstudiante'])) { echo $datos['ciudadEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['ciudadEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['ciudadEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalEstudiante" value="<?php if(isset($datos['codigoPostalEstudiante'])) { echo $datos['codigoPostalEstudiante']; } ?>">
                <?php if (isset($lista_de_errores['codigoPostalEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['codigoPostalEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo *</label>
                <select name="idCiclo">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?php if(isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) { echo "selected"; } ?>>
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['idCiclo'] ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarEstudiante" class="boton-primario">Registrar Estudiante</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

