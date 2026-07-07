<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_gastos');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$anyoActual   = (int)date('Y');
$anyo         = isset($_GET['anyo']) ? (int)$_GET['anyo'] : $anyoActual;
$idCategoria  = isset($_GET['idCategoria']) ? (int)$_GET['idCategoria'] : 0;
$idCiclo      = isset($_GET['idCiclo']) ? (int)$_GET['idCiclo'] : 0;

$gastos       = listarGastos($anyo, $idCategoria ?: null, $idCiclo ?: null);
$categorias   = listarCategorias();
$ciclos       = listarTodosLosCiclos();
$resumen      = resumenPresupuestoPorCategoria($anyo);
$totalAnyo    = totalGastadoEnAnyo($anyo);
$totalMes     = totalGastadoEnMes($anyoActual, (int)date('n'));
$presTotal    = array_sum(array_column($resumen, 'presupuestoAnual'));

$titulo_pagina = "AULAPRO | GASTOS";
$seccion = 'gastos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>GASTOS DEL CENTRO</h1>
        <p class="subtitulo-encabezado">Seguimiento de gastos y presupuesto anual</p>
    </div>
    <div class="acciones-pagina">
        <a href="categorias.php" class="boton-secundario">
            <i class="fas fa-tags"></i> Categorías
        </a>
        <a href="agregarGasto.php" class="boton-primario">
            <i class="fas fa-plus"></i> Nuevo Gasto
        </a>
    </div>
</div>

<!-- ── KPI cards ────────────────────────────────────────────────── -->
<div class="gastos-kpis">
    <div class="gasto-kpi-card">
        <div class="gasto-kpi-ico"><i class="fas fa-calendar-alt"></i></div>
        <div class="gasto-kpi-body">
            <span class="gasto-kpi-label">Gastado este mes</span>
            <span class="gasto-kpi-valor"><?= number_format($totalMes, 2, ',', '.') ?> €</span>
        </div>
    </div>
    <div class="gasto-kpi-card">
        <div class="gasto-kpi-ico" style="background:rgba(79,70,229,.12);color:var(--accent)"><i class="fas fa-chart-pie"></i></div>
        <div class="gasto-kpi-body">
            <span class="gasto-kpi-label">Total <?= $anyo ?></span>
            <span class="gasto-kpi-valor"><?= number_format($totalAnyo, 2, ',', '.') ?> €</span>
        </div>
    </div>
    <div class="gasto-kpi-card">
        <div class="gasto-kpi-ico" style="background:rgba(16,185,129,.12);color:#10b981"><i class="fas fa-wallet"></i></div>
        <div class="gasto-kpi-body">
            <span class="gasto-kpi-label">Presupuesto total <?= $anyo ?></span>
            <span class="gasto-kpi-valor"><?= number_format($presTotal, 2, ',', '.') ?> €</span>
        </div>
    </div>
    <div class="gasto-kpi-card">
        <div class="gasto-kpi-ico" style="background:<?= ($totalAnyo > $presTotal && $presTotal > 0) ? 'rgba(239,68,68,.12)' : 'rgba(16,185,129,.12)' ?>;color:<?= ($totalAnyo > $presTotal && $presTotal > 0) ? '#ef4444' : '#10b981' ?>">
            <i class="fas <?= ($totalAnyo > $presTotal && $presTotal > 0) ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
        </div>
        <div class="gasto-kpi-body">
            <span class="gasto-kpi-label">Disponible</span>
            <span class="gasto-kpi-valor" style="color:<?= ($presTotal - $totalAnyo < 0) ? '#ef4444' : 'inherit' ?>">
                <?= number_format($presTotal - $totalAnyo, 2, ',', '.') ?> €
            </span>
        </div>
    </div>
</div>

<!-- ── Budget bars per category ─────────────────────────────────── -->
<?php if (!empty($resumen)): ?>
<div class="panel margen-abajo">
    <div class="presupuesto-header">
        <h3 class="presupuesto-titulo"><i class="fas fa-chart-bar"></i> Presupuesto por Categoría — <?= $anyo ?></h3>
        <span class="presupuesto-anyo-nav">
            <a href="?anyo=<?= $anyo - 1 ?>" class="boton-secundario boton-pequeno"><i class="fas fa-chevron-left"></i></a>
            <strong><?= $anyo ?></strong>
            <?php if ($anyo < $anyoActual): ?>
            <a href="?anyo=<?= $anyo + 1 ?>" class="boton-secundario boton-pequeno"><i class="fas fa-chevron-right"></i></a>
            <?php else: ?><span class="boton-secundario boton-pequeno" style="opacity:.3;cursor:default"><i class="fas fa-chevron-right"></i></span><?php endif; ?>
        </span>
    </div>
    <div class="presupuesto-grid">
        <?php foreach ($resumen as $cat):
            $pct    = $cat['presupuestoAnual'] > 0 ? min(100, round(($cat['gastado'] / $cat['presupuestoAnual']) * 100)) : 0;
            $over   = $cat['presupuestoAnual'] > 0 && $cat['gastado'] > $cat['presupuestoAnual'];
            $barColor = $over ? '#ef4444' : ($pct >= 80 ? '#f59e0b' : $cat['color']);
        ?>
        <div class="presupuesto-cat">
            <div class="presupuesto-cat-header">
                <span class="presupuesto-cat-dot" style="background:<?= Security::escapeHtml($cat['color']) ?>"></span>
                <span class="presupuesto-cat-name"><?= Security::escapeHtml($cat['nombre']) ?></span>
                <?php if ($over): ?>
                    <span class="texto-estado rojo" style="margin-left:auto;font-size:10px">SOBREPASADO</span>
                <?php endif; ?>
            </div>
            <div class="presupuesto-barra-wrap">
                <div class="presupuesto-barra" style="width:<?= $pct ?>%;background:<?= Security::escapeHtml($barColor) ?>"></div>
            </div>
            <div class="presupuesto-cat-nums">
                <span><?= number_format($cat['gastado'], 2, ',', '.') ?> €</span>
                <span class="texto-suave">/ <?= number_format($cat['presupuestoAnual'], 2, ',', '.') ?> € (<?= $pct ?>%)</span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ── Filters ───────────────────────────────────────────────────── -->
<div class="panel margen-abajo">
    <form method="GET" id="form-filtros-gastos" class="caja caja-libre espacio-grande" style="align-items:flex-end;">
        <div class="campo relleno">
            <label>AÑO</label>
            <input type="number" name="anyo" id="filtro-anyo" value="<?= $anyo ?>" min="2020" max="<?= $anyoActual + 1 ?>" style="width:100px;">
        </div>
        <div class="campo relleno">
            <label>CATEGORÍA</label>
            <select name="idCategoria" id="filtro-categoria">
                <option value="">— Todas —</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int)$cat['idCategoria'] ?>" <?= $idCategoria == $cat['idCategoria'] ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($cat['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo relleno">
            <label>CICLO</label>
            <select name="idCiclo" id="filtro-ciclo">
                <option value="">— Todos —</option>
                <?php foreach ($ciclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>" <?= $idCiclo == $c['idCiclo'] ? 'selected' : '' ?>>
                    [<?= Security::escapeHtml($c['abreviaturaCiclo'] ?: $c['idCiclo']) ?>] <?= Security::escapeHtml($c['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo relleno">
            <label>BUSCAR</label>
            <input type="text" id="filtro-busqueda-gastos" placeholder="Concepto, referencia…" style="min-width:160px;">
        </div>
        <div class="campo">
            <button type="submit" class="boton-primario"><i class="fas fa-filter"></i> Filtrar</button>
        </div>
    </form>
</div>

<!-- ── Table ─────────────────────────────────────────────────────── -->
<div class="panel">
    <?php if (empty($gastos)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-receipt"></i></div>
        <p class="panel-vacio-titulo">Sin gastos registrados</p>
        <p class="panel-vacio-desc">Añade el primer gasto del centro para este período.</p>
        <a href="agregarGasto.php" class="boton-primario"><i class="fas fa-plus"></i> Añadir Gasto</a>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaGastos">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>CONCEPTO</th>
                    <th>CATEGORÍA</th>
                    <th>CICLO</th>
                    <th>TIPO</th>
                    <th>IMPORTE</th>
                    <th>JUSTIFICANTE</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $g): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($g['fecha'])) ?></td>
                    <td>
                        <b><?= Security::escapeHtml($g['concepto']) ?></b>
                        <?php if (!empty($g['numeroReferencia'])): ?>
                            <br><small class="texto-suave">Ref: <?= Security::escapeHtml($g['numeroReferencia']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="gasto-cat-chip" style="--cat-color:<?= Security::escapeHtml($g['color']) ?>">
                            <?= Security::escapeHtml($g['nombreCategoria']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($g['abreviaturaCiclo'])): ?>
                            <span class="texto-estado azul"><?= Security::escapeHtml($g['abreviaturaCiclo']) ?></span>
                        <?php else: ?>
                            <span class="texto-suave">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $tipoLabels = ['factura'=>'Factura','ticket'=>'Ticket','recibo'=>'Recibo','otro'=>'Otro'];
                        $tipoClases = ['factura'=>'azul','ticket'=>'verde','recibo'=>'naranja','otro'=>'gris'];
                        $t = $g['tipoJustificante'];
                        ?>
                        <span class="texto-estado <?= $tipoClases[$t] ?? 'gris' ?>"><?= $tipoLabels[$t] ?? Security::escapeHtml($t) ?></span>
                    </td>
                    <td><b><?= number_format($g['importe'], 2, ',', '.') ?> €</b></td>
                    <td>
                        <?php if (!empty($g['archivoJustificante'])): 
                            $archivos = json_decode($g['archivoJustificante'], true);
                            if (is_array($archivos)):
                                foreach ($archivos as $i => $arch): ?>
                                    <a href="../../../public/uploads/justificantes/<?= Security::escapeHtml($arch) ?>"
                                       target="_blank" rel="noopener" class="boton-secundario boton-pequeno" style="margin-bottom:2px; display:inline-block;">
                                        <i class="fas fa-file-alt"></i> Ver <?= count($archivos)>1 ? ($i+1) : '' ?>
                                    </a>
                                <?php endforeach;
                            else: ?>
                                <a href="../../../public/uploads/justificantes/<?= Security::escapeHtml($g['archivoJustificante']) ?>"
                                   target="_blank" rel="noopener" class="boton-secundario boton-pequeno">
                                    <i class="fas fa-file-alt"></i> Ver
                                </a>
                            <?php endif;
                        else: ?>
                            <span class="texto-suave">Sin archivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="modificarGasto.php?idGasto=<?= (int)$g['idGasto'] ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <div class="recurso-menu-sep"></div>
                                <a class="recurso-menu-item peligro" href="#"
                                   data-modal-borrar
                                   data-id="<?= (int)$g['idGasto'] ?>"
                                   data-tipo="Gasto"
                                   data-nombre="<?= Security::escapeHtml($g['concepto']) ?>"
                                   data-url="/controladores/admin/gastos/borrar.php"
                                   data-campo="idGasto"
                                   data-aviso="Se eliminará el gasto y su justificante adjunto.">
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

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaGastos', 15);

// Auto-submit when category or ciclo select changes
$('#filtro-categoria, #filtro-ciclo').on('change', function () {
    $('#form-filtros-gastos').submit();
});

// Auto-submit when year input is committed (blur or Enter)
$('#filtro-anyo').on('change', function () {
    $('#form-filtros-gastos').submit();
});

// Client-side text search on the loaded table rows
filtrarTabla('filtro-busqueda-gastos', 'tablaGastos');
</script>
