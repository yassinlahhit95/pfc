<?php
session_start();
$titulo_pagina = "AULAPRO | GESTIóN DE PRéSTAMOS";
$seccion = 'prestamos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/inventario.php";

$todos_los_prestamos = listarTodosLosPrestamos();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Préstamos de Material</h1>
    <a href="agregarPrestamo.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO PRÃ‰STAMO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
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
                        <td><strong><?= $p['nombreEstudiante'] ?></strong></td>
                        <td><?= $p['nombreArticulo'] ?></td>
                        <td><?= date('d/m/Y', strtotime($p['fechaPrestamo'])) ?></td>
                        <td>
                            <?php if (!empty($p['fechaDevolucion'])) { ?>
                                <?= date('d/m/Y', strtotime($p['fechaDevolucion'])) ?>
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                        <td>
                            <?php
                            $clase_estado = "inactivo-rojo";
                            if ($p['estadoPrestamo'] == 'en curso') { $clase_estado = "activo-verde"; }
                            ?>
                            <span class="estado-bolita <?= $clase_estado ?>">
                                <?= $p['estadoPrestamo'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <?php if ($p['estadoPrestamo'] == 'en curso') { ?>
                                    <form action="../../../controladores/admin/inventario/devolver.php" method="POST" class="d-inline">    
                                        <input type="hidden" name="idPrestamo" value="<?= $p['idPrestamo'] ?>">
                                        <input type="hidden" name="idArticulo" value="<?= $p['idArticulo'] ?>">
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




