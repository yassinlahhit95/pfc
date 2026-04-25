<?php
session_start();
$titulo_pagina = "Gestión de Pagos - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../../modelos/pagos.php";
require_once "../../../modelos/ciclos.php";

$idCicloFiltro = isset($_GET['idCiclo']) ? $_GET['idCiclo'] : '';

if ($idCicloFiltro != '') {
    $todos_los_pagos = listarPagosFiltrados($idCicloFiltro);
} else {
    $todos_los_pagos = listarTodosLosPagos();
}

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Gestión de Pagos</h1>
    <a href="/pfc/vistas/admin/pagos/agregarPagos.php" class="boton-primario">
        <i class="fas fa-plus"></i> Registrar Nuevo Pago
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <form method="GET" action="" class="formulario-filtro">
        <div class="disposicion-flexible alinear-fin">
            <div class="campo-formulario">
                <label>Filtrar por Ciclo:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los Ciclos --</option>
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if($idCicloFiltro == $ciclo['idCiclo']) echo "selected"; ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="ml-10">
                <a href="verPagosGeneral.php" class="boton-secundario">Limpiar</a>
            </div>
        </div>
    </form>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Tipo de Pago</th>
                    <th>Cantidad</th>
                    <th>Fecha Pago</th>
                    <th>Próximo Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_pagos)) { ?>
                    <tr><td colspan="7" class="sin-datos">No se han registrado pagos</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_pagos as $pago) { ?>
                    <tr>
                        <td><strong><?php echo $pago['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $pago['nombreCiclo']; ?></td>
                        <td><span class="etiqueta-pago"><?php echo ucfirst($pago['tipoPago']); ?></span></td>
                        <td><?php echo number_format($pago['monto'], 2); ?> €</td>
                        <td><?php echo date('d/m/Y', strtotime($pago['fechaPago'])); ?></td>
                        <td>
                            <?php 
                                if ($pago['tipoPago'] == 'unico') {
                                    echo '<span class="texto-gris">N/A (Pago Único)</span>';
                                } else {
                                    echo date('d/m/Y', strtotime($pago['fechaProximoPago'])); 
                                }
                            ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="historialEstudiante.php?idEstudiante=<?php echo $pago['idEstudiante']; ?>" class="boton-icono boton-ver" title="Ver Historial">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="modificarPagos.php?idPago=<?php echo $pago['idPago']; ?>" class="boton-icono boton-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/pagos/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro de pago?')">
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
