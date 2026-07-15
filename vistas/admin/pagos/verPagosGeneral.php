<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_pagos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeTodosLosCiclos = listarTodosLosCiclos();
$listaNiveles          = listarNiveles();

$idNivelFiltro = (int)($_GET['idNivel'] ?? 0);
$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($listaDeTodosLosCiclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
    : $listaDeTodosLosCiclos;

$idDelCicloParaFiltrar = (int)($_GET['idCiclo'] ?? 0);

if ($idNivelFiltro && $idDelCicloParaFiltrar && !in_array((int)$idDelCicloParaFiltrar, array_column($ciclosFiltrados, 'idCiclo'))) {
    $idDelCicloParaFiltrar = '';
}

$listaDePagosAMostrar = $idDelCicloParaFiltrar
    ? listarPagosFiltrados($idDelCicloParaFiltrar)
    : listarTodosLosPagos();

// El modal de pendientes es independiente de los filtros de la página:
// muestra estudiantes con saldo pendiente (precio del ciclo - pagos realizados),
// igual que el widget del dashboard, para que ambos conteos coincidan.
$listaPendientes = listarEstudiantesConPagosPendientes();

$titulo_pagina = "AULAPRO | GESTIÓN DE PAGOS";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
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
        <a href="agregarPagos.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO PAGO
        </a>
    </div>
</div>


<div class="panel margen-abajo">
    <form method="GET" action="verPagosGeneral.php">
        <div class="caja al-final espacio-grande">
            <div class="campo relleno">
                <label>FILTRAR POR NIVEL:</label>
                <select name="idNivel" onchange="this.form.submit()">
                    <option value="">-- Todos los Niveles --</option>
                    <?php foreach ($listaNiveles as $n) { ?>
                        <option value="<?= (int)$n['idNivel'] ?>" <?= ((int)$n['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($n['nombreNivel']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="campo relleno">
                <label>FILTRAR POR CICLO FORMATIVO:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los Ciclos --</option>
                    <?php foreach ($ciclosFiltrados as $cicloItem) { ?>
                        <option value="<?= (int)$cicloItem['idCiclo'] ?>" <?= (int)$idDelCicloParaFiltrar === (int)$cicloItem['idCiclo'] ? 'selected' : '' ?>>
                            <?= mb_strtoupper(Security::escapeHtml($cicloItem['nombreCiclo']), 'UTF-8') ?>
                        </option>
                    <?php } ?>
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
                <?php if (empty($listaDePagosAMostrar)) { ?>
                    <tr>
                        <td colspan="7" class="vacio">No hay registros de pagos que coincidan con la búsqueda.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDePagosAMostrar as $pagoIndividual) { ?>
                    <tr>
                        <td><b><?= mb_strtoupper(Security::escapeHtml($pagoIndividual['nombreEstudiante']), 'UTF-8') ?></b></td>
                        <td data-campo="curso"><?= Security::escapeHtml($pagoIndividual['curso'] ?? '1º') ?> Año</td>
                        <td><?= mb_strtoupper(Security::escapeHtml($pagoIndividual['nombreCiclo']), 'UTF-8') ?></td>
                        <td>
                            <span class="texto-pago"><?= mb_strtoupper(Security::escapeHtml($pagoIndividual['tipoPago']), 'UTF-8') ?></span>
                        </td>
                        <td class="texto-negrita"><?= number_format($pagoIndividual['monto'], 2) ?> €</td>
                        <td><?= date('d/m/Y', strtotime($pagoIndividual['fechaPago'])) ?></td>
                        <td>
                            <?php if ($pagoIndividual['tipoPago'] == 'unico') { ?>
                                <span class="texto-gris">N/A (PAGO ÚNICO)</span>
                            <?php } else { ?>
                                <?= date('d/m/Y', strtotime($pagoIndividual['fechaProximoPago'])) ?>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="historialEstudiante.php?idEstudiante=<?= (int)$pagoIndividual['idEstudiante'] ?>"><i class="fas fa-history"></i> Historial</a>
                                    <a class="recurso-menu-item" href="modificarPagos.php?idPago=<?= (int)$pagoIndividual['idPago'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$pagoIndividual['idPago'] ?>"
                                       data-tipo="Pago"
                                       data-nombre="<?= Security::escapeHtml($pagoIndividual['nombreEstudiante'] . ' — ' . $pagoIndividual['tipoPago'] . ' ' . number_format($pagoIndividual['monto'], 2) . ' €') ?>"
                                       data-url="/controladores/admin/pagos/borrar.php"
                                       data-campo="idPago"><i class="fas fa-trash"></i> Eliminar</a>
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

<!-- Modal Pagos Pendientes -->
<div id="modal-pendientes" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-caja" style="max-width:800px; width:90%;">
        <div class="modal-icono" style="background:#fee2e2;">
            <i class="fas fa-exclamation-circle" style="color:var(--danger)"></i>
        </div>
        <h3 class="modal-titulo">Estudiantes con Pagos Pendientes</h3>
        <p class="modal-subtitulo">Saldo pendiente (precio del ciclo menos pagos realizados)</p>
        <div class="contenedor-tabla" style="margin-top:20px; max-height:400px; overflow-y:auto;">
            <table class="tabla-datos" style="font-size:13px;">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Ciclo</th>
                        <th>Precio Ciclo</th>
                        <th>Pagado</th>
                        <th>Deuda</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaPendientes)): ?>
                    <tr><td colspan="6" class="vacio">No hay estudiantes con pagos pendientes. ¡Todos están al día!</td></tr>
                    <?php else: foreach ($listaPendientes as $p): ?>
                    <tr>
                        <td><b><?= Security::escapeHtml(mb_strtoupper($p['nombreEstudiante'], 'UTF-8')) ?></b></td>
                        <td><?= Security::escapeHtml(mb_strtoupper($p['nombreCiclo'], 'UTF-8')) ?></td>
                        <td><?= number_format((float)$p['precioCiclo'], 2) ?> €</td>
                        <td><?= number_format((float)$p['totalPagado'], 2) ?> €</td>
                        <td class="texto-rojo texto-negrita"><?= number_format((float)$p['deuda'], 2) ?> €</td>
                        <td><a href="agregarPagos.php?idEstudiante=<?= (int)$p['idEstudiante'] ?>" class="boton-primario" style="padding:4px 12px;font-size:.8rem;">Cobrar</a></td>
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

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaPagos', 15);
</script>

