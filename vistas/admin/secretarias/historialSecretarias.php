<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/secretarias.php';
require_once __DIR__ . '/../../../modelos/log.php';

$idFiltro    = (int)($_GET['id'] ?? 0);
$secretarias = listarTodasLasSecretarias();
$historial   = listarHistorialSecretarias($idFiltro ?: null, 300);

$filtroNombre = '';
if ($idFiltro) {
    foreach ($secretarias as $s) {
        if ((int)$s['idSecretaria'] === $idFiltro) {
            $filtroNombre = $s['nombreSecretaria'];
            break;
        }
    }
}

$titulo_pagina = 'AULAPRO | HISTORIAL DE SECRETARIAS';
$seccion = 'secretarias';
include_once __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>HISTORIAL DE ACCIONES<?= $filtroNombre ? ' — ' . Security::escapeHtml(strtoupper($filtroNombre)) : '' ?></h1>
    <a href="verSecretarias.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> SECRETARIAS
    </a>
</div>

<div class="panel margen-abajo">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <div class="campo" style="min-width:200px;margin:0;">
            <label>Filtrar por secretaria</label>
            <select name="id" onchange="this.form.submit()">
                <option value="">— Todas —</option>
                <?php foreach ($secretarias as $s): ?>
                    <option value="<?= (int)$s['idSecretaria'] ?>"
                        <?= $idFiltro === (int)$s['idSecretaria'] ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($s['nombreSecretaria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo" style="margin:0;">
            <label>&nbsp;</label>
            <input type="text" id="buscarHistorial"
                   placeholder="Buscar en historial…"
                   oninput="filtrarTabla('buscarHistorial','tablaHistorial')"
                   style="min-width:220px;">
        </div>
    </form>
</div>

<?php if (empty($historial)): ?>
<div class="panel-vacio">
    <div class="panel-vacio-icono"><i class="fas fa-history"></i></div>
    <p class="panel-vacio-titulo">Sin acciones registradas</p>
    <p class="panel-vacio-desc">
        Las acciones de las secretarias (altas de estudiantes, pagos, avisos, eventos…) aparecerán aquí.
    </p>
</div>
<?php else: ?>
<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaHistorial">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>SECRETARIA</th>
                    <th>ACCIÓN</th>
                    <th>SECCIÓN</th>
                    <th>DETALLE</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $h):
                    $colorAccion = match($h['accion']) {
                        'insertar'   => 'verde',
                        'actualizar' => 'azul',
                        'borrar'     => 'rojo',
                        default      => 'gris',
                    };
                    $iconAccion = match($h['accion']) {
                        'insertar'   => 'fa-plus-circle',
                        'actualizar' => 'fa-pen',
                        'borrar'     => 'fa-trash',
                        default      => 'fa-circle',
                    };
                ?>
                <tr>
                    <td style="white-space:nowrap;font-size:.83rem;">
                        <?= date('d/m/Y H:i', strtotime($h['fecha'])) ?>
                    </td>
                    <td>
                        <?php if (!empty($h['nombreSecretaria'])): ?>
                            <span style="font-weight:600;"><?= Security::escapeHtml($h['nombreSecretaria']) ?></span>
                        <?php else: ?>
                            <span class="texto-estado gris">Desconocida</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="texto-estado <?= $colorAccion ?>">
                            <i class="fas <?= $iconAccion ?>"></i>
                            <?= Security::escapeHtml(ucfirst($h['accion'])) ?>
                        </span>
                    </td>
                    <td><?= Security::escapeHtml(ucfirst(str_replace('_', ' ', $h['tabla']))) ?></td>
                    <td><?= Security::escapeHtml($h['descripcion'] ?? '—') ?></td>
                    <td style="font-size:.8rem;color:var(--dim);">
                        <?= Security::escapeHtml($h['ip'] ?? '—') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaHistorial', 20);
</script>
