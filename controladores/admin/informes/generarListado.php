<?php
session_start();
if (empty($_SESSION['idAdmin'])) { http_response_code(403); exit('Acceso denegado'); }

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../modelos/estudiantes.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$idCiclo = (int)($_GET['idCiclo'] ?? 0);
$cfg     = obtenerConfiguracionCentro();

if ($idCiclo) {
    $ciclo       = obtenerCicloPorId($idCiclo);
    $estudiantes = listarEstudiantesPorCiclo($idCiclo);
} else {
    $ciclo       = ['nombreCiclo' => 'Todos los Ciclos', 'abreviaturaCiclo' => 'TODOS'];
    $estudiantes = listarEstudiantes();
}

ob_start();
include __DIR__ . '/../../../templates/pdf/listado.php';
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$filename = 'listado_' . preg_replace('/\W+/', '_', $ciclo['abreviaturaCiclo'] ?? 'ciclo') . '_' . date('Ymd') . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]);
