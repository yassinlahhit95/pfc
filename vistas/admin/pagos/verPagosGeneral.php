<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idDelCicloParaFiltrar = $_GET['idCiclo'] ?? '';

if (!empty($idDelCicloParaFiltrar)) {
    $listaDePagosAMostrar = listarPagosFiltrados($idDelCicloParaFiltrar);
} else {
    $listaDePagosAMostrar = listarTodosLosPagos();
}

$listaDeTodosLosCiclos = listarTodosLosCiclos();

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
                <label>FILTRAR POR CICLO FORMATIVO:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los Ciclos --</option>
                    <?php foreach ($listaDeTodosLosCiclos as $cicloItem) { ?>
                        <option value="<?= $cicloItem['idCiclo'] ?>" <?= $idDelCicloParaFiltrar == $cicloItem['idCiclo'] ? 'selected' : '' ?>>
                            <?= strtoupper($cicloItem['nombreCiclo']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <a href="verPagosGeneral.php" class="boton-secundario">LIMPIAR</a>
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
                            <div class="botones-accion">
                                <a href="historialEstudiante.php?idEstudiante=<?= $pagoIndividual['idEstudiante'] ?>" 
                                   class="btn-accion btn-ver">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="modificarPagos.php?idPago=<?= $pagoIndividual['idPago'] ?>" 
                                   class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/pagos/borrar.php" method="POST" onsubmit="return confirm('Estás seguro de eliminar este registro de pago?')">
                                    <input type="hidden" name="idPago" value="<?= $pagoIndividual['idPago'] ?>">
                                    <input type="submit" class="btn-accion btn-eliminar" value="Borrar">
                                </form>
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

