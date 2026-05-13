<?php
session_start();

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

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "AULAPRO | GESTIÓN DE PAGOS";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>GESTIÓN DE PAGOS</h1>
    <a href="agregarPagos.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO PAGO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <form method="GET" action="verPagosGeneral.php">
        <div class="disposicion-flexible alinear-fin separacion-grande">
            <div class="campo-formulario flexible-rellenar">
                <label>FILTRAR POR CICLO FORMATIVO:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los Ciclos --</option>
                    <?php foreach ($listaDeTodosLosCiclos as $cicloItem) { ?>
                        <option value="<?= $cicloItem['idCiclo'] ?>" <?= $idDelCicloParaFiltrar == $cicloItem['idCiclo'] ? 'selected' : '' ?>>
                            <?= mb_strtoupper($cicloItem['nombreCiclo'], 'UTF-8') ?>
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

<div class="tarjeta-blanca">
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
                        <td colspan="7" class="sin-datos">No hay registros de pagos que coincidan con la búsqueda.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDePagosAMostrar as $pagoIndividual) { ?>
                    <tr>
                        <td><strong><?= mb_strtoupper($pagoIndividual['nombreEstudiante'], 'UTF-8') ?></strong></td>
                        <td><?= mb_strtoupper($pagoIndividual['nombreCiclo'], 'UTF-8') ?></td>
                        <td>
                            <span class="etiqueta-pago"><?= mb_strtoupper($pagoIndividual['tipoPago'], 'UTF-8') ?></span>
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
                                   class="btn-accion btn-ver" title="Ver historial completo">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="modificarPagos.php?idPago=<?= $pagoIndividual['idPago'] ?>" 
                                   class="btn-accion btn-editar" title="Editar este pago">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/pagos/borrar.php" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este registro de pago?')">
                                    <input type="hidden" name="idPago" value="<?= $pagoIndividual['idPago'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
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




