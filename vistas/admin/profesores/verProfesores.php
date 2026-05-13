<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "AULAPRO | PROFESORES DEL CENTRO";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/profesores.php";

$listaDeTodosLosProfesores = listarProfesores();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>PROFESORES DEL CENTRO</h1>
    <a href="agregarProfesores.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO PROFESOR
    </a>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaProfesores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>CORREO ELECTRÓNICO</th>
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
                        <td><strong><?= mb_strtoupper($profesorIndividual['nombreProfesor'], 'UTF-8') ?></strong></td>
                        <td><?= $profesorIndividual['emailProfesor'] ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="../../../vistas/admin/profesores/verDetallesProfesores.php?idProfesor=<?= $profesorIndividual['idProfesor'] ?>" 
                                   class="btn-accion btn-ver" title="Ver ficha completa">
                                    <i class="fas fa-search"></i>
                                </a>
                                <a href="../../../vistas/admin/profesores/asignarModulos.php?idProfesor=<?= $profesorIndividual['idProfesor'] ?>" 
                                   class="btn-accion btn-ver" title="Asignar Módulos específicos">
                                    <i class="fas fa-book"></i>
                                </a>
                                <a href="../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=<?= $profesorIndividual['idProfesor'] ?>" 
                                   class="btn-accion btn-editar" title="Editar datos del profesor">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/profesores/borrar.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar a este profesor?')">
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
<script>
iniciarPaginacion('tablaProfesores', 8);
</script>





