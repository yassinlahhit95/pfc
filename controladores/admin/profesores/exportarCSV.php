<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$profesores = listarProfesores();

$filename = 'profesores_' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');

$out = fopen('php://output', 'w');
fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

fputcsv($out, [
    'idProfesor',
    'nombreProfesor',
    'emailProfesor',
    'dniProfesor',
    'telefonoProfesor',
    'direccionProfesor',
    'ciudadProfesor',
    'codigoPostalProfesor',
    'fechaNacimientoProfesor',
    'fechaAltaProfesor',
    'esTutor',
    'nombreCicloTutor',
    'observacionesProfesor',
]);

foreach ($profesores as $p) {
    fputcsv($out, [
        $p['idProfesor'],
        $p['nombreProfesor'],
        $p['emailProfesor'],
        $p['dniProfesor'],
        $p['telefonoProfesor'],
        $p['direccionProfesor'],
        $p['ciudadProfesor'],
        $p['codigoPostalProfesor'],
        $p['fechaNacimientoProfesor'],
        $p['fechaAltaProfesor'],
        $p['esTutor'] ? 'Si' : 'No',
        $p['nombreCicloTutor'] ?? '',
        $p['observacionesProfesor'],
    ]);
}

fclose($out);
exit;
