<?php
session_start();
if (empty($_SESSION['idAdmin'])) { http_response_code(403); exit('Acceso denegado'); }

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../modelos/calificaciones.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$idCiclo = (int)($_GET['idCiclo'] ?? 0);
if (!$idCiclo) { header("Location: ../../../vistas/admin/informes/informes.php"); exit; }

$cfg    = obtenerConfiguracionCentro();
$ciclo  = obtenerCicloPorId($idCiclo);
$datos  = generarDatosBoletinCiclo($idCiclo);

if (empty($datos['estudiantes'])) {
    $_SESSION['errores'] = "No hay estudiantes en este ciclo.";
    header("Location: ../../../vistas/admin/informes/informes.php"); exit;
}

// Build one HTML page per student
ob_start();
$first = true;
foreach ($datos['estudiantes'] as $estudiante) {
    if (!$first) echo '<div style="page-break-after:always;"></div>';
    $first = false;
    include __DIR__ . '/../../../templates/pdf/boletin.php';
}
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'boletines_' . preg_replace('/\W+/', '_', $ciclo['abreviaturaCiclo'] ?? 'ciclo') . '_' . date('Ymd') . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]);
