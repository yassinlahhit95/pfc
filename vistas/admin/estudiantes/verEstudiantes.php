<?php
session_start();

// Validación de sesión simple
if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

$titulo_pagina = "GESTIÓN DE ESTUDIANTES - SUPER ADMIN";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

// Obtenemos todos los estudiantes de la base de datos
$listaDeEstudiantesActuales = listarEstudiantes();

// Captura de mensajes de sesión para mostrar alertas
$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';

// Limpiamos los mensajes para que no se repitan al recargar
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>LISTADO DE ESTUDIANTES</h1>
    </div>
    <div class="acciones-pagina">
        <a href="agregarEstudiantes.php" class="boton-primario">
            <i class="fas fa-user-plus"></i> NUEVO ESTUDIANTE
        </a>
    </div>
</div>

<?php if ($exito) : ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>

<?php if ($error) : ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>

<?php 
$listaDeCiclosParaFiltro = listarTodosLosCiclos(); 
?>
<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label><i class="fas fa-filter"></i> FILTRAR POR CICLO:</label>
        <select id="selectFiltroCiclo" onchange="filtrarTabla('selectFiltroCiclo', 'tablaEstudiantes')">
            <option value="">-- Todos los Ciclos --</option>
            <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) : ?>
                <option value="<?= strtoupper($cicloFiltro['nombreCiclo']) ?>">
                    <?= strtoupper($cicloFiltro['nombreCiclo']) ?>
                </option>
            <?php endforeach; ?>
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
                <?php if (empty($listaDeEstudiantesActuales)) : ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No hay estudiantes registrados en el sistema.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($listaDeEstudiantesActuales as $estudianteIndividual) : ?>
                    <tr>
                        <td><?= $estudianteIndividual['idEstudiante'] ?></td>
                        <td><strong><?= strtoupper($estudianteIndividual['nombreEstudiante']) ?></strong></td>
                        <td><?= $estudianteIndividual['emailEstudiante'] ?></td>
                        <td><?= strtoupper($estudianteIndividual['nombreCiclo']) ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="verDetallesEstudiantes.php?idEstudiante=<?= $estudianteIndividual['idEstudiante'] ?>" 
                                   class="btn-accion btn-ver" title="Ver ficha completa">
                                    <i class="fas fa-id-card"></i>
                                </a>
                                <a href="modificarEstudiantes.php?idEstudiante=<?= $estudianteIndividual['idEstudiante'] ?>" 
                                   class="btn-accion btn-editar" title="Editar información">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="../../../controladores/admin/estudiantes/borrar.php" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar a este estudiante?')">
                                    <input type="hidden" name="idEstudiante" value="<?= $estudianteIndividual['idEstudiante'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar" title="Borrar registro">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
