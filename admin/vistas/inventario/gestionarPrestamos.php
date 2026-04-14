<?php
session_start();
$titulo_pagina = "Gestionar Préstamos - Super Admin";
$seccion = 'inventario';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/inventario.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/cursos.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();

$modeloInventario = new inventario($conexionBD);
$modeloEstudiante = new estudiante($conexionBD);
$modeloCurso = new curso($conexionBD);

$listaArticulos = $modeloInventario->listarArticulosModelo();
$listaEstudiantes = $modeloEstudiante->listarEstudiantesModelo();
$listaCursos = $modeloCurso->listarCursosModelo();

// Capturar errores y datos de sesión
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];
$mensajeExito = $_SESSION['exito'] ?? '';
unset($_SESSION['errores'], $_SESSION['datos_inventario'], $_SESSION['exito']);

// Listas para las tablas
$listaPrestamosActivos = $modeloInventario->listarPrestamosActivos();
$historialPrestamos = $modeloInventario->listarHistorialPrestamosModelo();
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Gestión de Préstamos</h1>
        <p class="texto-atenuado">Asignación de equipos con formato DD-MM-YYYY</p>
    </div>
    <div>
        <a href="vistas/inventario/verInventario.php" class="boton-gris">Volver</a>
    </div>
</div>

<?php if ($mensajeExito) { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $mensajeExito; ?></div>
<?php } ?>

<div class="disposicion-flexible separacion-grande">
    <!-- Formulario de Préstamo -->
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta"><h3>Nuevo Préstamo</h3></div>
        <form method="POST" action="controlador/inventarioControlador.php">
            <input type="hidden" name="accion" value="prestar">
            
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
                <?php if (isset($errores['idArticulo'])) echo "<p style='color: red;'>{$errores['idArticulo']}</p>"; ?>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Curso (Filtro)</label>
                <select onchange="filtrarAlumnosPorCurso(this.value)">
                    <option value="todos">Todos los cursos</option>
                    <?php foreach ($listaCursos as $c) echo "<option value='{$c['idCurso']}'>{$c['nombreCurso']}</option>"; ?>
                </select>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Estudiante</label>
                <select name="idEstudiante" id="selectorEstudiante">
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($listaEstudiantes as $est) {
                        $selected = ($datos['idEstudiante'] ?? '') == $est['idEstudiante'] ? 'selected' : '';
                        echo "<option value='{$est['idEstudiante']}' data-curso='{$est['idCurso']}' {$selected}>{$est['nombreEstudiante']}</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])) echo "<p style='color: red;'>{$errores['idEstudiante']}</p>"; ?>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Fecha Préstamo (DD-MM-YYYY)</label>
                <input type="text" name="fechaPrestamo" 
                       placeholder="Ej: 14-04-2026"
                       value="<?php echo htmlspecialchars($datos['fechaPrestamo'] ?? date('d-m-Y')); ?>">
                <?php if (isset($errores['fechaPrestamo'])) echo "<p style='color: red;'>{$errores['fechaPrestamo']}</p>"; ?>
            </div>

            <button type="submit" name="guardarPrestamo" class="boton-azul ancho-total">Registrar Préstamo</button>
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
                                <td><?php echo htmlspecialchars($p['nombreArticulo']); ?></td>
                                <td><?php echo htmlspecialchars($p['nombreEstudiante']); ?></td>
                                <td><?php echo htmlspecialchars($p['fechaPrestamo']); ?></td>
                                <td>
                                    <form method="POST" action="controlador/inventarioControlador.php" class="d-inline">
                                        <input type="hidden" name="accion" value="devolver">
                                        <input type="hidden" name="idPrestamo" value="<?php echo $p['idPrestamo']; ?>">
                                        <button type="submit" class="boton-gris">Devolver</button>
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
function filtrarAlumnosPorCurso(cursoId) {
    var opciones = document.getElementById('selectorEstudiante').options;
    for (var i = 1; i < opciones.length; i++) {
        var curso = opciones[i].getAttribute('data-curso');
        opciones[i].style.display = (cursoId === "todos" || cursoId === curso) ? "block" : "none";
    }
}
</script>

<?php include '../comunes/footer.php'; ?>
