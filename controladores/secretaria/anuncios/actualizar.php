<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_anuncios');
require_once __DIR__ . "/../../../modelos/anuncios.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/anuncios/gestionAnuncios.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/anuncios/gestionAnuncios.php"); exit;
}

$idAnuncio       = (int)($_POST['idAnuncio'] ?? 0);
$titulo          = Security::sanitize($_POST['titulo'] ?? '');
$mensaje         = Security::sanitize($_POST['mensaje'] ?? '');
$fechaExpiracion = Security::sanitize($_POST['fechaExpiracion'] ?? '');
$dirigidoA       = Security::sanitize($_POST['dirigidoA'] ?? 'todos');

$opcValidas = ['todos', 'estudiantes', 'profesores', 'tutores'];
if (!in_array($dirigidoA, $opcValidas)) $dirigidoA = 'todos';

$errores = [];
if ($idAnuncio <= 0) $errores[] = "Aviso no válido.";
if (empty($titulo))  $errores[] = "El título es obligatorio.";
if (empty($mensaje)) $errores[] = "El contenido es obligatorio.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/anuncios/modificarAnuncio.php?id=$idAnuncio");
    exit;
}

if (empty($fechaExpiracion)) {
    $fechaExpiracion = date('Y-m-d', strtotime('+1 month'));
}

$ok = actualizarAnuncio($idAnuncio, $titulo, $mensaje, $fechaExpiracion, $dirigidoA);

if ($ok) {
    $_SESSION['exito'] = "Aviso actualizado correctamente.";
} else {
    $_SESSION['errores'] = "Error al actualizar el aviso.";
}
header("Location: ../../../vistas/secretaria/anuncios/gestionAnuncios.php");
exit;
