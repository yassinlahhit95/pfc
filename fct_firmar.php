<?php
require_once __DIR__ . "/modelos/conectar.php";
require_once __DIR__ . "/include/Security.php";
session_start();
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$exito = '';
$error = '';

if (empty($token)) {
    $error = "Enlace de firma inválido o ausente.";
} else {
    $con = obtenerConexion();

    // Fetch the logs associated with this token
    $stmt = mysqli_prepare($con, "
        SELECT d.*, f.empresa, f.tutorEmpresa, e.nombreEstudiante, f.idFCT
        FROM fct_diarios d
        INNER JOIN fct f ON d.idFCT = f.idFCT
        INNER JOIN estudiantes e ON f.idEstudiante = e.idEstudiante
        WHERE d.tokenAprobacion = ? AND d.estado = 'pendiente'
        ORDER BY d.fecha ASC
    ");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $diarios = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    if (empty($diarios)) {
        // Double check if they were already signed
        $error = "No hay registros pendientes de firma para este enlace. Es posible que ya hayan sido firmados o rechazados.";
    } else {
        $first = $diarios[0];
        $idFCT = (int)$first['idFCT'];
        $nombreEstudiante = $first['nombreEstudiante'];
        $empresa = $first['empresa'];
        $tutorEmpresa = $first['tutorEmpresa'];

        // Handle Form Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = trim($_POST['accion'] ?? '');
            $observaciones = trim($_POST['observaciones'] ?? '');

            if ($accion === 'aprobar') {
                mysqli_begin_transaction($con);
                try {
                    // Approve logs
                    $stmtApp = mysqli_prepare($con, "
                        UPDATE fct_diarios 
                        SET estado = 'aprobado', observacionesTutor = ?, tokenAprobacion = NULL 
                        WHERE tokenAprobacion = ? AND estado = 'pendiente'
                    ");
                    mysqli_stmt_bind_param($stmtApp, "ss", $observaciones, $token);
                    mysqli_stmt_execute($stmtApp);

                    // Recalculate total hours realized in FCT record
                    $stmtSum = mysqli_prepare($con, "
                        UPDATE fct 
                        SET horasRealizadas = (SELECT IFNULL(SUM(horas), 0) FROM fct_diarios WHERE idFCT = ? AND estado = 'aprobado') 
                        WHERE idFCT = ?
                    ");
                    mysqli_stmt_bind_param($stmtSum, "ii", $idFCT, $idFCT);
                    mysqli_stmt_execute($stmtSum);

                    mysqli_commit($con);
                    $exito = "¡Jornadas aprobadas y firmadas con éxito! Muchas gracias por su colaboración.";
                    $diarios = []; // Clear listing since they are processed
                } catch (\Throwable $e) {
                    mysqli_rollback($con);
                    $error = "Error al procesar la aprobación: " . $e->getMessage();
                }
            } elseif ($accion === 'rechazar') {
                mysqli_begin_transaction($con);
                try {
                    // Reject logs
                    $stmtRej = mysqli_prepare($con, "
                        UPDATE fct_diarios 
                        SET estado = 'rechazado', observacionesTutor = ?, tokenAprobacion = NULL 
                        WHERE tokenAprobacion = ? AND estado = 'pendiente'
                    ");
                    mysqli_stmt_bind_param($stmtRej, "ss", $observaciones, $token);
                    mysqli_stmt_execute($stmtRej);

                    mysqli_commit($con);
                    $exito = "Jornadas rechazadas. El estudiante ha sido notificado y recibirá sus observaciones para corregir los registros.";
                    $diarios = []; // Clear listing since they are processed
                } catch (\Throwable $e) {
                    mysqli_rollback($con);
                    $error = "Error al procesar el rechazo: " . $e->getMessage();
                }
            } else {
                $error = "Acción de firma inválida.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AulaPro | Firma de Diario FCT</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="public/css/dashboard.css" />
    <link rel="stylesheet" href="public/css/estilo.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .firma-caja {
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            max-width: 700px;
            width: 100%;
            border: 1px solid #e2e8f0;
        }
        .cabecera-logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .cabecera-logo h1 {
            color: #1e3a8a;
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .cabecera-logo p {
            color: #64748b;
            margin: 5px 0 0;
            font-size: 0.9rem;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }
        .meta-item strong {
            color: #0f172a;
        }
        .meta-item span {
            color: #64748b;
            display: block;
            margin-top: 2px;
        }
        .actividades-titulo {
            font-size: 1.1rem;
            color: #0f172a;
            margin: 0 0 12px;
            font-weight: 700;
        }
        .jornada-item {
            border-left: 3px solid #f97316;
            padding-left: 15px;
            margin-bottom: 16px;
            font-size: 0.92rem;
            line-height: 1.5;
        }
        .jornada-fecha {
            font-weight: 700;
            color: #0f172a;
        }
        .jornada-horas {
            background: #ffedd5;
            color: #ea580c;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 6px;
        }
        .jornada-desc {
            color: #334155;
            margin-top: 4px;
        }
        .observaciones-area {
            margin-top: 24px;
        }
        .acciones-firma {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        .btn-firma {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-aprobar {
            background: #22c55e;
            color: white;
        }
        .btn-aprobar:hover {
            background: #16a34a;
        }
        .btn-rechazar {
            background: #ef4444;
            color: white;
        }
        .btn-rechazar:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>

<div class="firma-caja">
    <div class="cabecera-logo">
        <h1>AulaPro Campus Suite</h1>
        <p>Firma de Diario de Prácticas FCT / Dual</p>
    </div>

        <div class="alerta alerta-exito" style="margin-bottom:0;">
            <i class="fas fa-check-circle fa-2x" style="margin-right:12px; vertical-align:middle;"></i>
            <span style="vertical-align:middle;"><?= Security::escapeHtml($exito) ?></span>
        </div>
        <div class="alerta alerta-error" style="margin-bottom:0;">
            <i class="fas fa-exclamation-circle fa-2x" style="margin-right:12px; vertical-align:middle;"></i>
            <span style="vertical-align:middle;"><?= Security::escapeHtml($error) ?></span>
        </div>
        <div class="meta-grid">
            <div class="meta-item">
                <span>Estudiante</span>
                <strong><?= Security::escapeHtml($nombreEstudiante) ?></strong>
            </div>
            <div class="meta-item">
                <span>Empresa</span>
                <strong><?= Security::escapeHtml($empresa) ?></strong>
            </div>
            <div class="meta-item">
                <span>Tutor de Empresa</span>
                <strong><?= Security::escapeHtml($tutorEmpresa ?? 'No indicado') ?></strong>
            </div>
            <div class="meta-item">
                <span>Total Horas a Firmar</span>
                <strong><?= (float)array_sum(array_column($diarios, 'horas')) ?> h</strong>
            </div>
        </div>

        <h3 class="actividades-titulo">Jornadas pendientes de firma</h3>
        <div style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                <div class="jornada-item">
                    <div>
                        <span class="jornada-fecha"><?= date('d/m/Y', strtotime($d['fecha'])) ?></span>
                        <span class="jornada-horas"><?= (float)$d['horas'] ?> horas</span>
                    </div>
                    <div class="jornada-desc"><?= nl2br(Security::escapeHtml($d['actividades'])) ?></div>
                </div>
        </div>

        <form method="POST" action="fct_firmar.php">
            <input type="hidden" name="token" value="<?= Security::escapeHtml($token) ?>">
            
            <div class="campo observaciones-area">
                <label for="obs">Observaciones o Comentarios para el estudiante (opcional)</label>
                <textarea name="observaciones" id="obs" rows="3" placeholder="Ej. Excelente trabajo hoy, o describe los cambios necesarios si rechazas el diario..."></textarea>
            </div>

            <div class="acciones-firma">
                <button type="submit" name="accion" value="rechazar" class="btn-firma btn-rechazar">
                    <i class="fas fa-times-circle"></i> RECHAZAR REGISTROS
                </button>
                <button type="submit" name="accion" value="aprobar" class="btn-firma btn-aprobar">
                    <i class="fas fa-check-circle"></i> APROBAR Y FIRMAR
                </button>
            </div>
        </form>
</div>

</body>
</html>
