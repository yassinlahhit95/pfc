<?php
session_start();
$titulo_pagina = "Gestión de Profesores - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../../modelos/profesores.php";

$todos_los_profesores = listarProfesores();

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$mensaje_exito = "";
if (isset($_SESSION['exito'])) { $mensaje_exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Profesores del Centro</h1>
    <a href="/pfc/vistas/admin/profesores/agregarProfesores.php" class="boton-primario">
        <i class="fas fa-plus"></i> Nuevo Profesor
    </a>
</div>

<?php if ($mensaje_exito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensaje_exito; ?></div>
<?php } ?>
<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Especialidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_profesores)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay profesores registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_profesores as $profesor) { ?>
                    <tr>
                        <td><?php echo $profesor['idProfesor']; ?></td>
                        <td><strong><?php echo $profesor['nombreProfesor']; ?></strong></td>
                        <td><?php echo $profesor['emailProfesor']; ?></td>
                        <td><?php echo $profesor['especialidadProfesor']; ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=<?php echo $profesor['idProfesor']; ?>" class="boton-icono boton-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/profesores/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este profesor?')">
                                    <input type="hidden" name="idProfesor" value="<?php echo $profesor['idProfesor']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar">
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
</div>

<?php include '../comunes/footer.php'; ?>
