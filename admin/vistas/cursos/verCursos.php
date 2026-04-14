<?php
session_start();
$titulo_pagina = "Ver Cursos - Super Admin";
$seccion = 'cursos';
include_once "../comunes/nav.php";

require_once "../../modelos/cursos.php";

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();

$cursoObj = new curso($conexion);
$listaCursos = $cursoObj->listarCursosModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Cursos</h1>
        <p class="subtitulo-encabezado">Gestión de cursos académicos</p>
    </div>
    <div class="acciones-pagina">
        <div class="caja-busqueda">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar curso..." id="inputBusqueda" />
        </div>
        <a href="vistas/cursos/agregarCursos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Curso
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
    <table class="tabla-datos" id="tablaCursos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Curso</th>
                <th>Nivel</th>
                <th>Tutor</th>
                <th>Aula</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaCursos)): ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay cursos registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaCursos as $c) { 
                    $estado = $c['nombreEstado'];
                    $estiloEstado = $estado === 'activo' ? 'estado-activo' : 'estado-inactivo';
                ?>
                <tr>
                    <td><?php echo $c['idCurso']; ?></td>
                    <td><?php echo htmlspecialchars($c['nombreCurso']); ?></td>
                    <td><?php echo htmlspecialchars($c['nombreNivel']); ?></td>
                    <td><?php echo htmlspecialchars($c['nombreProfesor'] ?? 'Sin asignar'); ?></td>
                    <td><?php echo htmlspecialchars($c['nombreAula'] ?? 'Sin asignar'); ?></td>
                    <td>
                        <span class="insignia-estado <?php echo $estiloEstado; ?>">
                            <?php echo ucfirst($estado); ?>
                        </span>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/cursos/verDetallesCursos.php?id=<?php echo $c['idCurso']; ?>" 
                               class="boton-icono boton-ver" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="vistas/cursos/modificarCursos.php?id=<?php echo $c['idCurso']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controlador/cursosControlador.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este curso?');">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="idCurso" value="<?php echo $c['idCurso']; ?>">
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
    var tablaFilas = document.querySelectorAll("#tablaCursos tbody tr");

    inputBusqueda.addEventListener("input", function() {
        var textoBusqueda = this.value.toLowerCase();
        tablaFilas.forEach(function(fila) {
            var nombre = fila.children[1].textContent.toLowerCase();
            fila.style.display = nombre.indexOf(textoBusqueda) !== -1 ? "" : "none";
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
