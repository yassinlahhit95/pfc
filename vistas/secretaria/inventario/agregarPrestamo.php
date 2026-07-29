<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
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
    <!-- Alerta de préstamos activos -->
    <div id="alerta-prestamos-activos" style="display: none;"></div>

    <form method="GET" action="agregarPrestamo.php" class="margen-abajo">
        <div class="campo">
            <label>Filtrar Estudiantes por Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Todos los ciclos --</option>
                <?php foreach ($todosLosCiclos as $ciclo) { ?>
                    <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloFiltro == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>

    <form method="POST" action="../../../controladores/secretaria/inventario/prestar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="form-cols">

            <div class="campo<?= fieldClass($errores, 'idArticulo') ?>">
                <label>Recurso (Solo disponibles)</label>
                <select name="idArticulo">
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
                <label>Estudiante</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php foreach ($todosLosEstudiantes as $estudiante) { ?>
                        <option value="<?= (int)$estudiante['idEstudiante'] ?>" <?= (isset($datos['idEstudiante']) && $datos['idEstudiante'] == $estudiante['idEstudiante']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
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

<script src="../../../public/js/features/verificar-prestamos.js"></script>
<?php include __DIR__ . '/../comunes/footer.php'; ?>
