<?php
session_start();
$titulo_pagina = "Gestión de Estudiantes - Super Admin";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";

require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/ciclos.php";

$listaEstudiantes = listarEstudiantes();

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
        <h1>Estudiantes</h1>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/estudiantes/agregarEstudiantes.php" class="boton-primario">
            <i class="fas fa-user-plus"></i> Nuevo Estudiante
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
                    <th>Ciclo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaEstudiantes)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay estudiantes registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaEstudiantes as $estudiante) { ?>
                    <tr>
                        <td><?php echo $estudiante['idEstudiante']; ?></td>
                        <td><strong><?php echo $estudiante['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $estudiante['emailEstudiante']; ?></td>
                        <td><?php 
                            if ($estudiante['nombreCiclo']) {
                                echo $estudiante['nombreCiclo'];
                            } else {
                                echo '<span class="texto-atenuado">Sin asignar</span>';
                            }
                        ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="vistas/estudiantes/verDetallesEstudiantes.php?idEstudiante=<?php echo $estudiante['idEstudiante']; ?>" 
                                   class="boton-icono boton-ver" title="Ver ficha">
                                    <i class="fas fa-id-card"></i>
                                </a>
                                <a href="vistas/estudiantes/modificarEstudiantes.php?idEstudiante=<?php echo $estudiante['idEstudiante']; ?>" 
                                   class="boton-icono boton-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="controladores/estudiantes/borrar.php" 
                                      class="d-inline"
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
</div>

<?php include '../comunes/footer.php'; ?>