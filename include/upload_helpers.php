<?php
// Shared upload logic for reto files.
// Requires registrarArchivoReto() to be defined (include modelos/retos.php first).
require_once __DIR__ . '/ImageOptimizer.php';

const RETO_UPLOAD_EXTENSIONES = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'zip'];
const RETO_UPLOAD_MIMES = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg', 'image/png', 'image/gif',
    'application/zip', 'application/x-zip-compressed',
];

function procesarArchivosReto(int $idReto): void {
    if (empty($_FILES['archivosReto']['name'][0])) return;
    $uploadDir = __DIR__ . '/../public/uploads/retos/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    foreach ($_FILES['archivosReto']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['archivosReto']['error'][$key] !== UPLOAD_ERR_OK) continue;
        $fileName = $_FILES['archivosReto']['name'][$key];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExt, RETO_UPLOAD_EXTENSIONES)) continue;
        if (!in_array(mime_content_type($tmpName), RETO_UPLOAD_MIMES)) continue;
        $newFileName = bin2hex(random_bytes(8)) . '.' . $fileExt;
        if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
            $tipo = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']) ? 'imagen' : 'pdf';
            $imgMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
            if (isset($imgMimes[$fileExt])) ImageOptimizer::optimize($uploadDir . $newFileName, $imgMimes[$fileExt]);
            registrarArchivoReto($idReto, $fileName, 'public/uploads/retos/' . $newFileName, $tipo);
        }
    }
}
