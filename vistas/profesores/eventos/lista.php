<?php
session_start();
if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once "../../../modelos/eventos.php";
$eventos = listarEventosProximos();

$tituloDelPagina = "Calendario de Eventos - Portal Profesores";
$seccionActual = 'eventos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Próximos Eventos del Centro</h1>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
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
                    <tr><td colspan="4" class="sin-datos">No hay eventos programados.</td></tr>
                <?php } else { ?>
                    <?php foreach ($eventos as $ev) { ?>
                    <tr>
                        <td class="texto-negrita"><?php echo date('d/m/Y', strtotime($ev['fechaEvento'])); ?></td>
                        <td><?php echo date('H:i', strtotime($ev['horaEvento'])); ?>h</td>
                        <td>
                            <strong><?php echo $ev['tituloEvento']; ?></strong><br>
                            <small class="texto-atenuado"><?php echo $ev['descripcionEvento']; ?></small>
                        </td>
                        <td><?php echo $ev['ubicacionEvento']; ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

