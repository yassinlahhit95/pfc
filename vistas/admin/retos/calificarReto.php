<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id = $_GET['id'] ?? '';
$retoActual = obtenerRetoPorId($id);

if (!$retoActual) {
    header("Location: verRetos.php");
    exit;
}

$listaEstudiantes = listarEstudiantes();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);

$titulo_pagina = "AULAPRO | CALIFICAR RETO";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>CALIFICAR RETO: <?= $retoActual['nombreReto'] ?></h1>
    <a href="verRetos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="contenedor-tabla">
    <form action="../../../controladores/admin/retos/calificar.php" method="POST">
        <input type="hidden" name="idReto" value="<?= $retoActual['idReto'] ?>">
        
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>DNI</th>
                    <th>Nota Actual</th>
                    <th>Nueva Nota (0-10)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaEstudiantes)) { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No hay estudiantes registrados</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEstudiantes as $estudiante) { 
                        $notaActual = obtenerCalificacionReto($estudiante['idEstudiante'], $id);
                    ?>
                    <tr>
                        <td><?= $estudiante['nombreEstudiante'] ?></td>
                        <td><?= $estudiante['dniEstudiante'] ?></td>
                        <td>
                            <span class="etiqueta-estado <?= $notaActual !== null ? 'activo' : '' ?>">
                                <?= $notaActual !== null ? number_format($notaActual, 2) : 'Sin calificar' ?>
                            </span>
                        </td>
                        <td>
                            <input type="text" name="notas[<?= $estudiante['idEstudiante'] ?>]" 
                                   value="<?= $notaActual ?>"
                                   class="p-5 w-80 br-4 border-ddd">
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        
       <div class="botones-formulario" style="margin-top: 20px;">
            <button type="submit" class="boton-primario">Guardar Calificaciones</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>




