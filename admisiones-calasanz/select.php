<?php

include_once 'db/socket.php';

$dbTableName = 'admisiones';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$dni = isset($_POST['dni']) ? strtoupper(trim((string) $_POST['dni'])) : '';

if ($dni === '') {
    header('Location: admision_denegada.php');
    exit;
}

if (!preg_match('/^(([X-Z]\d{7})|(\d{8}))[A-Z]$/', $dni)) {
    header('Location: admision_denegada.php');
    exit;
}

$mysqli = null;

mysqli_report(MYSQLI_REPORT_OFF);

try {
    $mysqli = @new mysqli($db_host, $db_user, $db_password, $db_name);
} catch (Exception $e) {
    $mysqli = null;
}

if (!$mysqli || $mysqli->connect_errno) {
    // Fallback rápido para entornos locales (XAMPP) donde no existe el usuario dedicado.
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

$stmt = $mysqli->prepare("SELECT nombre, apellidos, ciclo FROM {$dbTableName} WHERE DNI = ? LIMIT 1");

if (!$stmt) {
    $mysqli->close();
    http_response_code(500);
    exit('Error al preparar la consulta.');
}

$stmt->bind_param('s', $dni);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = urlencode((string) (isset($row['nombre']) ? $row['nombre'] : ''));
    $apellidos = urlencode((string) (isset($row['apellidos']) ? $row['apellidos'] : ''));
    $ciclo = urlencode((string) (isset($row['ciclo']) ? $row['ciclo'] : ''));
    $dni_encoded = urlencode($dni);

    $stmt->close();
    $mysqli->close();

    session_start();
    $_SESSION['dni_usuario'] = $dni;
    unset($_SESSION['paso1']);
    unset($_SESSION['paso2']);
    unset($_SESSION['paso3']);

    header('Location: admision_aprobada.php?nombre=' . $nombre . '&apellidos=' . $apellidos . '&ciclo=' . $ciclo . '&dni=' . $dni_encoded);
    exit;
}

$stmt->close();
$mysqli->close();

header('Location: admision_denegada.php');
exit;
?>