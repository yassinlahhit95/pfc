<?php
session_start();
$titulo_pagina = "Ver Directores - Super Admin";
$seccion = 'directores';
include_once "../comunes/nav.php";

require_once "../../modelos/directores.php";

$listaDirectores = listarDirectores();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Directores</h1>
        <p class="subtitulo-encabezado">Gestión de directores del sistema</p>
    </div>
    <div class="acciones-pagina">
        <div class="caja-busqueda">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar director..." id="inputBusqueda" />
        </div>
        <a href="vistas/directores/agregarDirectores.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Director
        </a>
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
    <table class="tabla-datos" id="tablaDirectores">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Fecha Alta</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaDirectores)) { ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay directores registrados</td>
            </tr>
            <?php } else { ?>
                <?php foreach ($listaDirectores as $d) { 
                    $estado = $d['nombreEstado'];
                    $estiloEstado = $estado === 'activo' ? 'estado-activo' : 'estado-inactivo';
                ?>
                <tr>
                    <td><?php echo $d['idDirector']; ?></td>
                    <td><?php echo $d['nombreDirector']; ?></td>
                    <td><?php echo $d['emailDirector']; ?></td>
                    <td><?php echo $d['telefonoDirector'] ?? '-'; ?></td>
                    <td><?php echo $d['fechaAltaDirector']; ?></td>
                    <td>
                        <span class="insignia-estado <?php echo $estiloEstado; ?>">
                            <?php echo ucfirst($estado); ?>
                        </span>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/directores/verDetallesDirectores.php?id=<?php echo $d['idDirector']; ?>" 
                               class="boton-icono boton-ver" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="vistas/directores/modificarDirectores.php?id=<?php echo $d['idDirector']; ?>" 
                               class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="../../controladores/directores/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este director?');">
                                <input type="hidden" name="idDirector" value="<?php echo $d['idDirector']; ?>">
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
    var tablaFilas = document.querySelectorAll("#tablaDirectores tbody tr");

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
