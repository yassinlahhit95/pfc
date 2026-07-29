<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/conectar.php';

$auth = v1Auth();
['user_type' => $type] = $auth;

if ($type !== 'director' && $type !== 'secretaria' && $type !== 'admin') {
    v1Error('No tienes permisos.', 403, 'forbidden');
}

$con = obtenerConexion();

// Total Students
$res = mysqli_query($con, "SELECT COUNT(*) as c FROM estudiantes WHERE (eliminado = 0 OR eliminado IS NULL)");
$totalStudents = $res ? (int)mysqli_fetch_assoc($res)['c'] : 0;

// Total Teachers
$res = mysqli_query($con, "SELECT COUNT(*) as c FROM profesores");
$totalTeachers = $res ? (int)mysqli_fetch_assoc($res)['c'] : 0;

// Total Gastos (This Month)
$res = mysqli_query($con, "SELECT SUM(importe) as s FROM gastos WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())");
$gastosMes = $res ? (float)(mysqli_fetch_assoc($res)['s'] ?? 0) : 0;

// Total Pagos (This Month)
$res = mysqli_query($con, "SELECT SUM(monto) as s FROM historial_pagos WHERE MONTH(fechaPago) = MONTH(CURRENT_DATE()) AND YEAR(fechaPago) = YEAR(CURRENT_DATE())");
$pagosMes = $res ? (float)(mysqli_fetch_assoc($res)['s'] ?? 0) : 0;

v1Ok([
    'total_estudiantes' => $totalStudents,
    'total_profesores' => $totalTeachers,
    'gastos_mes' => $gastosMes,
    'pagos_mes' => $pagosMes,
]);
