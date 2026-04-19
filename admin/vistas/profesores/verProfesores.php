<?php
session_start();
$titulo_pagina = "Ver Profesores - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../modelos/profesores.php";
$listaProfesores = listarProfesores();

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
        <div class="disposicion-flexible separacion-pequena">
            <div class="campo-formulario sin-margen">
                <input type="text" placeholder="Buscar profesor..." id="inputBusqueda" class="w-100" />
            </div>
            <a href="vistas/profesores/agregarProfesores.php" class="boton-primario">
                <i class="fas fa-plus"></i> Agregar Profesor
            </a>
        </div>
    </div>
</div>

<?php if ($error) { ?>
<div class="mensaje-error">
    <i class="fas fa-exclamation-circle"></i>
    <p><?php echo $error; ?></p>
</div>
<?php } ?>

<?php if ($exito) { ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo $exito; ?></p>
</div>
<?php } ?>

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
            <?php if (empty($listaProfesores)) { ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay profesores registrados</td>
            </tr>
            <?php } else { ?>
                <?php foreach ($listaProfesores as $profesor) { 
                    $estado = $profesor['nombreEstado'];
                    $claseEstado = ($estado == 'activo') ? 'activo-verde' : 'inactivo-rojo';
                ?>
                <tr>
                    <td><?php echo $profesor['idProfesor']; ?></td>
                    <td><strong><?php echo $profesor['nombreProfesor']; ?></strong></td>
                    <td><?php echo $profesor['emailProfesor'] ?? '-'; ?></td>
                    <td><?php echo $profesor['telefonoProfesor'] ?? '-'; ?></td>
                    <td><?php echo $profesor['dniProfesor'] ?? '-'; ?></td>
                    <td>
                        <span class="estado-bolita <?php echo $claseEstado; ?>">
                            <?php echo ucfirst($estado); ?>
                        </span>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/profesores/verDetallesProfesores.php?idProfesor=<?php echo $profesor['idProfesor']; ?>" 
                               class="boton-icono boton-ver" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="vistas/profesores/modificarProfesores.php?idProfesor=<?php echo $profesor['idProfesor']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/profesores/borrar.php" 
                                  class="d-inline"
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
            <?php } ?>
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
            if (fila.classList.contains('sin-datos')) return;
            var nombre = fila.cells[1].textContent.toLowerCase();
            var email = fila.cells[2].textContent.toLowerCase();
            
            if (nombre.indexOf(textoBusqueda) !== -1 || email.indexOf(textoBusqueda) !== -1) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
