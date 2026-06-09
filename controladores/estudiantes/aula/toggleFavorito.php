<?php
require_once __DIR__ . "/../../../include/Security.php";
// Marca / desmarca un recurso como favorito (#9). POST + CSRF.
// Responde JSON si la petición es AJAX; si no, vuelve a la página de origen.
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$esAjax = !empty($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

function responderFavorito($esAjax, $ok, $destino, $extra = []) {
    if ($esAjax) {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['ok' => $ok], $extra));
    } else {
        header("Location: $destino");
    }
    exit;
}

if (empty($_SESSION['idEstudiante'])) {
    responderFavorito($esAjax, false, "../../../vistas/login.php", ['error' => 'auth']);
}
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    responderFavorito($esAjax, false, "../../../vistas/estudiantes/aula/recursos.php", ['error' => 'csrf']);
}

$idEstudiante = $_SESSION['idEstudiante'];
$idArchivo    = intval($_POST['idArchivo'] ?? 0);
$origen       = $_POST['origen'] ?? 'recursos';   // recursos | favoritos
$idModulo     = intval($_POST['idModulo'] ?? 0);
$carpeta      = intval($_POST['carpeta'] ?? 0);
$favorito     = null;                             // nuevo estado: 1 = favorito, 0 = no

if ($idArchivo > 0) {
    // El archivo debe existir y pertenecer al ciclo del estudiante
    $archivo = obtenerArchivoPorId($idArchivo);
    $datos   = obtenerEstudiantePorId($idEstudiante);
    $modulo  = $archivo ? obtenerModuloPorId($archivo['idModulo']) : null;

    if ($archivo && $modulo && $modulo['idCiclo'] == ($datos['idCiclo'] ?? -1)) {
        if (esFavoritoAula($idEstudiante, $idArchivo)) {
            quitarFavoritoAula($idEstudiante, $idArchivo);
            if (!$esAjax) $_SESSION['exito'] = "Recurso quitado de favoritos.";
            $favorito = 0;
        } else {
            marcarFavoritoAula($idEstudiante, $idArchivo);
            if (!$esAjax) $_SESSION['exito'] = "Recurso añadido a favoritos.";
            $favorito = 1;
        }
    } else {
        if (!$esAjax) $_SESSION['errores'] = "No tienes permiso sobre este recurso.";
    }
}

// Destino seguro (construido en el servidor, sin URLs arbitrarias)
if ($origen === 'favoritos') {
    $destino = "../../../vistas/estudiantes/aula/favoritos.php";
} else {
    $destino = "../../../vistas/estudiantes/aula/recursos.php?id=$idModulo";
    if ($carpeta) $destino .= "&carpeta=$carpeta";
}
responderFavorito($esAjax, $favorito !== null, $destino, ['favorito' => $favorito]);
