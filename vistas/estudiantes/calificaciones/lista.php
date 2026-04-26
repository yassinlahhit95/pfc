<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";

$id = $_SESSION['idEstudiante'];

$notas = listarCalificacionesPorEstudiante($id); 

$tituloDelPagina = "Mis Calificaciones - Portal Estudiantes";
$seccionActual = 'calificaciones';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Mis Calificaciones</h1>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Calificaciones por Módulo</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>1ª Ev</th>
                    <th>1ª Final</th>
                    <th>2ª Ev</th>
                    <th>2ª Final</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($notas) { ?>
                    <?php foreach ($notas as $nota) { 
                        $nFinal = $nota['nota_2final'] > 0 ? $nota['nota_2final'] : $nota['nota_1final'];
                        $aprobado = ($nFinal >= 5);
                    ?>
                        <tr>
                            <td><strong><?php echo $nota['nombreModulo']; ?></strong></td>
                            <td><?php echo $nota['nota_1ev']; ?></td>
                            <td><?php echo $nota['nota_1final']; ?></td>
                            <td><?php echo $nota['nota_2ev']; ?></td>
                            <td><?php echo $nota['nota_2final']; ?></td>
                            <td>
                                <?php if ($aprobado) { ?>
                                    <span class="texto-verde texto-negrita">Aprobado</span>
                                <?php } else { ?>
                                    <span class="texto-rojo texto-negrita">Suspenso</span>
                                <?php } ?>
                            </td>
                            <td><small><?php echo $nota['observaciones']; ?></small></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="sin-datos">
                            <i class="fas fa-inbox"></i> No hay calificaciones registradas.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
