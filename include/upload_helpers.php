<?php
// Lógica de subida compartida para ficheros de retos.
// Requiere que registrarArchivoReto() esté definida (incluir antes modelos/retos.php).
require_once __DIR__ . '/ImageOptimizer.php';
require_once __DIR__ . '/R2Client.php';

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
    foreach ($_FILES['archivosReto']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['archivosReto']['error'][$key] !== UPLOAD_ERR_OK) continue;
        $fileName = $_FILES['archivosReto']['name'][$key];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExt, RETO_UPLOAD_EXTENSIONES)) continue;
        $mimeReal = mime_content_type($tmpName);
        if (!in_array($mimeReal, RETO_UPLOAD_MIMES)) continue;
        $newFileName = bin2hex(random_bytes(8)) . '.' . $fileExt;

        $imgMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
        if (isset($imgMimes[$fileExt])) ImageOptimizer::optimize($tmpName, $imgMimes[$fileExt]); // optimizar el temporal ANTES de subir a R2

        $bytes = file_get_contents($tmpName);
        $subioOk = $bytes !== false && R2Client::putObject('retos/' . $newFileName, $bytes, $mimeReal);
        @unlink($tmpName);

        if ($subioOk) {
            $tipo = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']) ? 'imagen' : 'pdf';
            // Se mantiene el mismo formato de ruta relativa que antes (no un
            // nombre de fichero suelto como en el resto de tablas) para no
            // romper la lectura de filas ya existentes con este formato.
            registrarArchivoReto($idReto, $fileName, 'public/uploads/retos/' . $newFileName, $tipo);
        }
    }
}
