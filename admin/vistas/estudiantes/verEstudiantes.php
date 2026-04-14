<?php
session_start();
$titulo_pagina = "Ver Estudiantes - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/estudiantes.php";
require_once "../../modelos/cursos.php";

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();

$cursoObj = new curso($conexion);
$listaCursos = $cursoObj->listarCursosModelo();

$estudiante = new estudiante($conexion);
$listaEstudiantes = $estudiante->listarEstudiantesModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Estudiantes</h1>
        <p class="subtitulo-encabezado">Gestión de estudiantes del sistema</p>
    </div>
    <div class="acciones-pagina">
        <div class="caja-busqueda">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar estudiante..." id="inputBusqueda" />
        </div>

        <select id="filtroCurso" class="selector-filtro">
            <option value="">Todos los cursos</option>
            <?php foreach ($listaCursos as $c) { ?>
                <option value="<?php echo htmlspecialchars($c['nombreCurso']); ?>">
                    <?php echo htmlspecialchars($c['nombreCurso']); ?>
                </option>
            <?php } ?>
        </select>

        <a href="vistas/estudiantes/agregarEstudiantes.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Estudiante
        </a>
    </div>
</div>

<?php if ($error): ?>
<div class="mensaje-error">
    <i class="fas fa-exclamation-circle"></i>
    <p><?php echo htmlspecialchars($error); ?></p>
</div>
<?php endif; ?>

<?php if ($exito): ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo htmlspecialchars($exito); ?></p>
</div>
<?php endif; ?>

<div class="contenedor-tabla">
    <table class="tabla-datos" id="tablaEstudiantes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Curso</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaEstudiantes)): ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay estudiantes registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaEstudiantes as $alumno) { 
                    $estado = $alumno['nombreEstado'];
                    $estiloEstado = $estado === 'activo' ? 'estado-activo' : 'estado-inactivo';
                ?>
                <tr data-curso="<?php echo htmlspecialchars($alumno['nombreCurso']); ?>">
                    <td><?php echo $alumno['idEstudiante']; ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombreEstudiante']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['emailEstudiante']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombreCurso']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['telefonoEstudiante'] ?? '-'); ?></td>
                    <td>
                        <span class="insignia-estado <?php echo $estiloEstado; ?>">
                            <?php echo ucfirst($estado); ?>
                        </span>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/estudiantes/verDetallesEstudiantes.php?id=<?php echo $alumno['idEstudiante']; ?>" 
                               class="boton-icono boton-ver" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="vistas/estudiantes/modificarEstudiantes.php?id=<?php echo $alumno['idEstudiante']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controlador/estudiantesControlador.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este estudiante?');">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="idEstudiante" value="<?php echo $alumno['idEstudiante']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var inputBusqueda = document.getElementById("inputBusqueda");
    var filtroCurso = document.getElementById("filtroCurso");
    var tablaFilas = document.querySelectorAll(".tabla-datos tbody tr");

    function filtrarEstudiantes() {
        var textoBusqueda = inputBusqueda.value.toLowerCase();
        var cursoSeleccionado = filtroCurso.value.toLowerCase();

        tablaFilas.forEach(function(fila) {
            var nombre = fila.children[1].textContent.toLowerCase();
            var email = fila.children[2].textContent.toLowerCase();
            var curso = fila.getAttribute('data-curso').toLowerCase();

            var coincideTexto = nombre.indexOf(textoBusqueda) !== -1 || email.indexOf(textoBusqueda) !== -1;
            var coincideCurso = cursoSeleccionado === "" || curso === cursoSeleccionado;

            fila.style.display = (coincideTexto && coincideCurso) ? "" : "none";
        });
    }

    inputBusqueda.addEventListener("input", filtrarEstudiantes);
    filtroCurso.addEventListener("change", filtrarEstudiantes);
});
</script>

<?php include '../comunes/footer.php'; ?>
