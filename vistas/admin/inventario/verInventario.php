<?php
session_start();
$titulo_pagina = "Gestión de Inventario - Super Admin";
$seccion = 'inventario';
include_once "../comunes/nav.php";

require_once "../../../modelos/inventario.php";

$listaArticulos = listarArticulos();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_inventario'])) {
    $datos = $_SESSION['datos_inventario'];
}
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_inventario']);
?>

<div class="encabezado-pagina">
    <h1>Inventario de Recursos</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><p><?php echo $exito; ?></p></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><p><?php echo $error; ?></p></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Nuevo Artículo al Inventario</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/inventario/insertar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Nombre del Recurso</label>
            <?php 
            $nombreArticulo = '';
            if (isset($datos['nombreArticulo'])) {
                $nombreArticulo = $datos['nombreArticulo'];
            }
            ?>
            <input type="text" name="nombreArticulo" value="<?php echo $nombreArticulo; ?>" placeholder="Proyector">
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>Número de Serie</label>
            <?php 
            $numeroSerie = '';
            if (isset($datos['numeroSerie'])) {
                $numeroSerie = $datos['numeroSerie'];
            }
            ?>
            <input type="text" name="numeroSerie" value="<?php echo $numeroSerie; ?>" placeholder="SN12345">
        </div>

        <div class="mt-25">
            <button type="submit" name="guardarArticulo" class="boton-primario">
                <i class="fas fa-save"></i> Registrar
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Todos los Recursos Registrados</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Artículo</th>
                    <th>Nº Serie</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaArticulos)) { ?>
                    <tr><td colspan="4" class="sin-datos">Inventario vacío</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaArticulos as $art) { ?>
                    <tr>
                        <td><strong><?php echo $art['nombreArticulo']; ?></strong></td>
                        <td><?php echo !empty($art['numeroSerie']) ? $art['numeroSerie'] : 'N/A'; ?></td>
                        <td>
                            <?php 
                            $claseBolita = 'inactivo-rojo';
                            if ($art['estado'] == 'disponible') {
                                $claseBolita = 'activo-verde';
                            }
                            ?>
                            <span class="estado-bolita <?php echo $claseBolita; ?>">
                                <?php echo ucfirst($art['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <form action="/pfc/controladores/admin/inventario/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este dispositivo?');">
                                <input type="hidden" name="idArticulo" value="<?php echo $art['idArticulo']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar">
                                    <i class="fas fa-trash"></i>
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