<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/inventario.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$articulos_disponibles = listarArticulos();
$todos_los_ciclos = listarTodosLosCiclos();

$idCicloFiltro = $_GET['idCiclo'] ?? '';

if (!empty($idCicloFiltro)) {
    $todos_los_estudiantes = listarEstudiantesPorCiclo($idCicloFiltro);
} else {
    $todos_los_estudiantes = listarEstudiantes();
}

$datos = $_SESSION['datos_prestamo'] ?? [];

$titulo_pagina = "AULAPRO | NUEVO PRÉSTAMO";
$seccion = 'prestamos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>REGISTRAR NUEVO PRÉSTAMO</h1>
    <a href="gestionarPrestamos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div>
<?php } ?>

<div class="panel">
    <form method="GET" action="agregarPrestamo.php" class="margen-abajo">
        <div class="campo">
            <label>Filtrar Estudiantes por Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Todos los ciclos --</option>
                <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                    <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloFiltro == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>

    <form method="POST" action="../../../controladores/admin/inventario/prestar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="form-cols">

            <div class="campo">
                <label>Recurso (Solo disponibles)</label>
                <select name="idArticulo">
                    <option value="">-- Seleccione un equipo --</option>
                    <?php foreach ($articulos_disponibles as $art) { ?>
                        <?php if ($art['estado'] == 'disponible') { ?>
                            <option value="<?= $art['idArticulo'] ?>" <?= (isset($datos['idArticulo']) && $datos['idArticulo'] == $art['idArticulo']) ? 'selected' : '' ?>>
                                <?= $art['nombreArticulo'] ?> (<?= $art['numeroSerie'] ?>)
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
                
            </div>

            <div class="campo">
                <label>Estudiante</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php foreach ($todos_los_estudiantes as $est) { ?>
                        <option value="<?= $est['idEstudiante'] ?>" <?= (isset($datos['idEstudiante']) && $datos['idEstudiante'] == $est['idEstudiante']) ? 'selected' : '' ?>>
                            <?= $est['nombreEstudiante'] ?>
                        </option>
                    <?php } ?>
                </select>
                
            </div>

            <div class="campo">
                <label>Fecha de Préstamo</label>
                <input type="date" name="fechaPrestamo" value="<?= Security::escapeHtml($datos['fechaPrestamo'] ?? '') ?>">
                
            </div>

        </div>

        <div class="acciones">
            <input type="submit" name="registrarPrestamo" class="boton-primario" value="Registrar Préstamo">
            <input type="reset" class="boton-secundario" value="Limpiar">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
