<?php
session_start();
$titulo_pagina = "Ver Estudiantes - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";

require_once "../../modelos/estudiantes.php";
require_once "../../modelos/ciclos.php";

$cicloObj = new ciclo();
$listaCiclos = $cicloObj->listarCiclosModelo();

$estudiante = new estudiante();
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

        <select id="filtroCiclo" class="selector-filtro">
            <option value="">Todos los ciclos</option>
            <?php foreach ($listaCiclos as $c) { ?>
                <option value="<?php echo htmlspecialchars($c['nombreCiclo']); ?>">
                    <?php echo htmlspecialchars($c['nombreCiclo']); ?>
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
                <th>Ciclo</th>
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
                    $estado = ($alumno['idEstado'] == 1) ? 'activo' : 'inactivo';
                    $estiloEstado = $alumno['idEstado'] == 1 ? 'estado-activo' : 'estado-inactivo';
                ?>
                <tr data-ciclo="<?php echo htmlspecialchars($alumno['nombreCiclo'] ?? ''); ?>">
                    <td><?php echo $alumno['idEstudiante']; ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombreEstudiante']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['emailEstudiante']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombreCiclo'] ?? 'Sin ciclo'); ?></td>
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
                            <form method="POST" action="controladores/estudiantes/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este estudiante?');">
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
    var filtroCiclo = document.getElementById("filtroCiclo");
    var tablaFilas = document.querySelectorAll("#tablaEstudiantes tbody tr");

    function filtrarEstudiantes() {
        var textoBusqueda = inputBusqueda.value.toLowerCase();
        var cicloSeleccionado = filtroCiclo.value.toLowerCase();

        tablaFilas.forEach(function(fila) {
            var nombre = fila.children[1].textContent.toLowerCase();
            var email = fila.children[2].textContent.toLowerCase();
            var ciclo = fila.getAttribute('data-ciclo') ? fila.getAttribute('data-ciclo').toLowerCase() : "";

            var coincideTexto = nombre.indexOf(textoBusqueda) !== -1 || email.indexOf(textoBusqueda) !== -1;
            var coincideCiclo = cicloSeleccionado === "" || ciclo === cicloSeleccionado;

            fila.style.display = (coincideTexto && coincideCiclo) ? "" : "none";
        });
    }

    inputBusqueda.addEventListener("input", filtrarEstudiantes);
    filtroCiclo.addEventListener("change", filtrarEstudiantes);
});
</script>

<?php include '../comunes/footer.php'; ?>
