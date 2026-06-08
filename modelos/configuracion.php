<?php
require_once __DIR__ . '/conectar.php';

function obtenerConfiguracionCentro() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT * FROM configuracion_centro WHERE idConfig = 1");
    $cfg = mysqli_fetch_assoc($res);
    return $cfg ?: [
        'nombreCentro' => 'Centro de Formación Profesional',
        'codigoCentro' => '', 'direccionCentro' => '', 'ciudadCentro' => '',
        'cpCentro' => '', 'telefonoCentro' => '', 'emailCentro' => '',
        'cursoEscolar' => date('Y') . '-' . (date('Y') + 1),
        'logoCentro' => '', 'logoGobierno1' => '', 'logoGobierno2' => '',
        'textoLegal' => '', 'nombreDirectorFirmante' => '',
    ];
}

function guardarConfiguracionCentro($d) {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT idConfig FROM configuracion_centro WHERE idConfig = 1");
    $sql = mysqli_num_rows($res) === 0
        ? "INSERT INTO configuracion_centro (nombreCentro,codigoCentro,direccionCentro,ciudadCentro,cpCentro,telefonoCentro,emailCentro,cursoEscolar,textoLegal,nombreDirectorFirmante,idConfig) VALUES (?,?,?,?,?,?,?,?,?,?,1)"
        : "UPDATE configuracion_centro SET nombreCentro=?,codigoCentro=?,direccionCentro=?,ciudadCentro=?,cpCentro=?,telefonoCentro=?,emailCentro=?,cursoEscolar=?,textoLegal=?,nombreDirectorFirmante=? WHERE idConfig=1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssssss',
        $d['nombreCentro'], $d['codigoCentro'], $d['direccionCentro'],
        $d['ciudadCentro'], $d['cpCentro'], $d['telefonoCentro'],
        $d['emailCentro'], $d['cursoEscolar'], $d['textoLegal'],
        $d['nombreDirectorFirmante']);
    return mysqli_stmt_execute($stmt);
}

function actualizarLogoCentro($campo, $ruta) {
    $con = obtenerConexion();
    if (!in_array($campo, ['logoCentro', 'logoGobierno1', 'logoGobierno2'])) return false;
    $sql = "UPDATE configuracion_centro SET $campo = ? WHERE idConfig = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $ruta);
    return mysqli_stmt_execute($stmt);
}

function logoParaPdf($ruta) {
    if (empty($ruta)) return '';
    $path = __DIR__ . '/../public/uploads/configuracion/' . basename($ruta);
    if (!file_exists($path)) return '';
    $mime = mime_content_type($path) ?: 'image/png';
    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
}
