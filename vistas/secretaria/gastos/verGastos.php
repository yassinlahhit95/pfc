<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$anyoActual  = (int)date('Y');
$anyo        = isset($_GET['anyo']) ? (int)$_GET['anyo'] : $anyoActual;
$idCategoria = isset($_GET['idCategoria']) ? (int)$_GET['idCategoria'] : 0;
$idCiclo     = isset($_GET['idCiclo']) ? (int)$_GET['idCiclo'] : 0;

$gastos     = listarGastos($anyo, $idCategoria ?: null, $idCiclo ?: null);
$categorias = listarCategorias();
$ciclos     = listarTodosLosCiclos();

$totalAnyo = array_sum(array_column($gastos, 'importe'));

$titulo_pagina = 'AULAPRO | GASTOS';
$seccion = 'gastos';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1>GASTOS DEL CENTRO</h1>
        <p class="subtitulo-encabezado">Registro de gastos <?= $anyo ?></p>
    </div>
    <a href="agregarGasto.php" class="boton-primario"><i class="fas fa-plus"></i> Nuevo Gasto</a>
</div>

<div class="panel margen-abajo">
    <form method="GET" action="verGastos.php" class="caja espacio-medio" style="flex-wrap:wrap;gap:12px;">
        <div class="campo relleno">
            <label>Año</label>
            <select name="anyo" onchange="this.form.submit()">
                <?php for ($y = $anyoActual; $y >= $anyoActual - 5; $y--): ?>
                <option value="<?= $y ?>" <?= $y === $anyo ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="campo relleno">
            <label>Categoría</label>
            <select name="idCategoria" onchange="this.form.submit()">
                <option value="">— Todas —</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int)$cat['idCategoria'] ?>" <?= $idCategoria === (int)$cat['idCategoria'] ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($cat['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo relleno">
            <label>Ciclo</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">— Todos —</option>
                <?php foreach ($ciclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>" <?= $idCiclo === (int)$c['idCiclo'] ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($c['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex;align-items:flex-end;gap:8px;">
            <a href="verGastos.php" class="boton-secundario">Limpiar</a>
        </div>
    </form>
</div>

<div class="panel" style="margin-bottom:16px;padding:16px 20px;">
    <b>Total <?= $anyo ?>:</b> <span style="color:var(--accent);font-size:1.2rem;font-weight:700;"><?= number_format($totalAnyo, 2, ',', '.') ?> €</span>
    <?php if (count($gastos) > 0): ?>
    <span class="texto-suave" style="margin-left:12px;"><?= count($gastos) ?> registros</span>
    <?php endif; ?>
</div>

<div class="panel">
    <?php if (empty($gastos)): ?>
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-wallet"></i></div>
            <div class="panel-vacio-titulo">Sin gastos</div>
            <div class="panel-vacio-desc">No hay gastos registrados para los filtros seleccionados.</div>
        </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaGastos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Categoría</th>
                    <th>Ciclo</th>
                    <th>Importe</th>
                    <th>Justificante</th>
                    <th style="width:60px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $g): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($g['fecha'])) ?></td>
                    <td><b><?= Security::escapeHtml($g['concepto']) ?></b></td>
                    <td><?= Security::escapeHtml($g['nombreCategoria'] ?? '—') ?></td>
                    <td><?= Security::escapeHtml($g['nombreCiclo'] ?? '—') ?></td>
                    <td><b><?= number_format((float)$g['importe'], 2, ',', '.') ?> €</b></td>
                    <td><?= Security::escapeHtml($g['tipoJustificante'] ?? '—') ?></td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="modificarGasto.php?id=<?= (int)$g['idGasto'] ?>">
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
iniciarPaginacion('tablaGastos', 15);
</script>
