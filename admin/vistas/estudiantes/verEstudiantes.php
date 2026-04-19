<?php
session_start();
$titulo_pagina = "Ver Estudiantes - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";

require_once "../../modelos/estudiantes.php";
require_once "../../modelos/ciclos.php";

$listaEstudiantes = listarEstudiantes();
$listaCiclos = listarTodosLosCiclos();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Estudiantes</h1>
        <p class="subtitulo-encabezado">Gestión de alumnos matriculados</p>
    </div>
    <div class="acciones-pagina">
        <div class="disposicion-flexible separacion-pequena">
            <select id="filtroCiclo" class="selector-filtro">
                <option value="">Todos los ciclos</option>
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?php echo $ciclo['nombreCiclo']; ?>">
                        <?php echo $ciclo['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
            
            <a href="vistas/estudiantes/agregarEstudiantes.php" class="boton-primario">
                <i class="fas fa-plus"></i> Agregar Estudiante
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
    <table class="tabla-datos" id="tablaEstudiantes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ciclo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaEstudiantes)) { ?>
            <tr>
                <td colspan="7" class="sin-datos">No hay estudiantes registrados</td>
            </tr>
            <?php } else { ?>
                <?php foreach ($listaEstudiantes as $estudiante) { 
                    $estado = $estudiante['idEstado'] == 1 ? 'Activo' : 'Inactivo';
                    $claseEstado = $estudiante['idEstado'] == 1 ? 'estado-activo' : 'estado-inactivo';
                ?>
                <tr>
                    <td><?php echo $estudiante['idEstudiante']; ?></td>
                    <td><?php echo $estudiante['nombreEstudiante']; ?></td>
                    <td><?php echo $estudiante['nombreCiclo']; ?></td>
                    <td><?php echo $estudiante['emailEstudiante']; ?></td>
                    <td><?php echo $estudiante['telefonoEstudiante']; ?></td>
                    <td>
                        <span class="insignia-estado <?php echo $claseEstado; ?>">
                            <?php echo $estado; ?>
                        </span>
                    </td>
                    <td>
                        <div class="botones-accion">
                            <a href="vistas/estudiantes/verDetallesEstudiantes.php?idEstudiante=<?php echo $estudiante['idEstudiante']; ?>" 
                               class="boton-icono boton-ver" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="vistas/estudiantes/modificarEstudiantes.php?idEstudiante=<?php echo $estudiante['idEstudiante']; ?>" 
                                                           class="boton-icono boton-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="controladores/estudiantes/borrar.php" 
                                  class="form-eliminar d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar este estudiante?');">
                                <input type="hidden" name="idEstudiante" value="<?php echo $estudiante['idEstudiante']; ?>">
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
document.addEventListener('DOMContentLoaded', function() {
    const filtroCiclo = document.getElementById('filtroCiclo');
    const tablaEstudiantes = document.getElementById('tablaEstudiantes');
    const filas = tablaEstudiantes.querySelectorAll('tbody tr');

    filtroCiclo.addEventListener('change', function() {
        const valor = this.value;
        filas.forEach(fila => {
            if (fila.classList.contains('sin-datos')) return;
            const ciclo = fila.cells[2].textContent;
            if (valor === "" || ciclo === valor) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
