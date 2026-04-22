<?php
session_start();
$titulo_pagina = "Gestión de TFGs - Super Admin";
$seccion = 'tfg';
include_once "../comunes/nav.php";

require_once "../../../modelos/tfg.php";

$listaTFGs = listarTodosLosTFGs();

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
        <h1>Proyectos TFG</h1>
        <p class="subtitulo-encabezado">Listado de trabajos finales entregados</p>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>


<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Proyectos Entregados por Alumnos</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Título del TFG</th>
                    <th>Fecha de Subida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaTFGs)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay TFGs subidos actualmente.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaTFGs as $tfg) { ?>
                    <tr>
                        <td><strong><?php echo $tfg['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $tfg['nombreCiclo']; ?></td>
                        <td><?php echo $tfg['tituloTFG'] ? $tfg['tituloTFG'] : 'Sin título'; ?></td>
                        <td><?php 
                            if ($tfg['fechaSubidaTFG']) {
                                echo date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG'])); 
                            } else {
                                echo 'N/A';
                            }
                        ?></td>
                        <td>
                            <a href="/pfc/public/uploads/tfg/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="boton-secundario boton-pequeno">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
