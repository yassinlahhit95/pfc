<?php
session_start();
$titulo_pagina = "Gestionar Préstamos - Super Admin";
$seccion = 'prestamos';
include_once "../comunes/nav.php";

require_once "../../modelos/inventario.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/ciclos.php";

$listaArticulos = listarArticulos();
$listaEstudiantes = listarEstudiantes();
$listaCiclos = listarTodosLosCiclos();

// Capturar errores y datos de sesión
$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_inventario']);

// Listas para las tablas
$listaPrestamosActivos = listarPrestamosActivos();
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Gestión de Préstamos</h1>
    </div>
    <div>
        <a href="vistas/inventario/verInventario.php" class="boton-secundario">Volver</a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><p><?php echo $exito; ?></p></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><p><?php echo $error; ?></p></div>
<?php } ?>

<div class="disposicion-flexible separacion-grande">
    <!-- Formulario de Préstamo -->
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta"><h3>Nuevo Préstamo</h3></div>
        <form method="POST" action="controladores/inventario/prestar.php">
            
            <div class="campo-formulario margen-abajo">
                <label>Recurso</label>
                <select name="idArticulo">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($listaArticulos as $art) { 
                        if ($art['cantidadDisponible'] > 0) {
                            $selected = ($datos['idArticulo'] ?? '') == $art['idArticulo'] ? 'selected' : '';
                            echo "<option value='{$art['idArticulo']}' {$selected}>{$art['nombreArticulo']}</option>";
                        }
                    } ?>
                </select>
                <?php if (isset($errores['idArticulo'])) echo "<p class='error-campo'>{$errores['idArticulo']}</p>"; ?>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Ciclo (Filtro)</label>
                <select onchange="filtrarAlumnosPorCiclo(this.value)">
                    <option value="todos">Todos los ciclos</option>
                    <?php foreach ($listaCiclos as $c) echo "<option value='{$c['idCiclo']}'>{$c['nombreCiclo']}</option>"; ?>
                </select>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Estudiante</label>
                <select name="idEstudiante" id="selectorEstudiante">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($listaEstudiantes as $est) {
                        $selected = ($datos['idEstudiante'] ?? '') == $est['idEstudiante'] ? 'selected' : '';
                        echo "<option value='{$est['idEstudiante']}' data-ciclo='{$est['idCiclo']}' {$selected}>{$est['nombreEstudiante']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])) echo "<p class='error-campo'>{$errores['idEstudiante']}</p>"; ?>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Fecha Préstamo</label>
                <input type="date" name="fechaPrestamo" 
                       value="<?php echo $datos['fechaPrestamo'] ?? date('Y-m-d'); ?>">
                <?php if (isset($errores['fechaPrestamo'])) echo "<p class='error-campo'>{$errores['fechaPrestamo']}</p>"; ?>
            </div>

            <button type="submit" name="registrarPrestamo" class="boton-primario ancho-total">Registrar Préstamo</button>
        </form>
    </div>

    <!-- Tablas -->
    <div class="flexible-rellenar">
        <div class="tarjeta-blanca">
            <div class="titulo-tarjeta"><h3>Equipos Prestados</h3></div>
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead><tr><th>Equipo</th><th>Alumno</th><th>Fecha</th><th>Acción</th></tr></thead>
                    <tbody>
                        <?php if (empty($listaPrestamosActivos)) { ?>
                            <tr><td colspan="4" class="sin-datos">No hay préstamos activos</td></tr>
                        <?php } else { ?>
                            <?php foreach ($listaPrestamosActivos as $p) { ?>
                            <tr>
                                <td><strong><?php echo $p['nombreArticulo']; ?></strong></td>
                                <td><?php echo $p['nombreEstudiante']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($p['fechaPrestamo'])); ?></td>
                                <td>
                                    <form action="controladores/inventario/devolver.php" method="POST" class="d-inline">
                                        <input type="hidden" name="idPrestamo" value="<?php echo $p['idPrestamo']; ?>">
                                        <input type="hidden" name="redireccion" value="../../vistas/inventario/gestionarPrestamos.php">
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
    </div>
</div>

<script>
function filtrarAlumnosPorCiclo(cicloId) {
    var opciones = document.getElementById('selectorEstudiante').options;
    for (var i = 1; i < opciones.length; i++) {
        var ciclo = opciones[i].getAttribute('data-ciclo');
        opciones[i].style.display = (cicloId === "todos" || cicloId === ciclo) ? "block" : "none";
    }
}
</script>

<?php include '../comunes/footer.php'; ?>
