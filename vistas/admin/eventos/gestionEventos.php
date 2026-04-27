<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "Gestión de Eventos - Super Admin";
$seccion = 'eventos';
include_once "../comunes/nav.php";

require_once "../../../modelos/eventos.php";

$todos_los_eventos = listarEventosProximos();

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Próximos Eventos</h1>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error != "") { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Crear Nuevo Evento</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/eventos/insertar.php">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Título del Evento *</label>
                <input type="text" name="tituloEvento" required placeholder="Ej: Examen Final, Reunión de Profesores...">
            </div>

            <div class="campo-formulario">
                <label>Ubicación</label>
                <input type="text" name="ubicacionEvento" placeholder="Ej: Aula 101, Salón de Actos...">
            </div>

            <div class="campo-formulario">
                <label>Fecha *</label>
                <input type="date" name="fechaEvento" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="campo-formulario">
                <label>Hora</label>
                <input type="time" name="horaEvento" value="09:00">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción</label>
                <textarea name="descripcionEvento" rows="3" placeholder="Detalles del evento..."></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarEvento" class="boton-primario">
                <i class="fas fa-calendar-plus"></i> Publicar Evento
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Eventos Programados</h3>
    </div>
    <div class="campo-formulario mb-20">
        <label><i class="fas fa-search"></i> BUSCAR EVENTO:</label>
        <input type="text" id="inputBuscarEv" placeholder="Busque por título o ubicación..." onkeyup="filtrarTabla('inputBuscarEv', 'tablaEventos')">
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
                            <strong><?php echo date('d/m/Y', strtotime($evento['fechaEvento'])); ?></strong><br>
                            <small class="texto-atenuado"><?php echo date('H:i', strtotime($evento['horaEvento'])); ?>h</small>
                        </td>
                        <td>
                            <span class="texto-negrita"><?php echo $evento['tituloEvento']; ?></span><br>
                            <small><?php echo substr($evento['descripcionEvento'], 0, 80); ?>...</small>
                        </td>
                        <td><?php echo $evento['ubicacionEvento']; ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarEvento.php?idEvento=<?php echo $evento['idEvento']; ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/eventos/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este evento?')">
                                    <input type="hidden" name="idEvento" value="<?php echo $evento['idEvento']; ?>">
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

