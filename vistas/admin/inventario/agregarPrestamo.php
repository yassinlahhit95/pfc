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

$articulos_disponibles = listarArticulos();
$todos_los_ciclos = listarTodosLosCiclos();

$idCicloFiltro = (int)($_GET['idCiclo'] ?? 0);

if (!empty($idCicloFiltro)) {
    $todos_los_estudiantes = listarEstudiantesPorCiclo($idCicloFiltro);
} else {
    $todos_los_estudiantes = listarEstudiantes();
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
        <div class="row">
            <div class="campo">
                <label>Filtrar Estudiantes por Ciclo:</label>
                <select id="filtroCicloEstudiante" name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los ciclos --</option>
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloFiltro == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="campo">
                <label>Filtrar Estudiantes por Curso:</label>
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
                <label>Recurso (Solo disponibles)</label>
                <select name="idArticulo">
                    <option value="">-- Seleccione un equipo --</option>
                    <?php foreach ($articulos_disponibles as $art) { ?>
                        <?php if ($art['estado'] == 'disponible') { ?>
                            <option value="<?= (int)$art['idArticulo'] ?>" <?= (isset($datos['idArticulo']) && $datos['idArticulo'] == $art['idArticulo']) ? 'selected' : '' ?>>
                                <?= Security::escapeHtml($art['nombreArticulo']) ?> (<?= Security::escapeHtml($art['numeroSerie']) ?>)
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <?= fieldError($errores, 'idArticulo') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'idEstudiante') ?>">
                <label>Estudiante</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php foreach ($todos_los_estudiantes as $est) { ?>
                        <option value="<?= (int)$est['idEstudiante'] ?>" data-curso="<?= Security::escapeHtml($est['anioEstudio'] ?? '') ?>" <?= (isset($datos['idEstudiante']) && $datos['idEstudiante'] == $est['idEstudiante']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($est['nombreEstudiante']) ?><?= !empty($est['anioEstudio']) ? ' (' . Security::escapeHtml($est['anioEstudio']) . ')' : '' ?>
                        </option>
                    <?php } ?>
                </select>
                <?= fieldError($errores, 'idEstudiante') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaPrestamo') ?>">
                <label>Fecha de Préstamo</label>
                <input type="date" name="fechaPrestamo" value="<?= Security::escapeHtml($datos['fechaPrestamo'] ?? '') ?>">
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
