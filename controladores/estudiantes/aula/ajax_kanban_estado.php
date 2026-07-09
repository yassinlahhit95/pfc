<?php
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . '/../../../modelos/conectar.php';

// Esto es una llamada AJAX de lectura/escritura ligera, pero igual liberamos sesión rápido si podemos.
// En este caso, escribimos en BD, no en SESSION, así que podemos cerrarla.
session_write_close();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$idEstudiante = (int)$_SESSION['idEstudiante'];
$idTarea = (int)($_POST['idTarea'] ?? 0);
$estado = $_POST['estado'] ?? 'todo';

if ($idTarea < 1 || !in_array($estado, ['todo', 'progress'])) {
    echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
    exit;
}

$db = obtenerConexion();

if ($estado === 'todo') {
    // Si vuelve a "todo", lo borramos de la tabla de progreso para ahorrar espacio
    $sql = "DELETE FROM aula_kanban_estado WHERE idEstudiante = ? AND idTarea = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idTarea);
    mysqli_stmt_execute($stmt);
} else {
    // Si va a "progress", lo insertamos (ON DUPLICATE KEY UPDATE por si acaso)
    $sql = "INSERT INTO aula_kanban_estado (idEstudiante, idTarea, estado) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE estado = VALUES(estado)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $idEstudiante, $idTarea, $estado);
    mysqli_stmt_execute($stmt);
}

echo json_encode(['ok' => true]);
