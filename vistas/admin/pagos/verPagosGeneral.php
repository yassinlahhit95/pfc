<?php
session_start();

// Validación de sesión simple
if (isset($_SESSION['idAdmin']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "GESTIÓN DE PAGOS - SUPER ADMIN";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../../modelos/pagos.php";
require_once "../../../modelos/ciclos.php";

// Captura de filtros
$idDelCicloParaFiltrar = "";
if (isset($_GET['idCiclo'])) {
    $idDelCicloParaFiltrar = $_GET['idCiclo'];
}

// Obtener datos según el filtro
if ($idDelCicloParaFiltrar != "") {
    $listaDePagosAMostrar = listarPagosFiltrados($idDelCicloParaFiltrar);
} else {
    $listaDePagosAMostrar = listarTodosLosPagos();
}

$listaDeTodosLosCiclos = listarTodosLosCiclos();

// Manejo de mensajes de sesión
$mensajeError = "";
if (isset($_SESSION['error'])) { 
    $mensajeError = $_SESSION['error']; 
}

$mensajeExito = "";
if (isset($_SESSION['exito'])) { 
    $mensajeExito = $_SESSION['exito']; 
}

// Limpiar mensajes
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>GESTIÓN DE PAGOS</h1>
    <a href="/pfc/vistas/admin/pagos/agregarPagos.php" class="boton-primario">
        <i class="fas fa-plus"></i> REGISTRAR NUEVO PAGO
    </a>
</div>

<?php if ($mensajeExito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensajeExito; ?></div>
<?php } ?>

<?php if ($mensajeError != "") { ?>
    <div class="mensaje-error"><?php echo $mensajeError; ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <div class="disposicion-flexible alinear-fin separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label><i class="fas fa-search"></i> BUSCAR PAGO (ALUMNO O TIPO):</label>
            <input type="text" id="inputBuscarPago" placeholder="Escriba nombre del alumno o tipo de pago..." onkeyup="filtrarTabla('inputBuscarPago', 'tablaPagos')">
        </div>
        <div class="campo-formulario flexible-rellenar">
            <label>FILTRAR POR CICLO FORMATIVO:</label>
            <select name="idCiclo" onchange="window.location.href='verPagosGeneral.php?idCiclo=' + this.value">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeTodosLosCiclos as $cicloItem) { ?>
                    <option value="<?php echo $cicloItem['idCiclo']; ?>" <?php if($idDelCicloParaFiltrar == $cicloItem['idCiclo']) { echo "selected"; } ?>>
                        <?php echo strtoupper($cicloItem['nombreCiclo']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div>
            <a href="verPagosGeneral.php" class="boton-secundario">LIMPIAR</a>
        </div>
    </div>
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
                <?php if ($listaDePagosAMostrar == false || count($listaDePagosAMostrar) == 0) { ?>
                    <tr>
                        <td colspan="7" class="sin-datos">No hay registros de pagos que coincidan con la búsqueda.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDePagosAMostrar as $pagoIndividual) { ?>
                    <tr>
                        <td><strong><?php echo strtoupper($pagoIndividual['nombreEstudiante']); ?></strong></td>
                        <td><?php echo strtoupper($pagoIndividual['nombreCiclo']); ?></td>
                        <td>
                            <span class="etiqueta-pago"><?php echo strtoupper($pagoIndividual['tipoPago']); ?></span>
                        </td>
                        <td class="texto-negrita"><?php echo number_format($pagoIndividual['monto'], 2); ?> €</td>
                        <td><?php echo date('d/m/Y', strtotime($pagoIndividual['fechaPago'])); ?></td>
                        <td>
                            <?php 
                                if ($pagoIndividual['tipoPago'] == 'unico') {
                                    echo '<span class="texto-gris">N/A (PAGO ÚNICO)</span>';
                                } else {
                                    echo date('d/m/Y', strtotime($pagoIndividual['fechaProximoPago'])); 
                                }
                            ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="historialEstudiante.php?idEstudiante=<?php echo $pagoIndividual['idEstudiante']; ?>" 
                                   class="boton-icono boton-ver" title="Ver historial completo">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="modificarPagos.php?idPago=<?php echo $pagoIndividual['idPago']; ?>" 
                                   class="boton-icono boton-editar" title="Editar este pago">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/pagos/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este registro de pago?')">
                                    <input type="hidden" name="idPago" value="<?php echo $pagoIndividual['idPago']; ?>">
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
