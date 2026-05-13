<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "AULAPRO | GESTIÓN DE EVENTOS";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/eventos.php";

$todos_los_eventos = listarEventosProximos();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>PRÓXIMOS EVENTOS</h1>
    <a href="agregarEvento.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO EVENTO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-arriba">
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
                    <tr><td colspan="4" class="sin-datos">No hay eventos próximos programados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_eventos as $evento) { ?>
                    <tr>
                        <td>
                            <strong><?= date('d/m/Y', strtotime($evento['fechaEvento'])) ?></strong><br>
                            <small class="texto-atenuado"><?= date('H:i', strtotime($evento['horaEvento'])) ?>h</small>
                        </td>
                        <td>
                            <span class="texto-negrita"><?= $evento['tituloEvento'] ?></span><br>
                            <small><?= substr($evento['descripcionEvento'], 0, 80) ?>...</small>
                        </td>
                        <td><?= $evento['ubicacionEvento'] ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarEvento.php?idEvento=<?= $evento['idEvento'] ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/eventos/borrar.php" method="POST" onsubmit="return confirm('¿Eliminar este evento?')">
                                    <input type="hidden" name="idEvento" value="<?= $evento['idEvento'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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


