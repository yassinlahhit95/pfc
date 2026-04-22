<?php
session_start();
$titulo_pagina = "Calificar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../../modelos/retos.php";
require_once "../../../modelos/estudiantes.php";

$id = $_GET['id'];
$retoActual = obtenerDetallesReto($id);

if (!$retoActual) {
    header("Location: verRetos.php");
    exit;
}

$listaEstudiantes = listarEstudiantes();

$exito = "";
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
    unset($_SESSION['exito']);
}
?>

<div class="encabezado-pagina">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; width: 100%;">
        <div>
            <h1>Calificar Reto: <?php echo $retoActual['nombreReto']; ?></h1>
        </div>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/retos/verRetos.php" class="boton-secundario">Volver</a>
    </div>
</div>

<?php if (!empty($exito)) { ?>
<div class="mensaje-exito">
    <p><?php echo $exito; ?></p>
</div>
<?php } ?>

<div class="contenedor-tabla">
    <form action="/pfc/controladores/admin/retos/calificar.php" method="POST">
        <input type="hidden" name="idReto" value="<?php echo $retoActual['idReto']; ?>">
        
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
                        $notaActual = obtenerCalificacion($estudiante['idEstudiante'], $id);
                    ?>
                    <tr>
                        <td><?php echo $estudiante['nombreEstudiante']; ?></td>
                        <td><?php echo $estudiante['dniEstudiante']; ?></td>
                        <td>
                            <span class="etiqueta-estado <?php if ($notaActual !== null) { echo 'activo'; } else { echo ''; } ?>">
                                <?php 
                                    if ($notaActual !== null) { 
                                        echo number_format($notaActual, 2); 
                                    } else { 
                                        echo 'Sin calificar'; 
                                    } 
                                ?>
                            </span>
                        </td>
                        <td>
                            <input type="text" name="notas[<?php echo $estudiante['idEstudiante']; ?>]" 
                                   value="<?php echo $notaActual; ?>"
                                   class="p-5 w-80 br-4 border-ddd">
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        
       <div class="botones-formulario mt-20">
            <button type="submit" class="boton-primario">Guardar Calificaciones</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

