<?php
session_start();
$titulo_pagina = "Gestión de Préstamos - Super Admin";
$seccion = 'prestamos';
include_once "../comunes/nav.php";

require_once "../../../modelos/inventario.php";

$todos_los_prestamos = listarTodosLosPrestamos();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Préstamos de Material</h1>
    <a href="/pfc/vistas/admin/inventario/agregarPrestamo.php" class="boton-primario">
        <i class="fas fa-plus"></i> Nuevo Préstamo
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Material</th>
                    <th>Fecha Préstamo</th>
                    <th>Fecha Devolución</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_prestamos)) { ?>
                    <tr><td colspan="6" class="sin-datos">No hay registros de préstamos</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_prestamos as $p) { ?>
                    <tr>
                        <td><strong><?php echo $p['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $p['nombreArticulo']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($p['fechaPrestamo'])); ?></td>
                        <td>
                            <?php 
                            if ($p['fechaDevolucion'] != "") {
                                echo date('d/m/Y', strtotime($p['fechaDevolucion']));
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            $clase_estado = "inactivo-rojo";
                            if ($p['estadoPrestamo'] == 'en curso') { $clase_estado = "activo-verde"; }
                            ?>
                            <span class="estado-bolita <?php echo $clase_estado; ?>">
                                <?php echo $p['estadoPrestamo']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <?php if ($p['estadoPrestamo'] == 'en curso') { ?>
                                    <form action="/pfc/controladores/admin/inventario/devolver.php" method="POST" class="d-inline">
                                        <input type="hidden" name="idPrestamo" value="<?php echo $p['idPrestamo']; ?>">
                                        <input type="hidden" name="idArticulo" value="<?php echo $p['idArticulo']; ?>">
                                        <button type="submit" class="boton-primario boton-pequeno">
                                            <i class="fas fa-undo"></i> Devolver
                                        </button>
                                    </form>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
