<?php
session_start();

// Validación de sesión simple
if (isset($_SESSION['idAdmin']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "GESTIÓN DE ESTUDIANTES - SUPER ADMIN";
$seccion = 'estudiantes';
include_once "../comunes/nav.php";

require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/ciclos.php";

// Obtenemos todos los estudiantes de la base de datos
$listaDeEstudiantesActuales = listarEstudiantes();

// Captura de mensajes de sesión para mostrar alertas
$mensajeExitoAMostrar = "";
if (isset($_SESSION['exito'])) {
    $mensajeExitoAMostrar = $_SESSION['exito'];
}

$mensajeErrorAMostrar = "";
if (isset($_SESSION['error'])) {
    $mensajeErrorAMostrar = $_SESSION['error'];
}

// Limpiamos los mensajes para que no se repitan al recargar
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>LISTADO DE ESTUDIANTES</h1>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/estudiantes/agregarEstudiantes.php" class="boton-primario">
            <i class="fas fa-user-plus"></i> NUEVO ESTUDIANTE
        </a>
    </div>
</div>

<?php if ($mensajeExitoAMostrar != "") { ?>
    <div class="mensaje-exito"><?php echo $mensajeExitoAMostrar; ?></div>
<?php } ?>

<?php if ($mensajeErrorAMostrar != "") { ?>
    <div class="mensaje-error"><?php echo $mensajeErrorAMostrar; ?></div>
<?php } ?>

<?php 
$listaDeCiclosParaFiltro = listarTodosLosCiclos(); 
?>
<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label><i class="fas fa-filter"></i> FILTRAR POR CICLO:</label>
        <select id="selectFiltroCiclo" onchange="filtrarTabla('selectFiltroCiclo', 'tablaEstudiantes')">
            <option value="">-- Todos los Ciclos --</option>
            <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                <option value="<?php echo strtoupper($cicloFiltro['nombreCiclo']); ?>">
                    <?php echo strtoupper($cicloFiltro['nombreCiclo']); ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEstudiantes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>CORREO ELECTRÓNICO</th>
                    <th>CICLO ASIGNADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($listaDeEstudiantesActuales == false || count($listaDeEstudiantesActuales) == 0) { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No hay estudiantes registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeEstudiantesActuales as $estudianteIndividual) { ?>
                    <tr>
                        <td><?php echo $estudianteIndividual['idEstudiante']; ?></td>
                        <td><strong><?php echo strtoupper($estudianteIndividual['nombreEstudiante']); ?></strong></td>
                        <td><?php echo $estudianteIndividual['emailEstudiante']; ?></td>
                        <td><?php echo strtoupper($estudianteIndividual['nombreCiclo']); ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=<?php echo $estudianteIndividual['idEstudiante']; ?>" 
                                   class="boton-icono boton-ver" title="Ver ficha completa">
                                    <i class="fas fa-id-card"></i>
                                </a>
                                <a href="/pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=<?php echo $estudianteIndividual['idEstudiante']; ?>" 
                                   class="boton-icono boton-editar" title="Editar información">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="/pfc/controladores/admin/estudiantes/borrar.php" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar a este estudiante?')">
                                    <input type="hidden" name="idEstudiante" value="<?php echo $estudianteIndividual['idEstudiante']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar" title="Borrar registro">
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
