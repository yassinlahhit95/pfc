<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeTodosLosCiclos = listarTodosLosCiclos();
$listaNiveles          = listarNiveles();

$idNivelFiltro = (int)($_GET['idNivel'] ?? 0);
$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($listaDeTodosLosCiclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
    : $listaDeTodosLosCiclos;

$idDelCicloParaFiltrar = $_GET['idCiclo'] ?? '';

if ($idNivelFiltro && $idDelCicloParaFiltrar && !in_array((int)$idDelCicloParaFiltrar, array_column($ciclosFiltrados, 'idCiclo'))) {
    $idDelCicloParaFiltrar = '';
}

$listaDePagosAMostrar = $idDelCicloParaFiltrar
    ? listarPagosFiltrados($idDelCicloParaFiltrar)
    : listarTodosLosPagos();

$titulo_pagina = "AULAPRO | GESTIÓN DE PAGOS";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTIÓN DE PAGOS</h1>
    <a href="agregarPagos.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO PAGO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

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
                        <option value="<?= $cicloItem['idCiclo'] ?>" <?= $idDelCicloParaFiltrar == $cicloItem['idCiclo'] ? 'selected' : '' ?>>
                            <?= strtoupper($cicloItem['nombreCiclo']) ?>
                        </option>
                    <?php } ?>
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
                <?php if (empty($listaDePagosAMostrar)) { ?>
                    <tr>
                        <td colspan="7" class="vacio">No hay registros de pagos que coincidan con la búsqueda.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDePagosAMostrar as $pagoIndividual) { ?>
                    <tr>
                        <td><b><?= strtoupper($pagoIndividual['nombreEstudiante']) ?></b></td>
                        <td><?= strtoupper($pagoIndividual['nombreCiclo']) ?></td>
                        <td>
                            <span class="texto-pago"><?= strtoupper($pagoIndividual['tipoPago']) ?></span>
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
                                    <a class="recurso-menu-item" href="historialEstudiante.php?idEstudiante=<?= $pagoIndividual['idEstudiante'] ?>"><i class="fas fa-history"></i> Historial</a>
                                    <a class="recurso-menu-item" href="modificarPagos.php?idPago=<?= $pagoIndividual['idPago'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarPago.php?id=<?= $pagoIndividual['idPago'] ?>" onclick="return confirm('¿Eliminar este pago?')"><i class="fas fa-trash"></i> Eliminar</a>
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

<?php include '../comunes/footer.php'; ?>

