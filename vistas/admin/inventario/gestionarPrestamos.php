<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/inventario.php";

$todos_los_prestamos = listarTodosLosPrestamos();

$titulo_pagina = "AULAPRO | GESTIÓN DE PRÉSTAMOS";
$seccion = 'prestamos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>PRÉSTAMOS DE MATERIAL</h1>
    <a href="agregarPrestamo.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO PRÉSTAMO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
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
                    <tr><td colspan="6" class="vacio">No hay registros de préstamos</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_prestamos as $p) { ?>
                    <tr>
                        <td><b><?= $p['nombreEstudiante'] ?></b></td>
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
                            <span class="indicador-estado <?= $clase_estado ?>">
                                <?= $p['estadoPrestamo'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <?php if ($p['estadoPrestamo'] == 'en curso') { ?>
                                    <form action="../../../controladores/admin/inventario/devolver.php" method="POST">
                                        <input type="hidden" name="idPrestamo" value="<?= $p['idPrestamo'] ?>">
                                        <input type="hidden" name="idArticulo" value="<?= $p['idArticulo'] ?>">
                                        <input type="submit" class="boton-primario boton-pequeno" value="Devolver">
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

