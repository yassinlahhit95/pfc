<?php
session_start();
$titulo_pagina = "Ver Estudiantes - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";

require_once "../../modelos/estudiantes.php";
require_once "../../modelos/ciclos.php";

$listaEstudiantes = listarEstudiantes();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Estudiantes</h1>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/estudiantes/agregarEstudiantes.php" class="boton-primario">
            <i class="fas fa-plus"></i> Agregar Estudiante
        </a>
    </div>
</div>

<?php if ($error) { ?>
<div class="mensaje-error">
    <p><?php echo $error; ?></p>
</div>
<?php } ?>

<?php if ($exito) { ?>
<div class="mensaje-exito">
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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaEstudiantes)) { ?>
            <tr>
                <td colspan="6" class="sin-datos">No hay estudiantes registrados</td>
            </tr>
            <?php } else { ?>
                <?php foreach ($listaEstudiantes as $estudiante) { ?>
                <tr>
                    <td><?php echo $estudiante['idEstudiante']; ?></td>
                    <td><strong><?php echo $estudiante['nombreEstudiante']; ?></strong></td>
                    <td><?php echo $estudiante['nombreCiclo']; ?></td>
                    <td><?php echo $estudiante['emailEstudiante']; ?></td>
                    <td><?php echo $estudiante['telefonoEstudiante']; ?></td>
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

<?php include '../comunes/footer.php'; ?>
