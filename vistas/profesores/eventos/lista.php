<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/eventos.php";
$eventos = listarEventosProximos();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "AULAPRO | CALENDARIO DE EVENTOS";
$seccionActual = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>PRÓXIMOS EVENTOS DEL CENTRO</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <div class="tcont">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Evento</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($eventos)) { ?>
                    <tr><td colspan="4" class="vacio">No hay eventos programados.</td></tr>
                <?php } else { ?>
                    <?php foreach ($eventos as $ev) { ?>
                    <tr>
                        <td class="texto-negrita"><?= date('d/m/Y', strtotime($ev['fechaEvento'])) ?></td>
                        <td><?= date('H:i', strtotime($ev['horaEvento'])) ?>h</td>
                        <td>
                            <b><?= $ev['tituloEvento'] ?></b><br>
                            <small class="atenuado"><?= $ev['descripcionEvento'] ?></small>
                        </td>
                        <td><?= $ev['ubicacionEvento'] ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




