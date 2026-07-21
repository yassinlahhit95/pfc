<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_inventario');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/inventario.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$articulosDisponibles = listarArticulos();
$todosLosCiclos = listarTodosLosCiclos();

$idCicloFiltro = (int)($_GET['idCiclo'] ?? 0);

if (!empty($idCicloFiltro)) {
    $todosLosEstudiantes = listarEstudiantesPorCiclo($idCicloFiltro);
} else {
    $todosLosEstudiantes = listarEstudiantes();
}

$datos = $_SESSION['datos_prestamo'] ?? [];
unset($_SESSION['datos_prestamo']);

$titulo_pagina = "AULAPRO | NUEVO PRÉSTAMO";
$seccion = 'prestamos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>REGISTRAR NUEVO PRÉSTAMO</h1>
    <a href="gestionarPrestamos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form method="GET" action="agregarPrestamo.php" class="margen-abajo">
        <div class="form-fila">
            <div class="campo">
                <label for="filtroCicloEstudiante">Filtrar Estudiantes por Ciclo:</label>
                <select id="filtroCicloEstudiante" name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los ciclos --</option>
                    <?php foreach ($todosLosCiclos as $ciclo) { ?>
                        <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloFiltro == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="campo">
                <label for="filtroCursoEstudiante">Filtrar Estudiantes por Curso:</label>
                <select id="filtroCursoEstudiante" onchange="filtrarEstudiantesPrestamo()">
                    <option value="">-- Todos los cursos --</option>
                    <option value="1º">1º Año</option>
                    <option value="2º">2º Año</option>
                </select>
            </div>
        </div>
    </form>

    <form method="POST" action="../../../controladores/admin/inventario/prestar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="form-cols">

            <div class="campo<?= fieldClass($errores, 'idArticulo') ?>">
                <label for="idArticulo">Recurso (Solo disponibles)</label>
                <select name="idArticulo" id="idArticulo">
                    <option value="">-- Seleccione un equipo --</option>
                    <?php foreach ($articulosDisponibles as $articulo) { ?>
                        <?php if ($articulo['estado'] == 'disponible') { ?>
                            <option value="<?= (int)$articulo['idArticulo'] ?>" <?= (isset($datos['idArticulo']) && $datos['idArticulo'] == $articulo['idArticulo']) ? 'selected' : '' ?>>
                                <?= Security::escapeHtml($articulo['nombreArticulo']) ?> (<?= Security::escapeHtml($articulo['numeroSerie']) ?>)
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <?= fieldError($errores, 'idArticulo') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'idEstudiante') ?>">
                <label for="idEstudiante">Estudiante</label>
                <select name="idEstudiante" id="idEstudiante">
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php foreach ($todosLosEstudiantes as $estudiante) { ?>
                        <option value="<?= (int)$estudiante['idEstudiante'] ?>" data-curso="<?= Security::escapeHtml($estudiante['anioEstudio'] ?? '') ?>" <?= (isset($datos['idEstudiante']) && $datos['idEstudiante'] == $estudiante['idEstudiante']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?><?= !empty($estudiante['anioEstudio']) ? ' (' . Security::escapeHtml($estudiante['anioEstudio']) . ')' : '' ?>
                        </option>
                    <?php } ?>
                </select>
                <?= fieldError($errores, 'idEstudiante') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaPrestamo') ?>">
                <label for="fechaPrestamo">Fecha de Préstamo</label>
                <input type="date" name="fechaPrestamo" id="fechaPrestamo" value="<?= Security::escapeHtml($datos['fechaPrestamo'] ?? '') ?>">
                <?= fieldError($errores, 'fechaPrestamo') ?>
            </div>

        </div>

        <div class="acciones">
            <input type="submit" name="registrarPrestamo" class="boton-primario" value="Registrar Préstamo">
            <input type="reset" class="boton-secundario" value="Limpiar">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
function filtrarEstudiantesPrestamo() {
    var curso = document.getElementById('filtroCursoEstudiante').value;
    var select = document.querySelector('select[name="idEstudiante"]');
    var options = select.options;
    var hasVisibleSelected = false;
    for (var i = 1; i < options.length; i++) {
        var opt = options[i];
        var optCurso = opt.getAttribute('data-curso');
        var show = curso === '' || optCurso === curso;
        opt.style.display = show ? '' : 'none';
        if (show && opt.selected) hasVisibleSelected = true;
    }
    if (select.value && !hasVisibleSelected) {
        select.value = '';
    }
}
</script>
