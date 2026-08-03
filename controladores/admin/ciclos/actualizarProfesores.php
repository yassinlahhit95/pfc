<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/ciclos/verCiclos.php"); exit;
}

$idCiclo       = (int)($_POST['idCiclo'] ?? 0);
$idsProfesores = isset($_POST['idsProfesores']) && is_array($_POST['idsProfesores'])
    ? array_map('intval', array_filter($_POST['idsProfesores'], 'is_numeric'))
    : [];

if (!$idCiclo) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'ID de ciclo no válido.']); exit; }
    header("Location: ../../../vistas/admin/ciclos/verCiclos.php"); exit;
}

$ok = actualizarProfesoresDeCiclo($idCiclo, $idsProfesores);
if ($ok) registrarAccion('actualizar_profesores', 'ciclos', $idCiclo);

if ($isAjax) {
    $nombres = listarNombresProfesoresCiclo($idCiclo);
    header('Content-Type: application/json');
    echo json_encode([
        'ok'      => $ok,
        'msg'     => $ok ? 'Profesores actualizados correctamente.' : 'Error al actualizar los profesores.',
        'nombres' => $nombres,
    ]);
    exit;
}

$_SESSION[$ok ? 'exito' : 'errores'] = $ok
    ? 'Profesores del ciclo actualizados.'
    : 'Error al actualizar los profesores.';
header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
