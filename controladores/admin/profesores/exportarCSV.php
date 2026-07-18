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

foreach ($profesores as $profesor) {
    fputcsv($out, [
        $profesor['idProfesor'],
        $profesor['nombreProfesor'],
        $profesor['emailProfesor'],
        $profesor['dniProfesor'],
        $profesor['telefonoProfesor'],
        $profesor['direccionProfesor'],
        $profesor['ciudadProfesor'],
        $profesor['codigoPostalProfesor'],
        $profesor['fechaNacimientoProfesor'],
        $profesor['fechaAltaProfesor'],
        $profesor['esTutor'] ? 'Si' : 'No',
        $profesor['nombreCicloTutor'] ?? '',
        $profesor['observacionesProfesor'],
    ]);
}

fclose($out);
exit;
