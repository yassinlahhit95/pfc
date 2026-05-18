<?php
session_start();
$titulo_pagina = "AULAPRO | INVENTARIO DEL CENTRO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/inventario.php";

$todos_los_articulos = listarArticulos();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="cabecera">
    <h1>GESTIÓN DE INVENTARIO</h1>
    <a href="agregarArticulo.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO ARTÍCULO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <div class="tcont">
        <table class="tabla-datos" id="tablaInventario">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Nº Serie</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_articulos)) { ?>
                    <tr><td colspan="4" class="vacio">No hay artículos en el inventario</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_articulos as $art) { ?>
                    <tr>
                        <td><b><?= $art['nombreArticulo'] ?></b></td>
                        <td><?= $art['numeroSerie'] ?></td>
                        <td>
                            <?php
                            $clase_estado = "activo-verde";
                            if ($art['estado'] != 'disponible') { $clase_estado = "inactivo-rojo"; }
                            ?>
                            <span class="bolita <?= $clase_estado ?>">
                                <?= $art['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarArticulo.php?idArticulo=<?= $art['idArticulo'] ?>" class="btn-accion btn-editar" title="Editar datos">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/inventario/borrar.php" method="POST" onsubmit="return confirm('¿Eliminar este artículo del inventario?')">
                                    <input type="hidden" name="idArticulo" value="<?= $art['idArticulo'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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




