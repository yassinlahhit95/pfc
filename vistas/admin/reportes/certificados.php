<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeEstudiantes = listarEstudiantes();
$listaDeCiclos = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);

$titulo_pagina = "AULAPRO | CERTIFICADOS";
$seccion = 'reportes_cert';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>GENERAR CERTIFICADO ACADÉMICO</h1>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Datos del Certificado</h3>
    </div>

    <form action="../../../controladores/admin/reportes/generarCertificado.php" method="POST" class="form-estandar">

        <div class="campo-formulario">
            <label>Nivel Formativo:</label>
            <select id="filtroNivel" onchange="alCambiarNivel()">
                <option value="">-- Selecciona un nivel --</option>
                <?php foreach ($listaNiveles as $nivel) { ?>
                    <option value="<?= $nivel['idNivel'] ?>">
                        <?= $nivel['nombreNivel'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario">
            <label>Ciclo Formativo:</label>
            <select id="filtroCiclo" onchange="alCambiarCiclo()" disabled>
                <option value="">-- Primero selecciona un nivel --</option>
                <?php foreach ($listaDeCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" data-nivel="<?= $ciclo['idNivel'] ?>">
                        <?= $ciclo['nombreCiclo'] ?> (<?= $ciclo['abreviaturaCiclo'] ?>)
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario">
            <label>Estudiante:</label>
            <select name="idEstudiante" id="selectEstudiante" disabled>
                <option value="">-- Primero selecciona un ciclo --</option>
                <?php foreach ($listaDeEstudiantes as $estudiante) { ?>
                    <option value="<?= $estudiante['idEstudiante'] ?>" data-ciclo="<?= $estudiante['idCiclo'] ?>">
                        <?= $estudiante['nombreEstudiante'] ?> &mdash; <?= $estudiante['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($errores['idEstudiante'])) { ?>
                <small class="texto-rojo"><?= $errores['idEstudiante'] ?></small>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Horario:</label>
            <input type="text" name="horario" placeholder="Ej: Mañana (08:00 - 14:30)">
            <?php if (isset($errores['horario'])) { ?>
                <small class="texto-rojo"><?= $errores['horario'] ?></small>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Año Académico:</label>
            <input type="text" name="anioAcademico" value="2025/2026">
            <?php if (isset($errores['anioAcademico'])) { ?>
                <small class="texto-rojo"><?= $errores['anioAcademico'] ?></small>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Ciudad:</label>
            <input type="text" name="ciudad" value="Bilbao">
            <?php if (isset($errores['ciudad'])) { ?>
                <small class="texto-rojo"><?= $errores['ciudad'] ?></small>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="generarCertificado" class="boton-primario">
                <i class="fas fa-file-alt"></i> GENERAR CERTIFICADO PDF
            </button>
        </div>

    </form>
</div>

<script>
function alCambiarNivel() {
    var idNivel = document.getElementById('filtroNivel').value;
    var selectCiclo = document.getElementById('filtroCiclo');
    var selectEstudiante = document.getElementById('selectEstudiante');

    selectEstudiante.value = '';
    selectEstudiante.disabled = true;
    selectEstudiante.options[0].textContent = '-- Primero selecciona un ciclo --';
    var opcionesEst = selectEstudiante.querySelectorAll('option');
    opcionesEst.forEach(function(opcion) { opcion.style.display = ''; });

    if (idNivel === '') {
        selectCiclo.value = '';
        selectCiclo.disabled = true;
        selectCiclo.options[0].textContent = '-- Primero selecciona un nivel --';
        var opcionesCiclo = selectCiclo.querySelectorAll('option');
        opcionesCiclo.forEach(function(opcion) { opcion.style.display = ''; });
        return;
    }

    var opcionesCiclo = selectCiclo.querySelectorAll('option');
    opcionesCiclo.forEach(function(opcion) {
        if (opcion.value === '') {
            opcion.style.display = '';
            return;
        }
        if (opcion.getAttribute('data-nivel') === idNivel) {
            opcion.style.display = '';
        } else {
            opcion.style.display = 'none';
        }
    });

    selectCiclo.value = '';
    selectCiclo.options[0].textContent = '-- Selecciona un ciclo --';
    selectCiclo.disabled = false;
}

function alCambiarCiclo() {
    var idCiclo = document.getElementById('filtroCiclo').value;
    var selectEstudiante = document.getElementById('selectEstudiante');

    selectEstudiante.value = '';

    if (idCiclo === '') {
        selectEstudiante.disabled = true;
        selectEstudiante.options[0].textContent = '-- Primero selecciona un ciclo --';
        var opciones = selectEstudiante.querySelectorAll('option');
        opciones.forEach(function(opcion) { opcion.style.display = ''; });
        return;
    }

    selectEstudiante.disabled = false;
    selectEstudiante.options[0].textContent = '-- Selecciona un estudiante --';

    var opciones = selectEstudiante.querySelectorAll('option');
    opciones.forEach(function(opcion) {
        if (opcion.value === '') {
            opcion.style.display = '';
            return;
        }
        if (opcion.getAttribute('data-ciclo') === idCiclo) {
            opcion.style.display = '';
        } else {
            opcion.style.display = 'none';
        }
    });
}
</script>

<?php include '../comunes/footer.php'; ?>
