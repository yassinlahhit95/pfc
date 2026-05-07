<?php
session_start();
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$id_del_estudiante = $_GET['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id_del_estudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

$datosGuardados = $_SESSION['datos_estudiante'] ?? [];
if (!is_array($datosGuardados)) {
    $datosGuardados = [];
}

if (!empty($datosGuardados)) {
    foreach ($datosGuardados as $key => $value) {
        $estudiante[$key] = $value;
    }
}

$todos_los_ciclos = listarTodosLosCiclos();

$lista_de_errores = $_SESSION['errores'] ?? [];
if (!is_array($lista_de_errores)) {
    $lista_de_errores = [];
}

unset($_SESSION['datos_estudiante'], $_SESSION['errores']);

$titulo_pagina = "Modificar Estudiante - Admin";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Estudiante: <?= $estudiante['nombreEstudiante'] ?></h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $id_del_estudiante ?>">
        
        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="nombreEstudiante">Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= $estudiante['nombreEstudiante'] ?>">
                <?php if (isset($lista_de_errores['nombreEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="emailEstudiante">Email *</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= $estudiante['emailEstudiante'] ?>">
                <?php if (isset($lista_de_errores['emailEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['emailEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idCiclo">Ciclo Formativo *</label>
                <select name="idCiclo" id="idCiclo">
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?php if ($ciclo['idCiclo'] == $estudiante['idCiclo']) { echo "selected"; } ?>>
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['idCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="dniEstudiante">DNI *</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= $estudiante['dniEstudiante'] ?>">
                <?php if (isset($lista_de_errores['dniEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['dniEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="telefonoEstudiante">Teléfono *</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= $estudiante['telefonoEstudiante'] ?>">
                <?php if (isset($lista_de_errores['telefonoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['telefonoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= $estudiante['fechaNacimientoEstudiante'] ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= $estudiante['direccionEstudiante'] ?>">
            </div>
            
            <div class="campo-formulario">
                <label for="ciudadEstudiante">Ciudad</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= $estudiante['ciudadEstudiante'] ?>">
            </div>

            <div class="campo-formulario">
                <label for="codigoPostalEstudiante">Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= $estudiante['codigoPostalEstudiante'] ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="observacionesEstudiante">Observaciones</label>
                <textarea name="observacionesEstudiante" id="observacionesEstudiante"><?= $estudiante['observacionesEstudiante'] ?></textarea>
            </div>
            
            <input type="hidden" name="fechaAltaEstudiante" value="<?= $estudiante['fechaAltaEstudiante'] ?>">
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarEstudiante" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>




