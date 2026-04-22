<?php
session_start();
$titulo_pagina = "Gestión de Pagos";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../../modelos/pagos.php";
$listaPagos = listarTodosLosPagos();

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Pagos y Cobros</h1>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/vistas/admin/pagos/agregarPagos.php" class="boton-primario">
            <i class="fas fa-plus"></i> Registrar Nuevo Pago
        </a>
    </div>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Tipo</th>
                    <th>Fecha Cobro</th>
                    <th>Próximo Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaPagos)) { ?>
                    <tr><td colspan="7" class="sin-datos">No hay registros de pagos</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaPagos as $pago) { ?>
                    <tr>
                        <td><strong><?php echo $pago['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $pago['concepto']; ?></td>
                        <td><?php echo number_format($pago['monto'], 2); ?> €</td>
                        <td><span class="etiqueta-gris"><?php echo ucfirst($pago['tipoPago']); ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($pago['fechaPago'])); ?></td>
                        <td>
                            <strong class="texto-primario">
                                <?php echo date('d/m/Y', strtotime($pago['fechaProximoPago'])); ?>
                            </strong>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <?php if (!empty($pago['comprobante'])) { ?>
                                    <a href="/pfc/public/uploads/<?php echo $pago['comprobante']; ?>" target="_blank" 
                                       class="boton-icono boton-ver" title="Ver Comprobante">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                <?php } ?>
                                <a href="/pfc/vistas/admin/pagos/modificarPagos.php?idPago=<?php echo $pago['idPago']; ?>" 
                                   class="boton-icono boton-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="/pfc/controladores/admin/pagos/borrar.php" class="d-inline" onsubmit="return confirm('¿Borrar registro?');">
                                    <input type="hidden" name="idPago" value="<?php echo $pago['idPago']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar">
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
