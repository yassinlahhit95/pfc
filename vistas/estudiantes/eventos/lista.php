<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/eventos.php";
$eventos = listarEventosProximos();

$tituloDelPagina = "Calendario de Eventos - Portal Estudiantes";
$seccionActual = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Próximos Eventos y Fechas Clave</h1>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($eventos)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay eventos programados próximamente.</td></tr>
                <?php } else { ?>
                    <?php foreach ($eventos as $ev) { ?>
                    <tr>
                        <td class="texto-negrita"><?= date('d/m/Y', strtotime($ev['fechaEvento'])) ?></td>
                        <td><?= date('H:i', strtotime($ev['horaEvento'])) ?>h</td>
                        <td><strong><?= strtoupper($ev['tituloEvento']) ?></strong></td>
                        <td><p class="texto-pequeno"><?= $ev['descripcionEvento'] ?></p></td>
                        <td><?= $ev['ubicacionEvento'] ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>



