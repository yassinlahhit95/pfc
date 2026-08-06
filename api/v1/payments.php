<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/pagos.php';
require_once __DIR__ . '/../../modelos/tutores.php';
require_once __DIR__ . '/../../modelos/log.php';
require_once __DIR__ . '/../../include/R2Client.php';

function comprobanteUrl(string $archivo): string {
    $archivoNombre = basename($archivo);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $urlLocal = "$scheme://$host/public/uploads/comprobantes/$archivoNombre";
    return R2Client::documentoUrl(
        __DIR__ . '/../../public/uploads/comprobantes/' . $archivoNombre,
        $urlLocal,
        'comprobantes/' . $archivoNombre
    );
}

function notificarComprobantePagoResuelto(int $idEstudiante, int $idPago, bool $aprobar, string $motivoRechazo): void {
    $fh = __DIR__ . '/../../controladores/firebase/firebase_helper.php';
    if (!file_exists($fh)) return;
    require_once $fh;

    $titulo = $aprobar ? 'Comprobante aprobado' : 'Comprobante rechazado';
    $mensaje = $aprobar
        ? 'Tu comprobante de pago ha sido verificado y aprobado.'
        : 'Tu comprobante de pago ha sido rechazado' . ($motivoRechazo !== '' ? ": $motivoRechazo" : '.');

    $con = obtenerConexion();
    $destinatarios = [['id' => $idEstudiante, 'rol' => 'estudiante']];
    $st = mysqli_prepare($con, 'SELECT idTutor FROM estudiante_tutor WHERE idEstudiante = ?');
    mysqli_stmt_bind_param($st, 'i', $idEstudiante);
    mysqli_stmt_execute($st);
    foreach (mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC) as $row) {
        $destinatarios[] = ['id' => (int)$row['idTutor'], 'rol' => 'tutor'];
    }
    foreach ($destinatarios as $d) {
        $token = obtenerTokenUsuario($d['id'], $d['rol']);
        if ($token) {
            enviarNotificacionFirebase($token, $titulo, $mensaje, 'pago_comprobante_resuelto', ['idPago' => $idPago]);
        }
    }
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
v1RequireFeature('feature_pagos');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($type === 'director' || $type === 'secretaria') {
        if ($action === 'paginated') {
            $limit   = min(max((int)($_GET['limit']  ?? 20), 1), 100);
            $offset  = max((int)($_GET['offset'] ?? 0), 0);
            $status  = strtolower(trim($_GET['status'] ?? ''));
            $ciclo   = (int)($_GET['ciclo'] ?? 0);

            $con = obtenerConexion();
            $where = [];
            $params = [];
            $types = '';

            if ($status === 'pagado') {
                $where[] = "p.estadoComprobante = 'aprobado'";
            } elseif ($status === 'vencido') {
                $where[] = "p.fechaProximoPago < CURDATE() AND p.estadoComprobante != 'aprobado'";
            } elseif ($status === 'pendiente') {
                $where[] = "p.fechaProximoPago >= CURDATE() AND p.estadoComprobante != 'aprobado'";
            }

            if ($ciclo > 0) {
                $where[] = 'e.idCiclo = ?';
                $params[] = $ciclo;
                $types .= 'i';
            }

            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
            $sql = "SELECT p.idPago, p.idEstudiante, e.nombreEstudiante,
                           p.monto, p.fechaPago, p.fechaProximoPago, p.tipoPago,
                           p.estadoComprobante, c.nombreCiclo, c.abreviaturaCiclo,
                           CASE
                               WHEN p.estadoComprobante = 'aprobado' THEN 'pagado'
                               WHEN p.fechaProximoPago < CURDATE() AND p.estadoComprobante != 'aprobado' THEN 'vencido'
                               ELSE 'pendiente'
                           END as estado
                    FROM pagos p
                    JOIN estudiantes e ON p.idEstudiante = e.idEstudiante
                    JOIN ciclos c ON e.idCiclo = c.idCiclo
                    $whereClause
                    ORDER BY p.fechaProximoPago ASC, e.nombreEstudiante ASC
                    LIMIT ? OFFSET ?";

            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';

            $st = mysqli_prepare($con, $sql);
            if (!$st) v1Error('Database query failed.', 500, 'error');
            mysqli_stmt_bind_param($st, $types, ...$params);
            mysqli_stmt_execute($st);
            $rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

            $countSql = "SELECT COUNT(*) as cnt FROM pagos p
                         JOIN estudiantes e ON p.idEstudiante = e.idEstudiante
                         JOIN ciclos c ON e.idCiclo = c.idCiclo
                         $whereClause";
            $countParams = array_slice($params, 0, -2);
            $countTypes = substr($types, 0, -2);

            if ($countParams) {
                $st2 = mysqli_prepare($con, $countSql);
                mysqli_stmt_bind_param($st2, $countTypes, ...$countParams);
                mysqli_stmt_execute($st2);
                $countResult = mysqli_fetch_assoc(mysqli_stmt_get_result($st2));
                $total = $countResult['cnt'] ?? 0;
            } else {
                $st2 = mysqli_prepare($con, $countSql);
                mysqli_stmt_execute($st2);
                $countResult = mysqli_fetch_assoc(mysqli_stmt_get_result($st2));
                $total = $countResult['cnt'] ?? 0;
            }

            $payments = [];
            foreach ($rows as $row) {
                $payments[] = [
                    'idPago' => (int)$row['idPago'],
                    'idEstudiante' => (int)$row['idEstudiante'],
                    'nombreEstudiante' => $row['nombreEstudiante'],
                    'monto' => $row['monto'],
                    'fechaPago' => $row['fechaPago'],
                    'fechaProximoPago' => $row['fechaProximoPago'],
                    'tipoPago' => $row['tipoPago'],
                    'nombreCiclo' => $row['nombreCiclo'],
                    'abreviaturaCiclo' => $row['abreviaturaCiclo'],
                    'estado' => $row['estado'],
                    'estadoComprobante' => $row['estadoComprobante'],
                ];
            }
            v1Ok([
                'payments' => $payments,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
            ]);
        }

        if (isset($_GET['pending'])) {
            v1Ok(['pending' => listarEstudiantesConPagosPendientes()]);
        }
        $idCiclo = isset($_GET['idCiclo']) ? (int)$_GET['idCiclo'] : null;
        $payments = $idCiclo ? listarPagosFiltrados($idCiclo) : listarTodosLosPagos();
        foreach ($payments as &$p) {
            if (!empty($p['comprobante'])) $p['comprobante_url'] = comprobanteUrl($p['comprobante']);
        }
        unset($p);
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
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? ($_GET['action'] ?? '');
    
    // Resuelve la acción por defecto para multipart/form-data cuando no viene definida explícitamente pero se puede inferir
    if (!$action) {
        if (isset($_POST['idEstudiante']) && isset($_POST['monto'])) $action = 'cobrar';
        elseif (isset($_POST['idPago']) && !isset($_POST['aprobar'])) $action = 'uploadComprobante';
        else {
            $body = v1Body();
            $action = $body['action'] ?? '';
            if (!$action && isset($body['aprobar'])) $action = 'resolveComprobante';
        }
    }

    if ($action === 'cobrar') {
        if ($type !== 'director' && $type !== 'secretaria') {
            v1Error('Only director and secretaria can record payments.', 403, 'forbidden');
        }

        $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
        $monto = floatval($_POST['monto'] ?? 0);
        $tipoPago = trim((string)($_POST['tipoPago'] ?? ''));
        $fechaProximoPago = !empty($_POST['fechaProximoPago']) ? trim((string)$_POST['fechaProximoPago']) : null;
        
        if (empty($fechaProximoPago)) {
            if ($tipoPago === 'mensual') $fechaProximoPago = date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 month'));
            elseif ($tipoPago === 'trimestral') $fechaProximoPago = date('Y-m-d', strtotime(date('Y-m-d') . ' + 3 months'));
            elseif ($tipoPago === 'semestral') $fechaProximoPago = date('Y-m-d', strtotime(date('Y-m-d') . ' + 6 months'));
        }

        if ($idEstudiante <= 0 || $monto <= 0 || $tipoPago === '') v1Error('Invalid parameters.', 400, 'validation');
        $tiposPermitidos = ['mensual', 'trimestral', 'semestral', 'unico'];
        if (!in_array($tipoPago, $tiposPermitidos, true)) v1Error('Invalid tipoPago.', 400, 'validation');

        $con = obtenerConexion();
        $stEst = mysqli_prepare($con, 'SELECT idEstudiante FROM estudiantes WHERE idEstudiante = ? AND eliminado = 0');
        mysqli_stmt_bind_param($stEst, 'i', $idEstudiante);
        mysqli_stmt_execute($stEst);
        if (!mysqli_fetch_assoc(mysqli_stmt_get_result($stEst))) v1Error('Student not found.', 404, 'not_found');

        $archivo = null;
        if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['archivo'];
            if ($file['size'] > 8 * 1024 * 1024) v1Error('File too large (max 8 MB).', 400, 'validation');
            $mime = mime_content_type($file['tmp_name']);
            $mimeExtMap = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
            if (!isset($mimeExtMap[$mime])) v1Error('Unsupported file type.', 400, 'validation');

            require_once __DIR__ . '/../../include/ImageOptimizer.php';
            if (in_array($mime, ['image/jpeg', 'image/png'], true)) ImageOptimizer::optimize($file['tmp_name'], $mime);
            
            $archivo = 'comp_' . $idEstudiante . '_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
            $bytes = file_get_contents($file['tmp_name']);
            $subioOk = $bytes !== false && R2Client::putObject('comprobantes/' . $archivo, $bytes, $mime);
            @unlink($file['tmp_name']);
            if (!$subioOk) v1Error('Could not upload the file.', 500, 'error');
        }

        $fechaPago = date('Y-m-d');
        if (registrarCobroPago($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximoPago, $archivo)) {
            v1Ok(['message' => 'Payment registered successfully.'], 201);
        }
        v1Error('Could not record the payment.', 500, 'error');
    }

    if ($action === 'uploadComprobante') {
        if ($type !== 'estudiante' && $type !== 'tutor') {
            v1Error('Only estudiantes and tutores can upload a comprobante.', 403, 'forbidden');
        }

        $idPago = (int)($_POST['idPago'] ?? 0);
        if ($idPago <= 0) v1Error('idPago is required.', 400, 'validation');

        $pago = obtenerPagoPorId($idPago);
        if (!$pago) v1Error('Payment not found.', 404, 'not_found');
        $idEstudiante = (int)$pago['idEstudiante'];

        if ($type === 'estudiante') {
            if ($idEstudiante !== $uid) v1Error('You do not have access to this payment.', 403, 'forbidden');
        } else {
            $hijos  = listarEstudiantesPorTutor($uid);
            $esHijo = in_array($idEstudiante, array_map(fn($h) => (int)$h['idEstudiante'], $hijos), true);
            if (!$esHijo) v1Error('You do not have access to this student.', 403, 'forbidden');
        }

        if (empty($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            v1Error('archivo is required.', 400, 'validation');
        }

        $file = $_FILES['archivo'];
        if ($file['size'] > 8 * 1024 * 1024) v1Error('File too large (max 8 MB).', 400, 'validation');
        $mime = mime_content_type($file['tmp_name']);
        $mimeExtMap = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (!isset($mimeExtMap[$mime])) v1Error('Unsupported file type.', 400, 'validation');

        require_once __DIR__ . '/../../include/ImageOptimizer.php';
        if (in_array($mime, ['image/jpeg', 'image/png'], true)) ImageOptimizer::optimize($file['tmp_name'], $mime);

        $archivo = 'comp_' . $idEstudiante . '_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
        $bytes = file_get_contents($file['tmp_name']);
        $subioOk = $bytes !== false && R2Client::putObject('comprobantes/' . $archivo, $bytes, $mime);
        @unlink($file['tmp_name']);
        if (!$subioOk) v1Error('Could not upload the file.', 500, 'error');

        if (!subirComprobantePago($idPago, $archivo)) v1Error('Could not save the comprobante.', 500, 'error');
        v1Ok(['message' => 'Comprobante uploaded. Pending verification.'], 201);
    }

    if ($action === 'resolveComprobante') {
        if ($type !== 'director' && $type !== 'secretaria') {
            v1Error('Only director/secretaria can resolve a comprobante.', 403, 'forbidden');
        }
        $body = v1Body();
        $idPago = (int)($body['idPago'] ?? 0);
        $aprobar = $body['aprobar'] ?? null;
        $motivoRechazo = trim((string)($body['motivoRechazo'] ?? ''));

        if ($idPago <= 0 || !is_bool($aprobar)) v1Error('idPago and aprobar are required.', 400, 'validation');
        if (!$aprobar && $motivoRechazo === '') v1Error('motivoRechazo is required when rejecting.', 400, 'validation');

        $pago = obtenerPagoPorId($idPago);
        if (!$pago) v1Error('Payment not found.', 404, 'not_found');
        if ($pago['estadoComprobante'] !== 'verificando') {
            v1Error('This comprobante has already been resolved or none was uploaded.', 409, 'validation');
        }

        $ok = resolverComprobantePago($idPago, $aprobar, $aprobar ? null : $motivoRechazo);
        if (!$ok) v1Error('Could not resolve the comprobante.', 500, 'error');

        $accion = $aprobar ? 'aprobar_comprobante' : 'rechazar_comprobante';
        if ($type === 'director') {
            registrarAccion($accion, 'pagos', $idPago, $motivoRechazo, $uid);
        } else {
            registrarAccionSecretaria($accion, 'pagos', $idPago, $motivoRechazo, $uid);
        }

        notificarComprobantePagoResuelto((int)$pago['idEstudiante'], $idPago, $aprobar, $motivoRechazo);
        v1Ok(['message' => $aprobar ? 'Approved.' : 'Rejected.']);
    }

    v1Error('Action not supported.', 400, 'validation');
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
