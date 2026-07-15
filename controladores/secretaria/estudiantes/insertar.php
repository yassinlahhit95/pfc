<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/estudiantes/agregarEstudiantes.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/estudiantes/agregarEstudiantes.php"); exit;
}

$nombre          = Security::sanitize($_POST['nombre'] ?? '');
$email           = strtolower(trim($_POST['email'] ?? ''));
$idCiclo         = (int)($_POST['idCiclo'] ?? 0);
$dni             = Security::sanitize($_POST['dni'] ?? '');
$telefono        = Security::sanitize($_POST['telefono'] ?? '');
$direccion       = Security::sanitize($_POST['direccion'] ?? '');
$fechaNacimiento = Security::sanitize($_POST['fechaNacimiento'] ?? '');
$curso           = Security::sanitize($_POST['curso'] ?? 'Grado Medio');
$anioEstudioPost = Security::sanitize($_POST['anioEstudio'] ?? '');
$anioEstudio     = existeNombreCursoEnCiclo($idCiclo, $anioEstudioPost) ? $anioEstudioPost : '';
$fechaAlta       = date('Y-m-d');
$ciudad          = '';
$codigoPostal    = '';
$observaciones   = '';

$errores = [];
if (empty($nombre))   $errores[] = "El nombre es obligatorio.";
if (empty($email) || !Security::validateEmail($email)) $errores[] = "Email no válido.";
if ($idCiclo <= 0)    $errores[] = "Debes seleccionar un ciclo.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_estudiante'] = $_POST;
    header("Location: ../../../vistas/secretaria/estudiantes/agregarEstudiantes.php");
    exit;
}

$ok = insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso, $anioEstudio);

if ($ok) {
    require_once __DIR__ . "/../../../include/credenciales.php";
    registrarAccionSecretaria('insertar', 'estudiantes', null, $nombre);
    $_SESSION['exito'] = mensajeExitoConCredenciales("Estudiante añadido correctamente.");
} else {
    $_SESSION['errores'] = "Error al guardar el estudiante. El email o DNI pueden estar en uso.";
}
header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
exit;
