<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/aula.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    v1Error('Método no permitido.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

// Listar todas las tareas (global)
if ($type === 'estudiante') {
    $con = obtenerConexion();
    $sql = "SELECT t.*, m.nombreModulo, p.nombreProfesor 
            FROM aula_tareas t
            JOIN modulos m ON t.idModulo = m.idModulo
            JOIN estudiantes e ON m.idCiclo = e.idCiclo
            JOIN profesores p ON t.idProfesor = p.idProfesor
            WHERE e.idEstudiante = ? AND t.publicado = 1
            ORDER BY t.fechaCreacion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    $tareas = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        // Adjuntar si ya está entregado
        $idTarea = (int)$fila['idTarea'];
        $entrega = obtenerEntregaAula($idTarea, $uid);
        if ($entrega) {
            $fila['entregado'] = true;
            $fila['nota'] = $entrega['nota'];
            $fila['miEntrega'] = $entrega;
        } else {
            $fila['entregado'] = false;
        }
        $tareas[] = $fila;
    }
    v1Ok(['tasks' => $tareas]);
} elseif ($type === 'profesor') {
    $con = obtenerConexion();
    $sql = "SELECT t.*, m.nombreModulo, p.nombreProfesor 
            FROM aula_tareas t
            JOIN modulos m ON t.idModulo = m.idModulo
            JOIN profesores p ON t.idProfesor = p.idProfesor
            WHERE t.idProfesor = ?
            ORDER BY t.fechaCreacion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    $tareas = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $tareas[] = $fila;
    }
    v1Ok(['tasks' => $tareas]);
}

v1Error('No disponible para este rol.', 403, 'forbidden');
