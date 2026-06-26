<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');

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


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaPrestamos">
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
                        <td><b><?= Security::escapeHtml($p['nombreEstudiante']) ?></b></td>
                        <td><?= Security::escapeHtml($p['nombreArticulo']) ?></td>
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
                                <?= Security::escapeHtml($p['estadoPrestamo']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <?php if ($p['estadoPrestamo'] == 'en curso') { ?>
                                    <form action="../../../controladores/secretaria/inventario/devolver.php" method="POST" style="margin:0">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="idPrestamo" value="<?= $p['idPrestamo'] ?>">
                                        <input type="hidden" name="idArticulo" value="<?= $p['idArticulo'] ?>">
                                        <button type="submit" class="recurso-menu-item"><i class="fas fa-rotate-left"></i> Marcar devuelto</button>
                                    </form>
                                    <?php } else { ?>
                                    <span class="recurso-menu-item" style="opacity:.6;cursor:default;"><i class="fas fa-check"></i> Devuelto</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>iniciarPaginacion('tablaPrestamos', 15);</script>

