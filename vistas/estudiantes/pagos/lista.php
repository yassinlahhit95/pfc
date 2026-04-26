<?php
session_start();

// Validación de sesión simple
if (isset($_SESSION['idEstudiante']) == false || $_SESSION['idEstudiante'] == "") {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/pagos.php";

$idDeEsteEstudiante = $_SESSION['idEstudiante'];
$listaMisPagos = obtenerPagosPorEstudiante($idDeEsteEstudiante);
$datosEstadoFinanciero = obtenerEstadoFinancieroEstudiante($idDeEsteEstudiante);

$tituloDelPagina = "MIS PAGOS - PORTAL ESTUDIANTES";
$seccionActual = 'pagos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MIS PAGOS</h1>
    <p class="subtitulo">Consulta tu historial de pagos y estado financiero</p>
</div>

<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica">
        <h3><?php echo number_format($datosEstadoFinanciero['totalPagado'], 2); ?> €</h3>
        <p>TOTAL PAGADO</p>
    </div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica">
        <h3><?php echo number_format($datosEstadoFinanciero['precioCiclo'], 2); ?> €</h3>
        <p>PRECIO DEL CICLO</p>
    </div>
  </div>
  <div class="tarjeta-estadistica <?php if ($datosEstadoFinanciero['restante'] > 0) { echo 'tarjeta-estadistica-naranja'; } else { echo 'tarjeta-estadistica-cian'; } ?>">
    <div class="info-estadistica">
        <h3><?php echo number_format($datosEstadoFinanciero['restante'], 2); ?> €</h3>
        <p>PENDIENTE DE PAGO</p>
    </div>
  </div>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>HISTORIAL DE PAGOS</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>CONCEPTO / TIPO</th>
                    <th>MONTO</th>
                    <th>PRÓXIMO PAGO</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($listaMisPagos == false || count($listaMisPagos) == 0) { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No se han registrado pagos en tu historial.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaMisPagos as $pagoIndividual) { ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($pagoIndividual['fechaPago'])); ?></td>
                        <td>
                            <span class="etiqueta-pago"><?php echo strtoupper($pagoIndividual['tipoPago']); ?></span>
                        </td>
                        <td class="texto-negrita"><?php echo number_format($pagoIndividual['monto'], 2); ?> €</td>
                        <td>
                            <?php 
                                if ($pagoIndividual['tipoPago'] == 'unico') {
                                    echo '<span class="texto-gris">N/A (PAGO FINALIZADO)</span>';
                                } else {
                                    echo date('d/m/Y', strtotime($pagoIndividual['fechaProximoPago'])); 
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
