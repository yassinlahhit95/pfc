<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_fct');
require_once __DIR__ . "/../../../modelos/fct.php";

$idCiclo      = (int)($_POST['idCiclo'] ?? 0);
$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$idProfesorTutor = (int)($_POST['idProfesorTutor'] ?? 0) ?: null;
$idEmpresa    = (int)($_POST['idEmpresa'] ?? 0) ?: null;
$empresa      = trim($_POST['empresa'] ?? '');
$ciudadEmpresa = trim($_POST['ciudadEmpresa'] ?? '') ?: null;
$tutorEmpresa = trim($_POST['tutorEmpresa'] ?? '') ?: null;
$emailTutorEmpresa = trim($_POST['emailTutorEmpresa'] ?? '') ?: null;
$telefonoEmpresa   = trim($_POST['telefonoEmpresa'] ?? '') ?: null;
$fechaInicio  = trim($_POST['fechaInicio'] ?? '') ?: null;
$fechaFin     = trim($_POST['fechaFin'] ?? '') ?: null;
$horasTotales = ($_POST['horasTotales'] ?? '') !== '' ? (int)$_POST['horasTotales'] : null;
$fase         = max(1, (int)($_POST['fase'] ?? 1));

$errores = [];
if (!$idCiclo) $errores['idEstudiante'] = 'Selecciona un ciclo.';
if (!$idEstudiante) $errores['idEstudiante'] = 'Selecciona un estudiante.';
if ($empresa === '') $errores['empresa'] = 'El nombre de la empresa es obligatorio.';
if (!empty($emailTutorEmpresa) && !Security::validateEmail($emailTutorEmpresa)) $errores['emailTutorEmpresa'] = 'Email no válido.';

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_fct'] = $_POST;
    header('Location: ../../../vistas/admin/fct/agregar.php?idCiclo=' . $idCiclo);
    exit;
}

$idFCT = insertarFCT([
    'idEstudiante'  => $idEstudiante,
    'idCiclo'       => $idCiclo,
    'empresa'       => $empresa,
    'idEmpresa'     => $idEmpresa,
    'tutorEmpresa'  => $tutorEmpresa,
    'emailTutorEmpresa' => $emailTutorEmpresa,
    'telefonoEmpresa'   => $telefonoEmpresa,
    'ciudadEmpresa' => $ciudadEmpresa,
    'fechaInicio'   => $fechaInicio,
    'fechaFin'      => $fechaFin,
    'horasTotales'  => $horasTotales,
    'fase'          => $fase,
    'idProfesorTutor' => $idProfesorTutor,
]);

if ($idFCT) {
    $_SESSION['exito'] = 'FCT dada de alta correctamente.';
} else {
    $_SESSION['errores'] = 'No se pudo guardar la FCT. Comprueba que no exista ya una para este estudiante y esta fase.';
    $_SESSION['datos_fct'] = $_POST;
}

header('Location: ../../../vistas/admin/fct/lista.php?idCiclo=' . $idCiclo);
exit;
