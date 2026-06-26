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
    ? array_values(array_filter($listaTodosLosCiclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
    : $listaTodosLosCiclos;

$idCicloFiltro = (int)($_GET['idCiclo'] ?? 0);
if ($idNivelFiltro && $idCicloFiltro && !in_array($idCicloFiltro, array_column($ciclosFiltrados, 'idCiclo'))) {
    $idCicloFiltro = 0;
}

$listaPagos = $idCicloFiltro ? listarPagosFiltrados($idCicloFiltro) : listarTodosLosPagos();

$titulo_pagina = 'AULAPRO | PAGOS';
$seccion = 'pagos';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>GESTIÓN DE PAGOS</h1>
    <a href="agregarPago.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO PAGO</a>
</div>

<div class="panel margen-abajo">
    <form method="GET" action="verPagos.php">
        <div class="caja al-final espacio-grande">
            <div class="campo relleno">
                <label>FILTRAR POR NIVEL:</label>
                <select name="idNivel" onchange="this.form.submit()">
                    <option value="">-- Todos los Niveles --</option>
                    <?php foreach ($listaNiveles as $n): ?>
                        <option value="<?= (int)$n['idNivel'] ?>" <?= (int)$n['idNivel'] === $idNivelFiltro ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($n['nombreNivel']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo relleno">
                <label>FILTRAR POR CICLO:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los Ciclos --</option>
                    <?php foreach ($ciclosFiltrados as $c): ?>
                        <option value="<?= (int)$c['idCiclo'] ?>" <?= (int)$c['idCiclo'] === $idCicloFiltro ? 'selected' : '' ?>>
                            <?= strtoupper(Security::escapeHtml($c['nombreCiclo'])) ?>
                        </option>
                    <?php endforeach; ?>
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
                    <?php foreach ($listaPagos as $p): ?>
                    <tr>
                        <td><b><?= strtoupper(Security::escapeHtml($p['nombreEstudiante'])) ?></b></td>
                        <td><?= strtoupper(Security::escapeHtml($p['nombreCiclo'])) ?></td>
                        <td><span class="texto-estado azul"><?= strtoupper(Security::escapeHtml($p['tipoPago'])) ?></span></td>
                        <td><b><?= number_format((float)$p['monto'], 2) ?> €</b></td>
                        <td><?= date('d/m/Y', strtotime($p['fechaPago'])) ?></td>
                        <td>
                            <?php if (strtolower(trim($p['tipoPago'])) === 'unico'): ?>
                                <span style="color:var(--mut)">N/A</span>
                            <?php else: ?>
                                <?= !empty($p['fechaProximoPago']) ? date('d/m/Y', strtotime($p['fechaProximoPago'])) : '—' ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="../../../vistas/secretaria/pagos/historialEstudiante.php?idEstudiante=<?= (int)$p['idEstudiante'] ?>">
                                        <i class="fas fa-history"></i> Historial
                                    </a>
                                    <a class="recurso-menu-item" href="../../../vistas/secretaria/pagos/modificarPagos.php?idPago=<?= (int)$p['idPago'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$p['idPago'] ?>"
                                       data-tipo="Pago"
                                       data-nombre="<?= Security::escapeHtml($p['nombreEstudiante'] . ' — ' . $p['tipoPago'] . ' ' . number_format((float)$p['monto'], 2) . ' €') ?>"
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaPagos', 15);
</script>
