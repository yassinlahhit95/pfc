<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_fct');
require_once __DIR__ . "/../../../modelos/fct.php";

$idFCT = (int)($_POST['idFCT'] ?? 0);
$fct = $idFCT ? obtenerFCTPorId($idFCT) : null;

if (!$fct) {
    $_SESSION['errores'] = 'FCT no encontrada.';
    header('Location: ../../../vistas/admin/fct/lista.php');
    exit;
}

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
$horasRealizadas = ($_POST['horasRealizadas'] ?? '') !== '' ? (int)$_POST['horasRealizadas'] : null;

$notaRaw = str_replace(',', '.', trim($_POST['nota'] ?? ''));
$nota = null;
$errores = [];
if ($empresa === '') $errores['empresa'] = 'El nombre de la empresa es obligatorio.';
if (!empty($emailTutorEmpresa) && !Security::validateEmail($emailTutorEmpresa)) $errores['emailTutorEmpresa'] = 'Email no válido.';
if ($notaRaw !== '') {
    if (!is_numeric($notaRaw) || $notaRaw < 0 || $notaRaw > 10) {
        $errores['nota'] = 'La nota debe estar entre 0 y 10.';
    } else {
        $nota = (float)$notaRaw;
    }
}
$aptoRaw = $_POST['apto'] ?? '';
$apto = ($aptoRaw === '1') ? true : (($aptoRaw === '0') ? false : null);
$observaciones = trim($_POST['observaciones'] ?? '') ?: null;

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_fct'] = $_POST;
    header('Location: ../../../vistas/admin/fct/editar.php?id=' . $idFCT);
    exit;
}

$ok1 = actualizarFCT($idFCT, [
    'empresa'       => $empresa,
    'idEmpresa'     => $idEmpresa,
    'tutorEmpresa'  => $tutorEmpresa,
    'emailTutorEmpresa' => $emailTutorEmpresa,
    'telefonoEmpresa'   => $telefonoEmpresa,
    'ciudadEmpresa' => $ciudadEmpresa,
    'fechaInicio'   => $fechaInicio,
    'fechaFin'      => $fechaFin,
    'horasTotales'  => $horasTotales,
    'idProfesorTutor' => $idProfesorTutor,
]);
$ok2 = actualizarSeguimientoFCT($idFCT, $horasRealizadas, $nota, $apto, $observaciones);

if ($ok1 && $ok2) {
    $_SESSION['exito'] = 'FCT actualizada correctamente.';
} else {
    $_SESSION['errores'] = 'No se pudo guardar la FCT.';
    $_SESSION['datos_fct'] = $_POST;
}

header('Location: ../../../vistas/admin/fct/lista.php?idCiclo=' . (int)$fct['idCiclo']);
exit;
