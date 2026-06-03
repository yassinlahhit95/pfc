<?php
session_start();

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/inventario.php";

$todos_los_articulos = listarArticulos();

$titulo_pagina = "AULAPRO | INVENTARIO DEL CENTRO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";

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

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaInventario">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Número Serie</th>
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
                            <span class="indicador-estado <?= $clase_estado ?>">
                                <?= $art['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="modificarArticulo.php?idArticulo=<?= $art['idArticulo'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarArticulo.php?id=<?= $art['idArticulo'] ?>" onclick="return confirm('¿Eliminar este artículo?')"><i class="fas fa-trash"></i> Eliminar</a>
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

<?php include '../comunes/footer.php'; ?>

