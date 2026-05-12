<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeCiclos = listarTodosLosCiclos();
$listaNiveles = listarNiveles();
$listaDeEstudiantes = listarEstudiantes();

$estudiantesPorCiclo = [];
foreach ($listaDeEstudiantes as $est) {
    $idCiclo = $est['idCiclo'];
    if (!isset($estudiantesPorCiclo[$idCiclo])) {
        $estudiantesPorCiclo[$idCiclo] = [];
    }
    $estudiantesPorCiclo[$idCiclo][] = ['id' => $est['idEstudiante'], 'nombre' => $est['nombreEstudiante']];
}

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);

$titulo_pagina = "AULAPRO | BOLETINES DE NOTAS";
$seccion = 'reportes_boletin';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>GENERAR BOLETINES DE NOTAS</h1>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Selecciona los Filtros</h3>
    </div>

    <form action="../../../controladores/admin/reportes/generarBoletin.php" method="POST" class="form-estandar">

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
            <select name="idCiclo" id="selectCiclo" onchange="alCambiarCiclo()" disabled>
                <option value="">-- Primero selecciona un nivel --</option>
                <?php foreach ($listaDeCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" data-nivel="<?= $ciclo['idNivel'] ?>">
                        <?= $ciclo['nombreCiclo'] ?> (<?= $ciclo['abreviaturaCiclo'] ?>)
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($errores['idCiclo'])) { ?>
                <small class="texto-rojo"><?= $errores['idCiclo'] ?></small>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Estudiante:</label>
            <select name="idEstudiante" id="selectEstudiante" disabled>
                <option value="">-- Primero selecciona un ciclo --</option>
            </select>
            <?php if (isset($errores['idEstudiante'])) { ?>
                <small class="texto-rojo"><?= $errores['idEstudiante'] ?></small>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label>Año Académico:</label>
            <input type="text" name="anioAcademico" value="2025/2026">
            <?php if (isset($errores['anioAcademico'])) { ?>
                <small class="texto-rojo"><?= $errores['anioAcademico'] ?></small>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="generarBoletin" class="boton-primario">
                <i class="fas fa-scroll"></i> GENERAR BOLETÍN PDF
            </button>
        </div>

    </form>
</div>

<script>
var estudiantesPorCiclo = <?= json_encode($estudiantesPorCiclo) ?>;

function alCambiarNivel() {
    var idNivel = document.getElementById('filtroNivel').value;
    var selectCiclo = document.getElementById('selectCiclo');
    var selectEstudiante = document.getElementById('selectEstudiante');

    selectEstudiante.innerHTML = '<option value="">-- Primero selecciona un ciclo --</option>';
    selectEstudiante.disabled = true;

    if (idNivel === '') {
        selectCiclo.value = '';
        selectCiclo.disabled = true;
        selectCiclo.options[0].textContent = '-- Primero selecciona un nivel --';
        var opciones = selectCiclo.querySelectorAll('option');
        opciones.forEach(function(opcion) { opcion.style.display = ''; });
        return;
    }

    var opciones = selectCiclo.querySelectorAll('option');
    opciones.forEach(function(opcion) {
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
    var idCiclo = document.getElementById('selectCiclo').value;
    var selectEstudiante = document.getElementById('selectEstudiante');

    selectEstudiante.innerHTML = '<option value="">-- Selecciona un estudiante --</option>';
    selectEstudiante.disabled = true;

    if (idCiclo === '') {
        return;
    }

    selectEstudiante.disabled = false;

    if (estudiantesPorCiclo[idCiclo]) {
        estudiantesPorCiclo[idCiclo].forEach(function(est) {
            var opcion = document.createElement('option');
            opcion.value = est.id;
            opcion.textContent = est.nombre;
            selectEstudiante.appendChild(opcion);
        });
    }
}
</script>

<?php include '../comunes/footer.php'; ?>
