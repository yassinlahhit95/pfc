<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$estudiantes = listarEstudiantes();

$filename = 'estudiantes_' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');

$out = fopen('php://output', 'w');
fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

fputcsv($out, [
    'idEstudiante',
    'nombreEstudiante',
    'emailEstudiante',
    'dniEstudiante',
    'telefonoEstudiante',
    'direccionEstudiante',
    'ciudadEstudiante',
    'codigoPostalEstudiante',
    'fechaNacimientoEstudiante',
    'fechaAltaEstudiante',
    'curso',
    'nombreCiclo',
    'observacionesEstudiante',
]);

foreach ($estudiantes as $e) {
    fputcsv($out, [
        $e['idEstudiante'],
        $e['nombreEstudiante'],
        $e['emailEstudiante'],
        $e['dniEstudiante'],
        $e['telefonoEstudiante'],
        $e['direccionEstudiante'],
        $e['ciudadEstudiante'],
        $e['codigoPostalEstudiante'],
        $e['fechaNacimientoEstudiante'],
        $e['fechaAltaEstudiante'],
        $e['curso'],
        $e['nombreCiclo'],
        $e['observacionesEstudiante'],
    ]);
}

fclose($out);
exit;
