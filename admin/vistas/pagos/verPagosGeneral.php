<?php
session_start();
$titulo_pagina = "Gestión de Pagos";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/pagos.php";

$con = new Conexion();
$conexionBD = $con->conectar();
$modeloPagos = new pago($conexionBD);

// Llamamos al método correcto del modelo
$listaPagos = $modeloPagos->listarTodosLosPagosModelo();

$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['exito']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Gestión de Pagos</h1>
        <p class="texto-atenuado">Listado general de cobros y recibos</p>
    </div>
    <a href="vistas/pagos/agregarPagos.php" class="boton-azul">
        <i class="fas fa-plus"></i> Registrar Nuevo Pago
    </a>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $exito; ?></div>
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaPagos)) { ?>
                    <tr><td colspan="7" class="sin-datos">No hay pagos registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaPagos as $p) { ?>
                    <tr>
                        <td><?php echo $p['idPago']; ?></td>
                        <td><strong><?php echo htmlspecialchars($p['nombreEstudiante']); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['concepto']); ?></td>
                        <td><?php echo number_format($p['monto'], 2); ?> €</td>
                        <td>
                            <span class="estado-bolita <?php echo ($p['estadoPago'] == 'pagado') ? 'activo-verde' : 'inactivo-rojo'; ?>">
                                <?php echo ucfirst($p['estadoPago']); ?>
                            </span>
                        </td>
                        <td><?php echo $p['fechaPago']; ?></td>
                        <td>
                            <form method="POST" action="controlador/pagosControlador.php" class="d-inline" onsubmit="return confirm('¿Borrar este pago?');">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="idPago" value="<?php echo $p['idPago']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
