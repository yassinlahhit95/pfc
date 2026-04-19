<?php
session_start();
$titulo_pagina = "Gestión de Pagos";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../modelos/pagos.php";
$listaPagos = listarPagos();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Gestión de Pagos</h1>
        <p class="texto-atenuado">Listado general de cobros y recibos</p>
    </div>
    <a href="vistas/pagos/agregarPagos.php" class="boton-primario">
        <i class="fas fa-plus"></i> Registrar Nuevo Pago
    </a>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $exito; ?></div>
<?php } ?>
<?php if ($error != "") { ?>
    <div class="mensaje-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaPagos)) { ?>
                    <tr><td colspan="8" class="sin-datos">No hay pagos registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaPagos as $pago) { ?>
                    <tr>
                        <td><?php echo $pago['idPago']; ?></td>
                        <td><strong><?php echo $pago['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $pago['concepto']; ?></td>
                        <td><?php echo number_format($pago['monto'], 2); ?> €</td>
                        <td>
                            <span class="estado-bolita <?php if ($pago['estadoPago'] == 'pagado') { echo 'activo-verde'; } else { echo 'inactivo-rojo'; } ?>">
                                <?php echo ucfirst($pago['estadoPago']); ?>
                            </span>
                        </td>
                        <td><?php echo $pago['fechaPago']; ?></td>
                        <td>
                            <?php if ($pago['comprobante']) { ?>
                                <a href="uploads/<?php echo $pago['comprobante']; ?>" target="_blank" class="boton-secundario boton-pequeno">
                                    <i class="fas fa-file-download"></i> Ver
                                </a>
                            <?php } else { ?>
                                <span class="texto-atenuado">Sin archivo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="vistas/pagos/modificarPagos.php?idPago=<?php echo $pago['idPago']; ?>" class="boton-icono boton-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="controladores/pagos/borrar.php" class="d-inline" onsubmit="return confirm('¿Borrar este pago?');">
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
