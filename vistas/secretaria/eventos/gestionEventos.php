<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/eventos.php";

$eventos = listarTodosLosEventos();

$titulo_pagina = "AULAPRO | EVENTOS";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVENTOS</h1>
    <a href="agregarEvento.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO EVENTO</a>
</div>

<div class="panel margen-abajo">
    <div class="formulario">
        <div class="campo ancho-total">
            <label for="filtroEventos">BUSCAR</label>
            <input type="text" id="filtroEventos" placeholder="Buscar por título, fecha o ubicación..."
                   autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other">
        </div>
    </div>
</div>

<div class="panel">
    <?php if (empty($eventos)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-calendar-xmark"></i></div>
        <div class="panel-vacio-titulo">No hay eventos</div>
        <div class="panel-vacio-desc">Crea el primer evento para que aparezca aquí.</div>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEventos">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventos as $evento): ?>
                <tr>
                    <td><?= Security::escapeHtml($evento['tituloEvento']) ?></td>
                    <td><?= Security::escapeHtml(date('d/m/Y', strtotime($evento['fechaEvento']))) ?></td>
                    <td><?= Security::escapeHtml(substr($evento['horaEvento'] ?? '', 0, 5)) ?></td>
                    <td><?= Security::escapeHtml($evento['ubicacionEvento'] ?? '—') ?></td>
                    <td>
                        <?php if ($evento['fechaEvento'] === date('Y-m-d')): ?>
                            <span class="texto-estado naranja">Hoy</span>
                        <?php elseif ($evento['fechaEvento'] > date('Y-m-d')): ?>
                            <span class="texto-estado verde">Próximo</span>
                        <?php else: ?>
                            <span class="texto-estado gris">Celebrado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="modificarEvento.php?idEvento=<?= (int)$evento['idEvento'] ?>">
                                    <i class="fas fa-pen"></i> Editar
                                </a>
                                <div class="recurso-menu-sep"></div>
                                <a class="recurso-menu-item peligro" href="#"
                                   data-modal-borrar
                                   data-id="<?= (int)$evento['idEvento'] ?>"
                                   data-tipo="Evento"
                                   data-nombre="<?= Security::escapeHtml($evento['tituloEvento']) ?>"
                                   data-url="/controladores/secretaria/eventos/borrar.php"
                                   data-campo="idEvento">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaEventos', 15);
// Filtrado en vivo: se ejecuta en cada pulsación
document.getElementById('filtroEventos').addEventListener('input', function () {
    filtrarTabla('filtroEventos', 'tablaEventos');
});
</script>
