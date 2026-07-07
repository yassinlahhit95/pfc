<?php
require 'vendor/autoload.php';

require 'include/ReportService.php';

$cfg = [
    'logoGobierno1' => 'public/assets/logo1.png',
    'nombreCentro' => 'Test Centro',
    'direccionCentro' => 'Test Dir',
    'logoCentro' => 'public/assets/logo2.png',
    'logoGobierno2' => '',
    'cursoEscolar' => '2023-2024',
    'ciudadCentro' => 'Test Ciudad',
    'nombreDirectorFirmante' => 'Test Director',
];

$ciclo = [
    'abreviaturaCiclo' => 'TEST',
    'nombreNivel' => 'TEST NIVEL'
];

$estudiantes = [
    [
        'nombreEstudiante' => 'Juan Perez',
        'dniEstudiante' => '12345678A',
        '_serial' => 'BLT-TEST-1234',
        'modulos' => []
    ],
    [
        'nombreEstudiante' => 'Maria Gomez',
        'dniEstudiante' => '87654321B',
        '_serial' => 'BLT-TEST-5678',
        'modulos' => []
    ]
];

function logoParaPdf($path) { return ''; }

$rs = new ReportService();
$pdf = $rs->generateBoletines($cfg, $ciclo, $estudiantes, 'http://localhost');
$pdf->Output('test_boletin.pdf', \Mpdf\Output\Destination::FILE);
echo "PDF Generado\n";
