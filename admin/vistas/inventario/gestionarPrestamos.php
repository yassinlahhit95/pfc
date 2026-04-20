<?php
session_start();
$titulo_pagina = "Gestión de Préstamos - Super Admin";
$seccion = 'prestamos';
include_once "../comunes/nav.php";

require_once "../../../modelos/inventario.php";

// Capturar mensajes de éxito o error
$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);

// Listas para las tablas
$listaPrestamosActivos = listarPrestamosActivos();
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Gestión de Préstamos</h1>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/inventario/agregarPrestamo.php" class="boton-primario">
            <i class="fas fa-plus"></i> Nuevo Préstamo
        </a>
        <a href="vistas/inventario/verInventario.php" class="boton-secundario">
            <i class="fas fa-box"></i> Ver Inventario
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><p><?php echo $exito; ?></p></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><p><?php echo $error; ?></p></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta"><h3>Equipos Prestados Actualmente</h3></div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Estudiante</th>
                    <th>Fecha Préstamo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaPrestamosActivos)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay préstamos activos en este momento</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaPrestamosActivos as $p) { ?>
                    <tr>
                        <td><strong><?php echo $p['nombreArticulo']; ?></strong></td>
                        <td><?php echo $p['nombreEstudiante']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($p['fechaPrestamo'])); ?></td>
                        <td>
                            <form action="controladores/inventario/devolver.php" method="POST" class="d-inline">
                                <input type="hidden" name="idPrestamo" value="<?php echo $p['idPrestamo']; ?>">
                                <input type="hidden" name="redireccion" value="../../vistas/inventario/gestionarPrestamos.php">
                                <button type="submit" class="boton-secundario boton-pequeno">
                                    <i class="fas fa-undo"></i> Devolver
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>