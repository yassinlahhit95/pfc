<?php
session_start();
$titulo_pagina = "Ver Profesores - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../modelos/profesores.php";

$profs = new profesor();
$listaProfesores = $profs->listarProfesoresModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Profesores</h1>
        <p class="subtitulo-encabezado">Gestión de profesores del sistema</p>
    </div>
    <div class="acciones-pagina">
        <div class="caja-busqueda">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar profesor..." id="inputBusqueda" />
        </div>
        <a href="vistas/profesores/agregarProfesores.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Profesor
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
    <table class="tabla-datos" id="tablaProfesores">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>DNI</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaProfesores)): ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay profesores registrados</td>
            </tr>
            <?php else: ?>
                <?php foreach ($listaProfesores as $profesor) { 
                    $estado = $profesor['nombreEstado'];
                    $estiloEstado = $estado === 'activo' ? 'estado-activo' : 'estado-inactivo';
                ?>
                <tr>
                    <td><?php echo $profesor['idProfesor']; ?></td>
                    <td><?php echo htmlspecialchars($profesor['nombreProfesor']); ?></td>
                    <td><?php echo htmlspecialchars($profesor['emailProfesor'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($profesor['telefonoProfesor'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($profesor['dniProfesor'] ?? '-'); ?></td>
                    <td>
                        <span class="insignia-estado <?php echo $estiloEstado; ?>">
                            <?php echo ucfirst($estado); ?>
                        </span>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/profesores/verDetallesProfesores.php?id=<?php echo $profesor['idProfesor']; ?>" 
                               class="boton-icono boton-ver" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="vistas/profesores/modificarProfesores.php?id=<?php echo $profesor['idProfesor']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/profesores/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este profesor?');">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="idProfesor" value="<?php echo $profesor['idProfesor']; ?>">
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
    var tablaFilas = document.querySelectorAll("#tablaProfesores tbody tr");

    inputBusqueda.addEventListener("input", function() {
        var textoBusqueda = this.value.toLowerCase();
        tablaFilas.forEach(function(fila) {
            var nombre = fila.children[1].textContent.toLowerCase();
            var email = fila.children[2].textContent.toLowerCase();
            fila.style.display = (nombre.indexOf(textoBusqueda) !== -1 || email.indexOf(textoBusqueda) !== -1) ? "" : "none";
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
