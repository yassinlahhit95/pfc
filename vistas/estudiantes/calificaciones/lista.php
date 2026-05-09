<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";

$id = $_SESSION['idEstudiante'];

$notas = listarCalificacionesPorEstudiante($id); 

$tituloDelPagina = "AULAPRO | MIS CALIFICACIONES";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MIS CALIFICACIONES</h1>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Calificaciones por Módulo</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>1º Ev</th>
                    <th>1º Final</th>
                    <th>2º Ev</th>
                    <th>2º Final</th>
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
                            <td><strong><?= $nota['nombreModulo'] ?></strong></td>
                            <td><?= $nota['nota_1ev'] ?></td>
                            <td><?= $nota['nota_1final'] ?></td>
                            <td><?= $nota['nota_2ev'] ?></td>
                            <td><?= $nota['nota_2final'] ?></td>
                            <td>
                                <?php if ($aprobado) { ?>
                                    <span class="texto-verde texto-negrita">Aprobado</span>
                                <?php } else { ?>
                                    <span class="texto-rojo texto-negrita">Suspenso</span>
                                <?php } ?>
                            </td>
                            <td><small><?= $nota['observaciones'] ?></small></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="sin-datos">
                            No hay calificaciones registradas.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>





