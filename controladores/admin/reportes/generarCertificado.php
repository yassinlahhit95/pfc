<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

if (!isset($_POST['generarCertificado'])) {
    header("Location: ../../../vistas/admin/reportes/certificados.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../lib/fpdf/fpdf.php";

$idEstudiante  = intval($_POST['idEstudiante'] ?? 0);
$horario       = trim($_POST['horario'] ?? '');
$anioAcademico = trim($_POST['anioAcademico'] ?? '');
$ciudad        = trim($_POST['ciudad'] ?? '');

$errores = [];

if ($idEstudiante <= 0) {
    $errores['idEstudiante'] = 'Debes seleccionar un estudiante.';
}
if (empty($horario)) {
    $errores['horario'] = 'El horario no puede estar vacío.';
}
if (empty($anioAcademico)) {
    $errores['anioAcademico'] = 'El año académico no puede estar vacío.';
}
if (empty($ciudad)) {
    $errores['ciudad'] = 'La ciudad no puede estar vacía.';
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/admin/reportes/certificados.php");
    exit;
}

$estudiante = obtenerEstudiantePorId($idEstudiante);
$director   = obtenerDirectorPorId(1);

if (empty($estudiante)) {
    header("Location: ../../../vistas/admin/reportes/certificados.php");
    exit;
}

$nombreEstudiante = mb_strtoupper($estudiante['nombreEstudiante'], 'UTF-8');
$dniEstudiante    = $estudiante['dniEstudiante'];
$nombreCiclo      = $estudiante['nombreCiclo'];
$numeroCurso      = intval($estudiante['curso']);
$textoCurso       = $numeroCurso == 2 ? 'segundo' : 'primer';

$nombreDirector = mb_strtoupper($director['nombreDirector'], 'UTF-8');
$dniDirector    = $director['dniDirector'];

$datosCiclo   = obtenerCicloPorId($estudiante['idCiclo']);
$listaNiveles = listarNiveles();
$nombreNivel  = '';
foreach ($listaNiveles as $nivel) {
    if ($nivel['idNivel'] == $datosCiclo['idNivel']) {
        $nombreNivel = $nivel['nombreNivel'];
        break;
    }
}

$meses = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
    5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
    9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
];
$fechaActual = date('j') . ' de ' . $meses[intval(date('m'))] . ' de ' . date('Y');

// Convierte UTF-8 a Latin-1 para FPDF (soporta caracteres españoles)
function conv($texto) {
    $resultado = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
    return $resultado !== false ? $resultado : $texto;
}

// Rutas de logos
$rutaLogoPng   = __DIR__ . '/../../../public/imagenes/aulapro.png';
$rutaLogoJpeg  = __DIR__ . '/../../../public/imagenes/aulapro.jpeg';
$rutaLogoFondo = __DIR__ . '/../../../public/imagenes/fondo.png';

// Crear PDF A4 vertical
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetMargins(28, 22, 28);
$pdf->SetAutoPageBreak(false);

// --- Logos ---
$yLogos            = 22;
$alturaLogoLateral = 18;
$alturaLogoCentral = 23;

if (file_exists($rutaLogoPng)) {
    $pdf->Image($rutaLogoPng, 28, $yLogos, 0, $alturaLogoLateral);
}

if (file_exists($rutaLogoJpeg)) {
    list($imgW, $imgH) = getimagesize($rutaLogoJpeg);
    $anchoCentral = $alturaLogoCentral * ($imgW / $imgH);
    $xCentral = (210 / 2) - ($anchoCentral / 2);
    $pdf->Image($rutaLogoJpeg, $xCentral, $yLogos, 0, $alturaLogoCentral);
}

if (file_exists($rutaLogoFondo)) {
    list($imgW, $imgH) = getimagesize($rutaLogoFondo);
    $anchoDerecha = $alturaLogoLateral * ($imgW / $imgH);
    $xDerecha = 210 - 28 - $anchoDerecha;
    $pdf->Image($rutaLogoFondo, $xDerecha, $yLogos, 0, $alturaLogoLateral);
}

// --- Párrafo del director ---
$pdf->SetY($yLogos + $alturaLogoCentral + 32);
$pdf->SetLeftMargin(28);
$pdf->SetFont('Times', '', 12);
$pdf->SetX(28);

$pdf->Write(7, conv('D./Dña. '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($nombreDirector));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, conv(', Director/a del Centro con DNI: '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($dniDirector));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, conv(', como Director/a del Centro de Formación Profesional AulaPro,'));

// --- CERTIFICA centrado ---
$pdf->Ln(18);
$pdf->SetFont('Times', 'B', 20);
$pdf->SetX(28);
$pdf->Cell(154, 10, 'CERTIFICA', 0, 1, 'C');

// --- Cuerpo párrafo 1 (con sangría) ---
$pdf->Ln(18);
$pdf->SetLeftMargin(43);
$pdf->SetX(43);
$pdf->SetFont('Times', '', 12);

$pdf->Write(7, conv('Que, '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($nombreEstudiante));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, conv(', con NIE/DNI/PASAPORTE '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($dniEstudiante));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, conv(', está matriculado/a en este Centro, el Ciclo Formativo de '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($nombreNivel));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, conv(' de '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($nombreCiclo));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, conv(', en '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($textoCurso . ' curso'));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, conv(', en este año académico '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($anioAcademico));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, conv(', con un horario de '));
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(7, conv($horario));
$pdf->SetFont('Times', '', 12);
$pdf->Write(7, '.');

// --- Cuerpo párrafo 2 ---
$pdf->Ln(14);
$pdf->SetX(43);
$pdf->Write(7, conv('Y para que así, y a petición del interesado/a, se expide el presente certificado.'));

// --- Fecha alineada a la derecha ---
$pdf->SetLeftMargin(28);
$pdf->Ln(22);
$pdf->SetFont('Times', '', 12);
$pdf->SetX(28);
$pdf->Cell(154, 7, conv('En ' . $ciudad . ', a ' . $fechaActual . '.'), 0, 1, 'R');

// --- Enviar como descarga directa ---
$nombreArchivo = 'Certificado_' . str_replace(' ', '_', $nombreEstudiante) . '.pdf';

if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output('D', $nombreArchivo);
exit;
