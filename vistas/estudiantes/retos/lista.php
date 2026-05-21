<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

$idEstudiante = $_SESSION['idEstudiante'];

require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$datosEstudiante = obtenerEstudiantePorId($idEstudiante);
if (!$datosEstudiante) {
    $_SESSION['errores'] = "ERROR AL RECUPERAR DATOS DEL ESTUDIANTE.";
    header("Location: ../inicio/dashboard.php");
    exit;
}

$idCiclo = $datosEstudiante['idCiclo'] ?? 0;
$nombreCiclo = $datosEstudiante['nombreCiclo'] ?? 'SIN ASIGNAR';

$retos = ($idCiclo > 0) ? listarRetosPorCiclo($idCiclo) : [];

$tituloDelPagina = "AULAPRO | MIS RETOS";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS RETOS</h1>
    <p class="subtitulo">Retos asignados a tu ciclo: <?= $nombreCiclo ?></p>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
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
                        <td colspan="4" class="vacio">No hay retos registrados para este ciclo formativo.</td>
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

