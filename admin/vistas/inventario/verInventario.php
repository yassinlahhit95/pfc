<?php
session_start();
$titulo_pagina = "Gestión de Inventario - Super Admin";
$seccion = 'inventario';
include_once "../comunes/nav.php";

require_once "../../modelos/inventario.php";

$listaArticulos = listarArticulos();
$listaPrestamosActivos = listarPrestamosActivos();

// Capturar errores y datos viejos de la sesión
$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_inventario']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Inventario de Recursos</h1>
        <p class="subtitulo-encabezado">Control de materiales y préstamos del centro</p>
    </div>
    <div>
        <a href="vistas/inventario/gestionarPrestamos.php" class="boton-primario">
            <i class="fas fa-hand-holding"></i> Ir a Préstamos
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><i class="fas fa-times-circle"></i> <?php echo $error; ?></div>
<?php } ?>

<!-- 1. SECCIÓN: EQUIPOS PRESTADOS (ACTIVOS) -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-clock color-warning mr-10"></i> Equipos Prestados Actualmente</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Alumno</th>
                    <th>Fecha Préstamo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaPrestamosActivos)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay préstamos activos en este momento</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaPrestamosActivos as $p) { ?>
                    <tr>
                        <td><strong><?php echo $p['nombreArticulo']; ?></strong></td>
                        <td><?php echo $p['nombreEstudiante']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($p['fechaPrestamo'])); ?></td>
                        <td>
                            <form action="controladores/inventario/devolver.php" method="POST" class="d-inline">
                                <input type="hidden" name="idPrestamo" value="<?php echo $p['idPrestamo']; ?>">
                                <input type="hidden" name="redireccion" value="../../vistas/inventario/verInventario.php">
                                <button type="submit" class="boton-secundario boton-pequeno">Devolver</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 2. SECCIÓN: FORMULARIO NUEVO ARTÍCULO (FILA) -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-plus"></i> Nuevo Artículo al Inventario</h3>
    </div>
    <form method="POST" action="controladores/inventario/insertar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Nombre del Recurso</label>
            <input type="text" name="nombreArticulo" value="<?php echo $datos['nombreArticulo'] ?? ''; ?>" placeholder="Ej: Proyector">
            <?php if (isset($errores['nombreArticulo'])) { ?>
                <p class="error-campo"><?php echo $errores['nombreArticulo']; ?></p>
            <?php } ?>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>Número de Serie</label>
            <input type="text" name="numeroSerie" value="<?php echo $datos['numeroSerie'] ?? ''; ?>" placeholder="Ej: SN12345">
            <?php if (isset($errores['numeroSerie'])) { ?>
                <p class="error-campo"><?php echo $errores['numeroSerie']; ?></p>
            <?php } ?>
        </div>

        <div class="mt-25">
            <button type="submit" name="guardarArticulo" class="boton-primario">
                <i class="fas fa-save"></i> Registrar
            </button>
        </div>
    </form>
</div>

<!-- 3. SECCIÓN: EQUIPAMIENTO TOTAL -->
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
                        <td><?php echo $art['numeroSerie'] ?? 'N/A'; ?></td>
                        <td>
                            <span class="estado-bolita <?php echo ($art['estado'] == 'disponible') ? 'activo-verde' : 'inactivo-rojo'; ?>">
                                <?php echo ucfirst($art['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <form action="controladores/inventario/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este dispositivo?');">
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
