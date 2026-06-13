<?php

include_once 'db/socket.php';

$dbTableName = 'espera';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Datos del alumnado
$subsNombre = trim((string) (isset($_POST['nombre']) ? $_POST['nombre'] : ''));
$subsApellidos = trim((string) (isset($_POST['apellidos']) ? $_POST['apellidos'] : ''));
$subsDni = strtoupper(trim((string) (isset($_POST['dni']) ? $_POST['dni'] : '')));
$subsTelefono = trim((string) (isset($_POST['telefono']) ? $_POST['telefono'] : ''));
$subsEmail = trim((string) (isset($_POST['email']) ? $_POST['email'] : ''));
$subsEstudios = trim((string) (isset($_POST['estudios']) ? $_POST['estudios'] : ''));
$subsPreinscripcion = trim((string) (isset($_POST['preinscripcion']) ? $_POST['preinscripcion'] : ''));
$subsCiclo1 = trim((string) (isset($_POST['ciclo_1']) ? $_POST['ciclo_1'] : ''));
$subsCiclo2 = trim((string) (isset($_POST['ciclo_2']) ? $_POST['ciclo_2'] : ''));
$subsCiclo3 = trim((string) (isset($_POST['ciclo_3']) ? $_POST['ciclo_3'] : ''));

if ($subsDni === '') {
    header('Location: admision_denegada.php');
    exit;
}

mysqli_report(MYSQLI_REPORT_OFF);

$mysqli = null;
try {
    $mysqli = @new mysqli($db_host, $db_user, $db_password, $db_name);
} catch (Exception $e) {
    $mysqli = null;
}

if (!$mysqli || $mysqli->connect_errno) {
    try {
        $mysqli = @new mysqli($db_host, 'root', '', $db_name);
    } catch (Exception $e) {
        $mysqli = null;
    }
}

if (!$mysqli || $mysqli->connect_errno) {
    http_response_code(500);
    exit('No se ha podido conectar a la base de datos');
}

$mysqli->set_charset('utf8');

// Evitar duplicados por DNI en lista de espera
$checkStmt = $mysqli->prepare("SELECT 1 FROM {$dbTableName} WHERE DNI = ? LIMIT 1");
if (!$checkStmt) {
    $mysqli->close();
    http_response_code(500);
    exit('Error al preparar la consulta.');
}

$checkStmt->bind_param('s', $subsDni);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult && $checkResult->num_rows > 0) {
    $checkStmt->close();
    $mysqli->close();
    header('Location: confirmacion_espera.html');
    exit;
}

$checkStmt->close();

$insertStmt = $mysqli->prepare(
    "INSERT INTO {$dbTableName} (Nombre, Apellidos, DNI, Telefono, Email, Estudios, Ciclo_1, Ciclo_2, Ciclo_3, Preinscripcion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$insertStmt) {
    $mysqli->close();
    http_response_code(500);
    exit('Error al preparar el alta en lista de espera.');
}

$insertStmt->bind_param(
    'ssssssssss',
    $subsNombre,
    $subsApellidos,
    $subsDni,
    $subsTelefono,
    $subsEmail,
    $subsEstudios,
    $subsCiclo1,
    $subsCiclo2,
    $subsCiclo3,
    $subsPreinscripcion
);

if (!$insertStmt->execute()) {
    $insertStmt->close();
    $mysqli->close();
    http_response_code(500);
    exit('Error al guardar los datos.');
}

$insertStmt->close();
$mysqli->close();

header('Location: confirmacion_espera.html');
exit;
?>