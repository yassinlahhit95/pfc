<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

$idEst = $_SESSION['idEstudiante'];

require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$datosEst = obtenerEstudiantePorId($idEst);
$idCiclo = $datosEst['idCiclo'] ?? 0;

// Obtenemos los retos específicos del ciclo del estudiante
$retos = obtenerRetosPorCiclo($idCiclo);

$tituloDelPagina = "AULAPRO | MIS RETOS";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MIS RETOS</h1>
    <p class="subtitulo">Retos asignados a tu ciclo: <?= $datosEst['nombreCiclo'] ?></p>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Lista de Retos Disponibles</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre del Reto</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Horas Estimadas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($retos)) { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No hay retos asignados actualmente para su ciclo formativo.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($retos as $reto) { ?>
                        <tr>
                            <td class="texto-negrita"><?= strtoupper($reto['nombreReto']) ?></td>
                            <td><?= date('d/m/Y', strtotime($reto['fechaInicio'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($reto['fechaFin'])) ?></td>
                            <td><?= $reto['horasReto'] ?> h</td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>





