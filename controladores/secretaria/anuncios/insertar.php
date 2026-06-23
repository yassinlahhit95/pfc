<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/anuncios/agregarAnuncio.php");
    exit;
}

$titulo    = Security::sanitize($_POST['titulo'] ?? '');
$mensaje   = Security::sanitize($_POST['mensaje'] ?? '');
$dirigidoA = Security::sanitize($_POST['dirigidoA'] ?? 'todos');

$opcValidas = ['todos', 'estudiantes', 'profesores'];
if (!in_array($dirigidoA, $opcValidas)) $dirigidoA = 'todos';

$errores = [];
if (empty($titulo))  $errores[] = "El título es obligatorio.";
if (empty($mensaje)) $errores[] = "El contenido es obligatorio.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/anuncios/agregarAnuncio.php");
    exit;
}

$ok = insertarAnuncio($titulo, $mensaje, $dirigidoA);

if ($ok) {
    $_SESSION['exito'] = "Aviso publicado correctamente.";
} else {
    $_SESSION['errores'] = "Error al publicar el aviso.";
}
header("Location: ../../../vistas/secretaria/anuncios/gestionAnuncios.php");
exit;
