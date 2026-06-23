<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/inventario.php";

$articulos = listarArticulos();

$titulo_pagina = "AULAPRO | INVENTARIO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>INVENTARIO</h1>
    <a href="gestionarPrestamos.php" class="boton-secundario"><i class="fas fa-hand-holding"></i> GESTIONAR PRÉSTAMOS</a>
</div>

<div class="panel margen-abajo">
    <div class="filtros">
        <input type="text" id="filtroInventario" class="filtro-input" placeholder="Buscar artículo...">
    </div>
</div>

<div class="panel">
    <?php if (empty($articulos)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-box-open"></i></div>
        <div class="panel-vacio-titulo">Inventario vacío</div>
        <div class="panel-vacio-desc">No hay artículos registrados en el inventario.</div>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaInventario">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Nº Serie</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articulos as $art): ?>
                <tr>
                    <td><?= Security::escapeHtml($art['nombreArticulo']) ?></td>
                    <td><code><?= Security::escapeHtml($art['numeroSerie'] ?? '—') ?></code></td>
                    <td>
                        <?php
                        $estado = $art['estado'] ?? 'disponible';
                        $col = ($estado === 'disponible') ? 'verde' : 'naranja';
                        ?>
                        <span class="texto-estado <?= $col ?>"><?= Security::escapeHtml(ucfirst($estado)) ?></span>
                    </td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="agregarPrestamo.php?id=<?= (int)$art['idArticulo'] ?>">
                                    <i class="fas fa-hand-holding"></i> Prestar
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

<?php include '../comunes/footer.php'; ?>
<script>
filtrarTabla('filtroInventario', 'tablaInventario');
iniciarPaginacion('tablaInventario', 15);
</script>
