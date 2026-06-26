<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/eventos.php";

$eventos = listarEventosProximos();

$titulo_pagina = "AULAPRO | EVENTOS";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVENTOS</h1>
    <a href="agregarEvento.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO EVENTO</a>
</div>

<div class="panel margen-abajo">
    <div class="filtros">
        <input type="text" id="filtroEventos" class="filtro-input" placeholder="Buscar evento...">
    </div>
</div>

<div class="panel">
    <?php if (empty($eventos)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-calendar-xmark"></i></div>
        <div class="panel-vacio-titulo">No hay eventos próximos</div>
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventos as $ev): ?>
                <tr>
                    <td><?= Security::escapeHtml($ev['tituloEvento']) ?></td>
                    <td><?= Security::escapeHtml(date('d/m/Y', strtotime($ev['fechaEvento']))) ?></td>
                    <td><?= Security::escapeHtml(substr($ev['horaEvento'] ?? '', 0, 5)) ?></td>
                    <td><?= Security::escapeHtml($ev['ubicacionEvento'] ?? '—') ?></td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="modificarEvento.php?idEvento=<?= (int)$ev['idEvento'] ?>">
                                    <i class="fas fa-pen"></i> Editar
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
filtrarTabla('filtroEventos', 'tablaEventos');
iniciarPaginacion('tablaEventos', 15);
</script>
