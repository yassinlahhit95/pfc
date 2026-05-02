<?php
session_start();
$titulo_pagina = "Inventario del Centro - Super Admin";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/inventario.php";

$todos_los_articulos = listarArticulos();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_inventario']);
?>

<div class="encabezado-pagina">
    <h1>Gestión de Inventario</h1>
</div>

<?php if ($exito) : ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>

<?php if ($error) : ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Añadir Nuevo Artículo</h3>
    </div>
    <form method="POST" action="../../../controladores/admin/inventario/insertar.php">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Artículo *</label>
                <input type="text" name="nombreArticulo" value="<?= $datos['nombreArticulo'] ?? '' ?>" placeholder="Ej: Portátil HP ProBook">
                <?php if (isset($lista_de_errores['nombreArticulo'])) : ?>
                    <p class="error-campo"><?= $lista_de_errores['nombreArticulo'] ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Número de Serie *</label>
                <input type="text" name="numeroSerie" value="<?= $datos['numeroSerie'] ?? '' ?>" placeholder="Ej: SN-12345678">
                <?php if (isset($lista_de_errores['numeroSerie'])) : ?>
                    <p class="error-campo"><?= $lista_de_errores['numeroSerie'] ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarArticulo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Artículo
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="contenedor-tabla">
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
                <?php if (empty($todos_los_articulos)) : ?>
                    <tr><td colspan="4" class="sin-datos">No hay artículos en el inventario</td></tr>
                <?php else : ?>
                    <?php foreach ($todos_los_articulos as $art) : ?>
                    <tr>
                        <td><strong><?= $art['nombreArticulo'] ?></strong></td>
                        <td><?= $art['numeroSerie'] ?></td>
                        <td>
                            <?php
                            $clase_estado = "activo-verde";
                            if ($art['estado'] != 'disponible') { $clase_estado = "inactivo-rojo"; }
                            ?>
                            <span class="estado-bolita <?= $clase_estado ?>">
                                <?= $art['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarArticulo.php?idArticulo=<?= $art['idArticulo'] ?>" class="btn-accion btn-editar" title="Editar datos">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/inventario/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este artículo del inventario?')">
                                    <input type="hidden" name="idArticulo" value="<?= $art['idArticulo'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
