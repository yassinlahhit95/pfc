<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/gastos.php';
require_once __DIR__ . '/../../modelos/ciclos.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('No tienes permisos para acceder a los gastos.', 403, 'forbidden');
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET') {
    if ($action === 'list') {
        $anyo = isset($_GET['anyo']) ? (int)$_GET['anyo'] : null;
        $gastos = listarGastos($anyo);
        $categorias = listarCategorias();
        $ciclos = listarCiclos();
        v1Ok([
            'gastos' => $gastos,
            'categorias' => $categorias,
            'ciclos' => $ciclos
        ]);
    }
} elseif ($method === 'POST') {
    if ($action === 'create') {
        $idCategoria = (int)($_POST['idCategoria'] ?? 0);
        $idCiclo = !empty($_POST['idCiclo']) ? (int)$_POST['idCiclo'] : null;
        $concepto = trim($_POST['concepto'] ?? '');
        $importe = floatval($_POST['importe'] ?? 0);
        $fecha = trim($_POST['fecha'] ?? date('Y-m-d'));
        $tipoJustificante = trim($_POST['tipoJustificante'] ?? 'Ninguno');
        $numeroReferencia = trim($_POST['numeroReferencia'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');

        if ($idCategoria <= 0 || empty($concepto) || $importe <= 0) {
            v1Error('Concepto, importe y categoría son obligatorios.', 400, 'validation');
        }

        $archivoJustificante = null;
        if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['archivo'];
            if ($file['size'] > 8 * 1024 * 1024) {
                v1Error('El archivo es demasiado grande (máx 8 MB).', 400, 'validation');
            }
            
            $mime = mime_content_type($file['tmp_name']);
            $mimeExtMap = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
            if (!isset($mimeExtMap[$mime])) {
                v1Error('Formato no soportado. Sube un PDF, JPG o PNG.', 400, 'validation');
            }

            require_once __DIR__ . '/../../include/ImageOptimizer.php';
            require_once __DIR__ . '/../../include/R2Client.php';
            if (in_array($mime, ['image/jpeg', 'image/png'], true)) {
                ImageOptimizer::optimize($file['tmp_name'], $mime);
            }
            
            $nombreArchivo = 'comp_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $mimeExtMap[$mime];
            $bytes = file_get_contents($file['tmp_name']);
            
            // Subir a R2 (S3 compatible)
            $subioR2 = $bytes !== false && R2Client::putObject('justificantes/' . $nombreArchivo, $bytes, $mime);
            
            // Subir local por compatibilidad con la web
            $localDir = __DIR__ . "/../../public/uploads/justificantes";
            if (!is_dir($localDir)) mkdir($localDir, 0755, true);
            file_put_contents("$localDir/$nombreArchivo", $bytes);
            
            @unlink($file['tmp_name']);
            
            // Guardarlo como array JSON ya que la web admite múltiples
            $archivoJustificante = json_encode([$nombreArchivo]);
        }

        $id = insertarGasto($idCategoria, $idCiclo, $concepto, $importe, $fecha, $tipoJustificante, $numeroReferencia, $archivoJustificante, $observaciones);
        if ($id) {
            v1Ok(['message' => 'Gasto registrado correctamente', 'idGasto' => $id], 201);
        } else {
            v1Error('No se pudo registrar el gasto en la base de datos.', 500, 'error');
        }
    }
}

v1Error('Ruta no válida.', 400, 'validation');
