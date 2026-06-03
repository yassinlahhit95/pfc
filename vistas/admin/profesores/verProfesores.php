<?php
session_start();

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";

$listaDeTodosLosProfesores = listarProfesores();

$titulo_pagina = "AULAPRO | PROFESORES DEL CENTRO";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>PROFESORES DEL CENTRO</h1>
    <a href="agregarProfesores.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO PROFESOR
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div>
<?php } ?>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div>
<?php } ?>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaProfesores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>CORREO ELECTRONICO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeTodosLosProfesores)) { ?>
                    <tr>
                        <td colspan="4" class="vacio">No hay profesores registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeTodosLosProfesores as $profesorIndividual) { ?>
                    <tr>
                        <td><?= Security::escapeHtml($profesorIndividual['idProfesor']) ?></td>
                        <td><b><?= strtoupper(Security::escapeHtml($profesorIndividual['nombreProfesor'])) ?></b></td>
                        <td><?= Security::escapeHtml($profesorIndividual['emailProfesor']) ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="../../../vistas/admin/profesores/verDetallesProfesores.php?idProfesor=<?= Security::escapeHtml($profesorIndividual['idProfesor']) ?>"><i class="fas fa-search"></i> Ver detalles</a>
                                    <a class="recurso-menu-item" href="../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=<?= Security::escapeHtml($profesorIndividual['idProfesor']) ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarProfesor.php?id=<?= Security::escapeHtml($profesorIndividual['idProfesor']) ?>" onclick="return confirm('¿Eliminar este profesor?')"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
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
<script>
iniciarPaginacion('tablaProfesores', 8);
</script>

