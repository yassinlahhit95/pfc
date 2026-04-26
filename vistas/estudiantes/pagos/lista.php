<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/pagos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$mis_pagos = obtenerPagosPorEstudiante($idEstudiante);
$estado = obtenerEstadoFinancieroEstudiante($idEstudiante);

$tituloDelPagina = "Mis Pagos - Portal Estudiantes";
$seccionActual = 'pagos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Mis Pagos</h1>
    <p class="subtitulo">Consulta tu historial de pagos y estado financiero</p>
</div>

<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica">
        <h3><?php echo number_format($estado['totalPagado'], 2); ?> €</h3>
        <p>Total Pagado</p>
    </div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica">
        <h3><?php echo number_format($estado['precioCiclo'], 2); ?> €</h3>
        <p>Precio del Ciclo</p>
    </div>
  </div>
  <div class="tarjeta-estadistica <?php echo $estado['restante'] > 0 ? 'tarjeta-estadistica-naranja' : 'tarjeta-estadistica-cian'; ?>">
    <div class="info-estadistica">
        <h3><?php echo number_format($estado['restante'], 2); ?> €</h3>
        <p>Pendiente de Pago</p>
    </div>
  </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Historial de Pagos</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Concepto / Tipo</th>
                    <th>Monto</th>
                    <th>Próximo Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mis_pagos)) { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No se han registrado pagos en tu historial.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($mis_pagos as $pago) { ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($pago['fechaPago'])); ?></td>
                        <td>
                            <span class="etiqueta-pago"><?php echo ucfirst($pago['tipoPago']); ?></span>
                        </td>
                        <td class="texto-negrita"><?php echo number_format($pago['monto'], 2); ?> €</td>
                        <td>
                            <?php 
                                if ($pago['tipoPago'] == 'unico') {
                                    echo '<span class="texto-gris">N/A (Pago Finalizado)</span>';
                                } else {
                                    echo date('d/m/Y', strtotime($pago['fechaProximoPago'])); 
                                }
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="margen-arriba tarjeta-gris-suave">
    <p><i class="fas fa-info-circle"></i> Si detectas algún error en tus pagos, por favor contacta con administración a través de la sección de mensajería.</p>
</div>

<?php include '../comunes/footer.php'; ?>
