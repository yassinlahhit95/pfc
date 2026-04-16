<?php
session_start();
$titulo_pagina = "Gestión de Inventario - Super Admin";
$seccion = 'inventario';
include_once "../comunes/nav.php";

require_once "../../modelos/inventario.php";

$modeloInventario = new inventario();
$listaArticulos = $modeloInventario->listarArticulosModelo();

// Capturar errores y datos viejos de la sesión
$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_inventario']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Inventario de Recursos</h1>
        <p class="texto-atenuado">Control de materiales del centro</p>
    </div>
    <div>
        <a href="vistas/inventario/gestionarPrestamos.php" class="boton-primario">
            <i class="fas fa-hand-holding"></i> Gestionar Préstamos
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><i class="fas fa-times-circle"></i> <?php echo $error; ?></div>
<?php } ?>

<div class="disposicion-flexible separacion-grande">
    <!-- Formulario con Errores Dinámicos -->
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta">
            <h3><i class="fas fa-plus"></i> Nuevo Artículo</h3>
        </div>
        <form method="POST" action="../../controladores/inventario/insertar.php">
            
            <!-- Campo: Nombre -->
            <div class="campo-formulario margen-abajo">
                <label>Nombre del Recurso</label>
                <input type="text" name="nombreArticulo" 
                       value="<?php echo htmlspecialchars($datos['nombreArticulo'] ?? ''); ?>" 
                       placeholder="Ej: Proyector">
                <?php if (isset($errores['nombreArticulo'])) { ?>
                    <p style="color: red;"><?php echo $errores['nombreArticulo']; ?></p>
                <?php } ?>
            </div>

            <!-- Campo: Nº Serie -->
            <div class="campo-formulario margen-abajo">
                <label>Número de Serie</label>
                <input type="text" name="numeroSerie" 
                       value="<?php echo htmlspecialchars($datos['numeroSerie'] ?? ''); ?>"
                       placeholder="Ej: SN12345">
                <?php if (isset($errores['numeroSerie'])) { ?>
                    <p style="color: red;"><?php echo $errores['numeroSerie']; ?></p>
                <?php } ?>
            </div>

            <button type="submit" name="guardarArticulo" class="boton-primario ancho-total">Guardar en Inventario</button>
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
                            <td><strong><?php echo htmlspecialchars($art['nombreArticulo']); ?></strong></td>
                            <td><?php echo htmlspecialchars($art['numeroSerie'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="estado-bolita <?php echo ($art['estado'] == 'disponible') ? 'activo-verde' : 'inactivo-rojo'; ?>">
                                    <?php echo ucfirst($art['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="../../controladores/inventario/borrar.php?id=<?php echo $art['idArticulo']; ?>" 
                                   class="boton-icono boton-eliminar" 
                                   onclick="return confirm('¿Eliminar este dispositivo?');">
                                    <i class="fas fa-trash"></i>
                                </a>
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
