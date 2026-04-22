<?php
session_start();
$titulo_pagina = "Gestión de Profesores - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../../modelos/profesores.php";
$listaProfesores = listarProfesores();

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Profesores</h1>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/profesores/agregarProfesores.php" class="boton-primario">
            <i class="fas fa-user-plus"></i> Nuevo Profesor
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>DNI</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaProfesores)) { ?>
                    <tr><td colspan="6" class="sin-datos">No hay profesores registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaProfesores as $profesor) { ?>
                    <tr>
                        <td><?php echo $profesor['idProfesor']; ?></td>
                        <td><strong><?php echo $profesor['nombreProfesor']; ?></strong></td>
                        <td><?php 
                            if ($profesor['emailProfesor']) {
                                echo $profesor['emailProfesor'];
                            } else {
                                echo '-';
                            }
                        ?></td>
                        <td><?php 
                            if ($profesor['telefonoProfesor']) {
                                echo $profesor['telefonoProfesor'];
                            } else {
                                echo '-';
                            }
                        ?></td>
                        <td><?php 
                            if ($profesor['dniProfesor']) {
                                echo $profesor['dniProfesor'];
                            } else {
                                echo '-';
                            }
                        ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/profesores/verDetallesProfesores.php?idProfesor=<?php echo $profesor['idProfesor']; ?>" 
                                   class="boton-icono boton-ver" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=<?php echo $profesor['idProfesor']; ?>" 
                                   class="boton-icono boton-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="/pfc/controladores/admin/profesores/borrar.php" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este profesor?');">
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
</div>

<?php include '../comunes/footer.php'; ?>
