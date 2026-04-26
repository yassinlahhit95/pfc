<?php
session_start();

// Validación de sesión simple
if (isset($_SESSION['idAdmin']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "GESTIÓN DE PROFESORES - SUPER ADMIN";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../../modelos/profesores.php";

// Obtenemos la lista completa de profesores
$listaDeTodosLosProfesores = listarProfesores();

// Captura de mensajes de sesión para alertas
$mensajeDeError = "";
if (isset($_SESSION['error'])) { 
    $mensajeDeError = $_SESSION['error']; 
}

$mensajeDeExito = "";
if (isset($_SESSION['exito'])) { 
    $mensajeDeExito = $_SESSION['exito']; 
}

// Limpiamos la sesión después de capturar
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>PROFESORES DEL CENTRO</h1>
    <a href="/pfc/vistas/admin/profesores/agregarProfesores.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO PROFESOR
    </a>
</div>

<?php if ($mensajeDeExito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensajeDeExito; ?></div>
<?php } ?>

<?php if ($mensajeDeError != "") { ?>
    <div class="mensaje-error"><?php echo $mensajeDeError; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaProfesores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>CORREO ELECTRÓNICO</th>
                    <th>ESPECIALIDAD / ÁREA</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($listaDeTodosLosProfesores == false || count($listaDeTodosLosProfesores) == 0) { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No hay profesores registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeTodosLosProfesores as $profesorIndividual) { ?>
                    <tr>
                        <td><?php echo $profesorIndividual['idProfesor']; ?></td>
                        <td><strong><?php echo strtoupper($profesorIndividual['nombreProfesor']); ?></strong></td>
                        <td><?php echo $profesorIndividual['emailProfesor']; ?></td>
                        <td><?php echo strtoupper($profesorIndividual['especialidad']); ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/profesores/asignarModulos.php?idProfesor=<?php echo $profesorIndividual['idProfesor']; ?>" 
                                   class="boton-icono boton-ver" title="Asignar Módulos específicos">
                                    <i class="fas fa-book"></i>
                                </a>
                                <a href="/pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=<?php echo $profesorIndividual['idProfesor']; ?>" 
                                   class="boton-icono boton-editar" title="Editar datos del profesor">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/profesores/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar a este profesor?')">
                                    <input type="hidden" name="idProfesor" value="<?php echo $profesorIndividual['idProfesor']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar" title="Eliminar del sistema">
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
