<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/aula.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!Security::verifyCSRFToken($csrf)) {
    echo json_encode(['ok' => false, 'msg' => 'Token CSRF inválido']);
    exit;
}

$tipo = $_POST['tipo'] ?? ''; // 'carpeta' o 'archivo'
$idElemento = intval($_POST['idElemento'] ?? 0);
$idDestino = intval($_POST['idDestino'] ?? 0);

if ($idDestino === 0) {
    $idDestino = null; // Raíz
}

if ($idElemento <= 0 || !in_array($tipo, ['carpeta', 'archivo'])) {
    echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']);
    exit;
}

try {
    if ($tipo === 'carpeta') {
        // No se puede mover una carpeta dentro de sí misma
        if ($idDestino === $idElemento) {
            echo json_encode(['ok' => false, 'msg' => 'No puedes mover una carpeta dentro de sí misma.']);
            exit;
        }
        // Solo el profesor propietario puede mover la carpeta
        $carpeta = obtenerCarpetaAulaPorId($idElemento);
        if (!$carpeta || $carpeta['idProfesor'] != $idProfesor) {
            echo json_encode(['ok' => false, 'msg' => 'No tienes permiso sobre este elemento.']);
            exit;
        }
        moverCarpetaAula($idElemento, $idDestino);
    } else {
        // Solo el profesor propietario puede mover el archivo
        $archivo = obtenerArchivoPorId($idElemento);
        if (!$archivo || $archivo['idProfesor'] != $idProfesor) {
            echo json_encode(['ok' => false, 'msg' => 'No tienes permiso sobre este elemento.']);
            exit;
        }
        moverArchivoAula($idElemento, $idDestino);
    }
    echo json_encode(['ok' => true, 'msg' => 'Movido con éxito']);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'msg' => 'Error al mover el elemento.']);
}
