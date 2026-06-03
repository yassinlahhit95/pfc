<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/eventos.php";

$todos_los_eventos = listarEventosProximos();

$titulo_pagina = "AULAPRO | GESTIÓN DE EVENTOS";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>PRÓXIMOS EVENTOS</h1>
    <a href="agregarEvento.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO EVENTO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Eventos Programados</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEventos">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Evento</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_eventos)) { ?>
                    <tr><td colspan="4" class="vacio">No hay eventos próximos programados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_eventos as $evento) { ?>
                    <tr>
                        <td>
                            <b><?= date('d/m/Y', strtotime($evento['fechaEvento'])) ?></b><br>
                            <span class="texto-suave"><?= date('H:i', strtotime($evento['horaEvento'])) ?>h</span>
                        </td>
                        <td>
                            <span class="texto-negrita"><?= $evento['tituloEvento'] ?></span><br>
                            <span><?= substr($evento['descripcionEvento'], 0, 80) ?>...</span>
                        </td>
                        <td><?= $evento['ubicacionEvento'] ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="modificarEvento.php?idEvento=<?= $evento['idEvento'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarEvento.php?id=<?= $evento['idEvento'] ?>" onclick="return confirm('¿Eliminar este evento?')"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaEventos', 10);
</script>

