<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/pagos.php";

$idDeEsteEstudiante = $_SESSION['idEstudiante'];
$listaMisPagos = listarPagosPorEstudiante($idDeEsteEstudiante);
$datosEstadoFinanciero = obtenerEstadoFinancieroEstudiante($idDeEsteEstudiante);

$tituloDelPagina = "AULAPRO | MIS PAGOS";
$seccionActual = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS PAGOS</h1>
    <p class="subtitulo">Consulta tu historial de pagos y estado financiero</p>
</div>


<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['totalPagado'], 2)) ?> €</h3>
        <p>TOTAL PAGADO</p>
    </div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['precioCiclo'], 2)) ?> €</h3>
        <p>PRECIO DEL CICLO</p>
    </div>
  </div>
  <div class="tarjeta-estadistica <?= Security::escapeHtml(($datosEstadoFinanciero['restante'] > 0) ? 'tarjeta-estadistica-naranja' : 'tarjeta-estadistica-cian') ?>">
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['restante'], 2)) ?> €</h3>
        <p>PENDIENTE DE PAGO</p>
    </div>
  </div>
</div>

<div class="panel margen-arriba">
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
                    <th>PROXIMO PAGO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaMisPagos)) { ?>
                    <tr>
                        <td colspan="4" class="vacio">No hay pagos registrados en su historial.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaMisPagos as $pagoIndividual) { ?>
                    <tr>
                        <td><?= Security::escapeHtml(date('d/m/Y', strtotime($pagoIndividual['fechaPago']))) ?></td>
                        <td>
                            <span class="texto-pago"><?= Security::escapeHtml(strtoupper($pagoIndividual['tipoPago'])) ?></span>
                        </td>
                        <td class="texto-negrita"><?= Security::escapeHtml(number_format($pagoIndividual['monto'], 2)) ?> €</td>
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


<?php include '../comunes/footer.php'; ?>



