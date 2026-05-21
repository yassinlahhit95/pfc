<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$id_del_estudiante = $_GET['idEstudiante'] ?? '';
$estudiante = obtenerEstudiantePorId($id_del_estudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

$datos_sesion = $_SESSION['datos_estudiante'] ?? null;
if ($datos_sesion) {
    $estudiante = $datos_sesion + $estudiante;
}

$lista_ciclos = listarTodosLosCiclos();

$titulo_pagina = "AULAPRO | MODIFICAR ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MODIFICAR ESTUDIANTE: <?= $estudiante['nombreEstudiante'] ?></h1>
    </div>
    <a href="verEstudiantes.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $id_del_estudiante ?>">

        <div class="formulario">
            <div class="campo">
                <label>Nombre Completo</label>
                <input type="text" name="nombreEstudiante" value="<?= $estudiante['nombreEstudiante'] ?>">
                
            </div>

            <div class="campo">
                <label>Email</label>
                <input type="text" name="emailEstudiante" value="<?= $estudiante['emailEstudiante'] ?>">
                
            </div>

            <div class="campo">
                <label>Nivel</label>
                <select name="curso" id="curso" onchange="filtrarCiclos()">
                    <option value="Grado Medio" <?php if ($estudiante['curso'] == 'Grado Medio') { echo 'selected'; } ?>>Grado Medio</option>
                    <option value="Grado Superior" <?php if ($estudiante['curso'] == 'Grado Superior') { echo 'selected'; } ?>>Grado Superior</option>
                </select>
            </div>

            <div class="campo">
                <label>Ciclo Formativo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona un ciclo --</option>
                </select>
                
            </div>

            <div class="campo">
                <label>DNI</label>
                <input type="text" name="dniEstudiante" value="<?= $estudiante['dniEstudiante'] ?>">
                
            </div>

            <div class="campo">
                <label>Teléfono</label>
                <input type="text" name="telefonoEstudiante" value="<?= $estudiante['telefonoEstudiante'] ?>">
                
            </div>

            <div class="campo">
                <label>Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" value="<?= $estudiante['fechaNacimientoEstudiante'] ?>">
            </div>

            <div class="campo ancho-total">
                <label>Dirección</label>
                <input type="text" name="direccionEstudiante" value="<?= $estudiante['direccionEstudiante'] ?>">
            </div>

            <div class="campo">
                <label>Ciudad</label>
                <input type="text" name="ciudadEstudiante" value="<?= $estudiante['ciudadEstudiante'] ?>">
            </div>

            <div class="campo">
                <label>Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" value="<?= $estudiante['codigoPostalEstudiante'] ?>">
            </div>

            <div class="campo ancho-total">
                <label>Observaciones</label>
                <textarea name="observacionesEstudiante"><?= $estudiante['observacionesEstudiante'] ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarEstudiante" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
var listaDeCiclos = [
    <?php foreach ($lista_ciclos as $c) {
        echo '{id:' . $c['idCiclo'] . ', nivel:' . $c['idNivel'] . ', nombre:"' . addslashes($c['nombreCiclo']) . '"},';
    } ?>
];

function filtrarCiclos() {
    var nivelId = $('#curso').val() === 'Grado Medio' ? 1 : 2;
    var $select = $('#idCiclo').empty().append('<option value="">-- Selecciona un ciclo --</option>');

    $.each(listaDeCiclos, function(i, ciclo) {
        if (ciclo.nivel === nivelId) {
            $select.append($('<option>').val(ciclo.id).text(ciclo.nombre));
        }
    });
}

$(function() {
    filtrarCiclos();
    $('#idCiclo').val('<?= $estudiante['idCiclo'] ?>');
});
</script>

<?php include '../comunes/footer.php'; ?>
