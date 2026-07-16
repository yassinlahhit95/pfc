<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaTodosLosCiclos = listarTodosLosCiclos();
$listaNiveles        = listarNiveles();

$idNivelFiltro = (int)($_GET['idNivel'] ?? 0);
$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($listaTodosLosCiclos, fn($ciclo) => (int)$ciclo['idNivel'] === $idNivelFiltro))
    : $listaTodosLosCiclos;

$idCicloFiltro = (int)($_GET['idCiclo'] ?? 0);
if ($idNivelFiltro && $idCicloFiltro && !in_array($idCicloFiltro, array_column($ciclosFiltrados, 'idCiclo'))) {
    $idCicloFiltro = 0;
}

$listaPagos = $idCicloFiltro ? listarPagosFiltrados($idCicloFiltro) : listarTodosLosPagos();

// El modal de pendientes es independiente de los filtros de la página:
// siempre muestra todos los vencidos reales (último pago por estudiante).
$listaPendientes = listarPagosPendientes();

$titulo_pagina = 'AULAPRO | PAGOS';
$seccion = 'pagos';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>GESTIÓN DE PAGOS</h1>
    <div class="acciones-cabecera" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <button type="button" class="boton-secundario" onclick="$('#modal-pendientes').addClass('modal-abierto').removeClass('modal-cerrando')"
                style="border-color:var(--danger); color:var(--danger); display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-exclamation-circle"></i> PAGOS PENDIENTES
            <?php if (count($listaPendientes) > 0): ?>
            <span style="background:var(--danger);color:#fff;border-radius:999px;padding:1px 8px;font-size:.75rem;font-weight:700;"><?= count($listaPendientes) ?></span>
            <?php endif; ?>
        </button>
        <a href="agregarPago.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO PAGO</a>
    </div>
</div>

<div class="panel margen-abajo">
    <form method="GET" action="verPagos.php">
        <div class="caja al-final espacio-grande">
            <div class="campo relleno">
                <label>FILTRAR POR NIVEL:</label>
                <select name="idNivel" onchange="this.form.submit()">
                    <option value="">-- Todos los Niveles --</option>
                    <?php foreach ($listaNiveles as $nivel): ?>
                        <option value="<?= (int)$nivel['idNivel'] ?>" <?= (int)$nivel['idNivel'] === $idNivelFiltro ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($nivel['nombreNivel']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo relleno">
                <label>FILTRAR POR CICLO:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los Ciclos --</option>
                    <?php foreach ($ciclosFiltrados as $ciclo): ?>
                        <option value="<?= (int)$ciclo['idCiclo'] ?>" <?= (int)$ciclo['idCiclo'] === $idCicloFiltro ? 'selected' : '' ?>>
                            <?= mb_strtoupper(Security::escapeHtml($ciclo['nombreCiclo']), 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo relleno">
                <label>FILTRAR POR CURSO:</label>
                <select id="filtroCursoPagos" data-filtro-tabla="tablaPagos" data-filtro-campo="curso" onchange="filtrarTablaMulti('tablaPagos')">
                    <option value="">-- Todos los Cursos --</option>
                    <option value="1º">1º Año</option>
                    <option value="2º">2º Año</option>
                </select>
            </div>
        </div>
    </form>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaPagos">
            <thead>
                <tr>
                    <th>ESTUDIANTE</th>
                    <th>CURSO</th>
                    <th>CICLO</th>
                    <th>TIPO</th>
                    <th>CANTIDAD</th>
                    <th>FECHA PAGO</th>
                    <th>PRÓXIMO PAGO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaPagos)): ?>
                    <tr><td colspan="7" class="vacio">No hay registros de pagos que coincidan con los filtros.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaPagos as $pago): ?>
                    <tr>
                        <td><b><?= mb_strtoupper(Security::escapeHtml($pago['nombreEstudiante']), 'UTF-8') ?></b></td>
                        <td data-campo="curso"><?= Security::escapeHtml($pago['curso'] ?? '1º') ?> Año</td>
                        <td><?= mb_strtoupper(Security::escapeHtml($pago['nombreCiclo']), 'UTF-8') ?></td>
                        <td><span class="texto-estado azul"><?= mb_strtoupper(Security::escapeHtml($pago['tipoPago']), 'UTF-8') ?></span></td>
                        <td><b><?= number_format((float)$pago['monto'], 2) ?> €</b></td>
                        <td><?= date('d/m/Y', strtotime($pago['fechaPago'])) ?></td>
                        <td>
                            <?php if (strtolower(trim($pago['tipoPago'])) === 'unico'): ?>
                                <span style="color:var(--mut)">N/A</span>
                            <?php else: ?>
                                <?= !empty($pago['fechaProximoPago']) ? date('d/m/Y', strtotime($pago['fechaProximoPago'])) : '—' ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="../../../vistas/secretaria/pagos/historialEstudiante.php?idEstudiante=<?= (int)$pago['idEstudiante'] ?>">
                                        <i class="fas fa-history"></i> Historial
                                    </a>
                                    <a class="recurso-menu-item" href="../../../vistas/secretaria/pagos/modificarPagos.php?idPago=<?= (int)$pago['idPago'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$pago['idPago'] ?>"
                                       data-tipo="Pago"
                                       data-nombre="<?= Security::escapeHtml($pago['nombreEstudiante'] . ' — ' . $pago['tipoPago'] . ' ' . number_format((float)$pago['monto'], 2) . ' €') ?>"
                                       data-url="/controladores/secretaria/pagos/borrar.php"
                                       data-campo="idPago">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pagos Pendientes -->
<div id="modal-pendientes" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-caja" style="max-width:800px; width:90%;">
        <div class="modal-icono" style="background:#fee2e2;">
            <i class="fas fa-exclamation-circle" style="color:var(--danger)"></i>
        </div>
        <h3 class="modal-titulo">Estudiantes con Pagos Pendientes</h3>
        <p class="modal-subtitulo">Cuotas recurrentes vencidas (último pago de cada estudiante, sin prórroga vigente)</p>
        <div class="contenedor-tabla" style="margin-top:20px; max-height:400px; overflow-y:auto;">
            <table class="tabla-datos" style="font-size:13px;">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Ciclo</th>
                        <th>Tipo</th>
                        <th>Vencido desde</th>
                        <th>Último Pago</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaPendientes)): ?>
                    <tr><td colspan="6" class="vacio">No hay cuotas vencidas. ¡Todos los estudiantes están al día!</td></tr>
                    <?php else: foreach ($listaPendientes as $pago): ?>
                    <tr>
                        <td><b><?= Security::escapeHtml(mb_strtoupper($pago['nombreEstudiante'], 'UTF-8')) ?></b></td>
                        <td><?= Security::escapeHtml(mb_strtoupper($pago['nombreCiclo'], 'UTF-8')) ?></td>
                        <td><span class="texto-estado azul"><?= mb_strtoupper(Security::escapeHtml($pago['tipoPago']), 'UTF-8') ?></span></td>
                        <td class="texto-rojo texto-negrita"><?= date('d/m/Y', strtotime($pago['fechaProximoPago'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($pago['fechaPago'])) ?> · <?= number_format((float)$pago['monto'], 2) ?> €</td>
                        <td>
                            <button type="button" class="boton-secundario" style="padding:4px 10px;font-size:.78rem;"
                                    onclick="otorgarProrrogaPago(<?= (int)$pago['idPago'] ?>)">
                                <i class="fas fa-clock"></i> Prórroga 7 días
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <div class="modal-acciones" style="margin-top:20px;">
            <button class="boton-secundario" onclick="$('#modal-pendientes').addClass('modal-cerrando'); setTimeout(()=>$('#modal-pendientes').removeClass('modal-abierto modal-cerrando'), 200);">Cerrar</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaPagos', 15);

function otorgarProrrogaPago(idPago) {
    var pedir = window.ModalConfirm
        ? ModalConfirm.prompt('¿Otorgar 7 días de prórroga? Esto desbloqueará el acceso del estudiante.', 'Otorgar prórroga')
        : Promise.resolve(confirm('¿Otorgar 7 días de prórroga?'));
    pedir.then(function (ok) {
        if (!ok) return;
        var fd = new FormData();
        fd.append('idPago', idPago);
        fetch('../../../controladores/secretaria/ajax_prorroga.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (window.Toast) Toast.show(res.msg || (res.ok ? 'Prórroga otorgada' : 'Error'), res.ok ? 'success' : 'error');
                if (res.ok) setTimeout(function () { window.location.reload(); }, 900);
            })
            .catch(function () {
                if (window.Toast) Toast.show('Error de conexión', 'error');
            });
    });
}
</script>
