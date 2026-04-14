<?php
session_start();
$titulo_pagina = "Gestión de Inventario - Super Admin";
$seccion = 'inventario';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/inventario.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$modeloInventario = new inventario($conexionBD);
$listaArticulos = $modeloInventario->listarArticulosModelo();

// Capturar errores y datos viejos de la sesión
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_viejos'] ?? [];
$mensajeExito = $_SESSION['exito'] ?? '';
unset($_SESSION['errores'], $_SESSION['datos_viejos'], $_SESSION['exito']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Inventario de Recursos</h1>
        <p class="texto-atenuado">Control de materiales del centro</p>
    </div>
    <div>
        <a href="vistas/inventario/gestionarPrestamos.php" class="boton-azul">
            <i class="fas fa-hand-holding"></i> Gestionar Préstamos
        </a>
    </div>
</div>

<?php if ($mensajeExito) { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $mensajeExito; ?></div>
<?php } ?>

<div class="disposicion-flexible separacion-grande">
    <!-- Formulario con Errores Dinámicos -->
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta">
            <h3><i class="fas fa-plus"></i> Nuevo Artículo</h3>
        </div>
        <form method="POST" action="controlador/inventarioControlador.php">
            <input type="hidden" name="accion" value="insertar">
            
            <!-- Campo: Nombre -->
            <div class="campo-formulario margen-abajo">
                <label>Nombre del Recurso</label>
                <input type="text" name="nombreArticulo" 
                       class="<?php echo isset($errores['nombreArticulo']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datos['nombreArticulo'] ?? ''); ?>" 
                       placeholder="Ej: Proyector">
                <?php if (isset($errores['nombreArticulo'])) { ?>
                    <p class="error-campo"><?php echo $errores['nombreArticulo']; ?></p>
                <?php } ?>
            </div>

            <!-- Campo: Cantidad -->
            <div class="campo-formulario margen-abajo">
                <label>Cantidad Inicial</label>
                <input type="text" name="cantidadTotal" 
                       class="<?php echo isset($errores['cantidadTotal']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datos['cantidadTotal'] ?? '1'); ?>">
                <?php if (isset($errores['cantidadTotal'])) { ?>
                    <p class="error-campo"><?php echo $errores['cantidadTotal']; ?></p>
                <?php } ?>
            </div>

            <button type="submit" class="boton-azul ancho-total">Guardar en Inventario</button>
        </form>
    </div>

    <!-- Tabla de Resultados -->
    <div class="tarjeta-blanca flexible-rellenar">
        <div class="titulo-tarjeta">
            <h3>Equipamiento Registrado</h3>
        </div>
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Artículo</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaArticulos)) { ?>
                        <tr><td colspan="3" class="sin-datos">Inventario vacío</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaArticulos as $art) { ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($art['nombreArticulo']); ?></strong></td>
                            <td>
                                <span class="estado-bolita <?php echo ($art['estado'] == 'disponible') ? 'activo-verde' : 'inactivo-rojo'; ?>">
                                    <?php echo ucfirst($art['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="controlador/inventarioControlador.php" class="d-inline">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="idArticulo" value="<?php echo $art['idArticulo']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar" onclick="return confirm('¿Eliminar?');">
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
</div>

<?php include '../comunes/footer.php'; ?>
