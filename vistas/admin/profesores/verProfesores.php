<?php
session_start();

// ValidaciÃ³n de sesiÃ³n simple
if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

$titulo_pagina = "GESTIÃ“N DE PROFESORES - ADMIN";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/profesores.php";

// Obtenemos la lista completa de profesores
$listaDeTodosLosProfesores = listarProfesores();

// Captura de mensajes de sesiÃ³n para alertas
$mensajeDeError = $_SESSION['error'] ?? '';

$mensajeDeExito = $_SESSION['exito'] ?? '';

// Limpiamos la sesiÃ³n despuÃ©s de capturar
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>PROFESORES DEL CENTRO</h1>
    <a href="agregarProfesores.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO PROFESOR
    </a>
</div>

<?php if (!empty($mensajeDeExito)) { ?>
    <div class="mensaje-exito"><?= $mensajeDeExito ?></div>
<?php } ?>

<?php if (!empty($mensajeDeError)) { ?>
    <div class="mensaje-error"><?= $mensajeDeError ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaProfesores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>CORREO ELECTRÃ“NICO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeTodosLosProfesores)) { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No hay profesores registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeTodosLosProfesores as $profesorIndividual) { ?>
                    <tr>
                        <td><?= $profesorIndividual['idProfesor'] ?></td>
                        <td><strong><?= strtoupper($profesorIndividual['nombreProfesor']) ?></strong></td>
                        <td><?= $profesorIndividual['emailProfesor'] ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="../../../vistas/admin/profesores/verDetallesProfesores.php?idProfesor=<?= $profesorIndividual['idProfesor'] ?>" 
                                   class="btn-accion btn-ver" title="Ver ficha completa">
                                    <i class="fas fa-search"></i>
                                </a>
                                <a href="../../../vistas/admin/profesores/asignarModulos.php?idProfesor=<?= $profesorIndividual['idProfesor'] ?>" 
                                   class="btn-accion btn-ver" title="Asignar MÃ³dulos especÃ­ficos">
                                    <i class="fas fa-book"></i>
                                </a>
                                <a href="../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=<?= $profesorIndividual['idProfesor'] ?>" 
                                   class="btn-accion btn-editar" title="Editar datos del profesor">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/profesores/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('Â¿EstÃ¡s seguro de eliminar a este profesor?')">
                                    <input type="hidden" name="idProfesor" value="<?= $profesorIndividual['idProfesor'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar" title="Eliminar del sistema">
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




