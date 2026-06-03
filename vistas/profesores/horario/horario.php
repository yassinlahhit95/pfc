<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";

// Solo los ciclos del profesor (donde tutoriza o imparte algun modulo)
$ciclos = listarCiclosDeProfesor($idProfesor);

// Restringir la seleccion a los ciclos propios (no fiarse del ?ciclo= a mano)
$idsPermitidos  = array_column($ciclos, 'idCiclo');
$idCicloPedido  = isset($_GET['ciclo']) ? (int)$_GET['ciclo'] : 0;
$idCicloHorario = in_array($idCicloPedido, $idsPermitidos) ? $idCicloPedido : (int)($ciclos[0]['idCiclo'] ?? 0);

$horarioCeldas = $idCicloHorario ? listarHorarioPorCiclo($idCicloHorario) : [];
$puedeEditar   = false;

$tituloDelPagina = "AULAPRO | CUADRO HORARIO";
$seccionActual = 'horario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CUADRO HORARIO</h1>
    <form method="GET" class="horario-selector-form">
        <label for="ciclo">Ciclo:</label>
        <select name="ciclo" id="ciclo" onchange="this.form.submit()">
            <?php foreach ($ciclos as $c) { ?>
                <option value="<?= Security::escapeHtml($c['idCiclo']) ?>" <?= ($c['idCiclo'] == $idCicloHorario) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($c['nombreCiclo']) ?> (<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>)
                </option>
            <?php } ?>
        </select>
    </form>
</div>

<?php if (empty($ciclos)) { ?>
    <div class="panel"><p class="vacio">No tienes ciclos asignados.</p></div>
<?php } else { ?>
<div class="horario-contenido horario-solo-lectura">
    <?php include __DIR__ . "/../../../include/horario-tabla.php"; ?>
</div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
