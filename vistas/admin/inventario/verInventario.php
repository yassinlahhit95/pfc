<?php
session_start();
$titulo_pagina = "Inventario del Centro - Super Admin";
$seccion = 'inventario';
include_once "../comunes/nav.php";

require_once "../../../modelos/inventario.php";

$todos_los_articulos = listarArticulos();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = [];
if (isset($_SESSION['datos_inventario'])) { $datos = $_SESSION['datos_inventario']; }

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_inventario']);
?>

<div class="encabezado-pagina">
    <h1>Gestión de Inventario</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Añadir Nuevo Artículo</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/inventario/insertar.php">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Artículo *</label>
                <input type="text" name="nombreArticulo" value="<?php if(isset($datos['nombreArticulo'])) echo $datos['nombreArticulo']; ?>" placeholder="Ej: Portátil HP ProBook">
                <?php if (isset($lista_de_errores['nombreArticulo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreArticulo']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Número de Serie *</label>
                <input type="text" name="numeroSerie" value="<?php if(isset($datos['numeroSerie'])) echo $datos['numeroSerie']; ?>" placeholder="Ej: SN-12345678">
                <?php if (isset($lista_de_errores['numeroSerie'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['numeroSerie']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Estado Inicial *</label>
                <select name="estadoArticulo">
                    <option value="disponible" <?php if(isset($datos['estadoArticulo']) && $datos['estadoArticulo'] == 'disponible') echo "selected"; ?>>Disponible</option>
                    <option value="prestado" <?php if(isset($datos['estadoArticulo']) && $datos['estadoArticulo'] == 'prestado') echo "selected"; ?>>Prestado</option>
                    <option value="reparacion" <?php if(isset($datos['estadoArticulo']) && $datos['estadoArticulo'] == 'reparacion') echo "selected"; ?>>En Reparación</option>
                </select>
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
        <table class="tabla-datos">
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
                    <tr><td colspan="4" class="sin-datos">No hay artículos en el inventario</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_articulos as $art) { ?>
                    <tr>
                        <td><strong><?php echo $art['nombreArticulo']; ?></strong></td>
                        <td><?php echo $art['numeroSerie']; ?></td>
                        <td>
                            <?php 
                            $clase_estado = "activo-verde";
                            if ($art['estado'] != 'disponible') { $clase_estado = "inactivo-rojo"; }
                            ?>
                            <span class="estado-bolita <?php echo $clase_estado; ?>">
                                <?php echo $art['estado']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <form action="/pfc/controladores/admin/inventario/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este artículo del inventario?')">
                                    <input type="hidden" name="idArticulo" value="<?php echo $art['idArticulo']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar">
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
