<?php
session_start();
$titulo_pagina = "AULAPRO | NUEVO PRÉSTAMO";
$seccion = 'prestamos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/inventario.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$articulos_disponibles = listarArticulos();
$todos_los_estudiantes = listarEstudiantes();
$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_prestamo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_prestamo']);
?>

<div class="encabezado-pagina">
    <h1>REGISTRAR NUEVO PRÉSTAMO</h1>
    <a href="gestionarPrestamos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/inventario/prestar.php">
        <div class="formulario-cuadricula">

            <div class="campo-formulario">
                <label>Recurso (Solo disponibles) *</label>
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
                <?php if (isset($errores['idArticulo'])) { ?>
                    <strong class="error-campo"><?= $errores['idArticulo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccione un estudiante --</option>
                    <?php foreach ($todos_los_estudiantes as $est) { ?>
                        <option value="<?= $est['idEstudiante'] ?>" <?= (isset($datos['idEstudiante']) && $datos['idEstudiante'] == $est['idEstudiante']) ? 'selected' : '' ?>>
                            <?= $est['nombreEstudiante'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['idEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Préstamo *</label>
                <input type="date" name="fechaPrestamo" value="<?= $datos['fechaPrestamo'] ?? '' ?>">
                <?php if (isset($errores['fechaPrestamo'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaPrestamo'] ?></strong>
                <?php } ?>
            </div>

        </div>

        <div class="margen-arriba disposicion-flexible separacion-media">
            <button type="submit" name="registrarPrestamo" class="boton-primario">
                <i class="fas fa-save"></i> Registrar Préstamo
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>




