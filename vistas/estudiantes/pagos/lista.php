<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (empty($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/pagos.php";

$idDeEsteEstudiante = $_SESSION['idEstudiante'];
$listaMisPagos = obtenerPagosPorEstudiante($idDeEsteEstudiante);
$datosEstadoFinanciero = obtenerEstadoFinancieroEstudiante($idDeEsteEstudiante);

$tituloDelPagina = "MIS PAGOS - PORTAL ESTUDIANTES";
$seccionActual = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MIS PAGOS</h1>
    <p class="subtitulo">Consulta tu historial de pagos y estado financiero</p>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica">
        <h3><?= number_format($datosEstadoFinanciero['totalPagado'], 2) ?> €</h3>
        <p>TOTAL PAGADO</p>
    </div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica">
        <h3><?= number_format($datosEstadoFinanciero['precioCiclo'], 2) ?> €</h3>
        <p>PRECIO DEL CICLO</p>
    </div>
  </div>
  <div class="tarjeta-estadistica <?= ($datosEstadoFinanciero['restante'] > 0) ? 'tarjeta-estadistica-naranja' : 'tarjeta-estadistica-cian' ?>">
    <div class="info-estadistica">
        <h3><?= number_format($datosEstadoFinanciero['restante'], 2) ?> €</h3>
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
                <?php if (empty($listaMisPagos)) { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No hay pagos registrados en su historial.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaMisPagos as $pagoIndividual) { ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($pagoIndividual['fechaPago'])) ?></td>
                        <td>
                            <span class="etiqueta-pago"><?= strtoupper($pagoIndividual['tipoPago']) ?></span>
                        </td>
                        <td class="texto-negrita"><?= number_format($pagoIndividual['monto'], 2) ?> €</td>
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
    <p>Si detectas algún error en tus pagos, por favor contacta con administración a través de la sección de mensajería.</p>
</div>

<?php include '../comunes/footer.php'; ?>




