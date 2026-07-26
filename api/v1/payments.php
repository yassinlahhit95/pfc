<?php
declare(strict_types=1);

// GET /api/v1/payments.php
//   director/secretaria → full payment management view (read-only for now;
//     registering/editing payments stays web-only in this first mobile pass)
//     ?idCiclo=  — filter by ciclo (optional)
//     ?pending=1 — students with outstanding balance instead of the payment list
//   estudiante → own payment history + running balance
//   tutor      → payment history + balance for each linked child
//   profesor   → 403 (not relevant to this role)

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/pagos.php';
require_once __DIR__ . '/../../modelos/tutores.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
v1RequireFeature('feature_pagos');

if ($type === 'director' || $type === 'secretaria') {
    if (isset($_GET['pending'])) {
        v1Ok(['pending' => listarEstudiantesConPagosPendientes()]);
    }
    $idCiclo = isset($_GET['idCiclo']) ? (int)$_GET['idCiclo'] : null;
    $payments = $idCiclo ? listarPagosFiltrados($idCiclo) : listarTodosLosPagos();
    v1Ok(['payments' => $payments]);
}

if ($type === 'estudiante') {
    v1Ok([
        'payments' => listarPagosPorEstudiante($uid),
        'estado' => obtenerEstadoFinancieroEstudiante($uid),
    ]);
}

if ($type === 'tutor') {
    $hijos = listarEstudiantesPorTutor($uid);
    $resultado = [];
    foreach ($hijos as $h) {
        $idHijo = (int)$h['idEstudiante'];
        $resultado[] = [
            'idEstudiante' => $idHijo,
            'nombreEstudiante' => $h['nombreEstudiante'],
            'payments' => listarPagosPorEstudiante($idHijo),
            'estado' => obtenerEstadoFinancieroEstudiante($idHijo),
        ];
    }
    v1Ok(['students' => $resultado]);
}

v1Error('This endpoint is not available for this role.', 403, 'forbidden');
