<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/inventario.php";

$todosLosArticulos = listarArticulos();

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
                <?php if (empty($todosLosArticulos)) { ?>
                    <tr><td colspan="4" class="vacio">No hay artículos en el inventario</td></tr>
                <?php } else { ?>
                    <?php foreach ($todosLosArticulos as $articulo) { ?>
                    <tr>
                        <td><b><?= Security::escapeHtml($articulo['nombreArticulo']) ?></b></td>
                        <td><?= Security::escapeHtml($articulo['numeroSerie'] ?? '') ?></td>
                        <td>
                            <?php
                            $claseEstado = "activo-verde";
                            if ($articulo['estado'] != 'disponible') { $claseEstado = "inactivo-rojo"; }
                            ?>
                            <span class="indicador-estado <?= $claseEstado ?>">
                                <?= Security::escapeHtml($articulo['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="modificarArticulo.php?idArticulo=<?= (int)$articulo['idArticulo'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$articulo['idArticulo'] ?>"
                                       data-tipo="Artículo"
                                       data-nombre="<?= Security::escapeHtml($articulo['nombreArticulo']) ?>"
                                       data-url="/controladores/secretaria/inventario/borrar.php"
                                       data-campo="idArticulo"><i class="fas fa-trash"></i> Eliminar</a>
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
<script>
iniciarPaginacion('tablaInventario', 15);
</script>
