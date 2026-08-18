<?php
require_once __DIR__ . '/../modelos/calificaciones.php';
require_once __DIR__ . '/../modelos/configuracion.php';
require_once __DIR__ . '/../include/R2Client.php';

$_ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
    ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]
    : ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
$_ip = trim($_ip);

// Rate limit: max 30 checks per IP per hour
if (contarIntentosVerificacion($_ip, 60) >= 30) {
    http_response_code(429);
    exit('Demasiadas solicitudes. Espera unos minutos e inténtalo de nuevo.');
}

$serial  = strtoupper(trim($_GET['s'] ?? ''));
// Only accept the expected format: BLT-YYYY-XXXX-XXXX-XXXX[-XXXX]
if ($serial && !preg_match('/^BLT-\d{4}(-[A-F0-9]{4}){3,4}$/', $serial)) {
    $serial = '';
}

$cfg     = obtenerConfiguracionCentro();
$doc     = $serial ? verificarBoletinPorSerial($serial, $_ip) : null;
$valido  = $doc !== null;
$logoUrl = '';
if (!empty($cfg['logoCentro'])) {
    $logoFichero = basename($cfg['logoCentro']);
    $logoUrl = R2Client::imagenUrl(
        __DIR__ . '/../public/uploads/configuracion/' . $logoFichero,
        '../public/uploads/configuracion/' . $logoFichero,
        'configuracion/' . $logoFichero
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Verificación de Documento — <?= htmlspecialchars($cfg['nombreCentro']) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }
        .card-header {
            padding: 20px 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .card-header img { height: 40px; }
        .card-header .centro { font-size: .85rem; color: #64748b; }
        .card-header .nombre { font-size: 1rem; font-weight: 700; color: #1e293b; }

        .badge {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 28px;
            border-bottom: 1px solid #e2e8f0;
        }
        .badge-icon {
            width: 56px; height: 56px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }
        .badge-icon.ok  { background: #dcfce7; color: #16a34a; }
        .badge-icon.err { background: #fee2e2; color: #dc2626; }
        .badge-title { font-size: 1.15rem; font-weight: 700; }
        .badge-title.ok  { color: #15803d; }
        .badge-title.err { color: #b91c1c; }
        .badge-sub { font-size: .85rem; color: #64748b; margin-top: 3px; }

        .details { padding: 24px 28px; display: flex; flex-direction: column; gap: 14px; }
        .detail-row { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
        .detail-label { font-size: .78rem; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; font-weight: 600; }
        .detail-value { font-size: .92rem; font-weight: 600; color: #1e293b; text-align: right; }

        .serial-box {
            margin: 0 28px 24px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            font-family: monospace;
            font-size: .85rem;
            color: #334155;
            letter-spacing: .5px;
            text-align: center;
        }
        .footer-note {
            padding: 14px 28px;
            background: #f8fafc;
            font-size: .75rem;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .not-found-body { padding: 28px; text-align: center; color: #64748b; font-size: .9rem; line-height: 1.6; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <?php if ($logoUrl): ?>
        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="">
        <?php endif; ?>
        <div>
            <div class="nombre"><?= htmlspecialchars($cfg['nombreCentro']) ?></div>
            <div class="centro">Verificación de documento oficial</div>
        </div>
    </div>

    <?php if (!$serial): ?>
        <div class="not-found-body">
            No se ha proporcionado ningún número de serie.<br>
            Escanea el código QR del boletín para verificarlo.
        </div>

    <?php elseif ($valido): ?>
        <div class="badge">
            <div class="badge-icon ok">✓</div>
            <div>
                <div class="badge-title ok">Documento Auténtico</div>
                <div class="badge-sub">Este boletín ha sido emitido oficialmente por el centro</div>
            </div>
        </div>
        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Alumno/a</span>
                <span class="detail-value"><?= htmlspecialchars(mb_strtoupper($doc['nombreEstudiante'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Especialidad</span>
                <span class="detail-value"><?= htmlspecialchars($doc['nombreCiclo']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Curso Escolar</span>
                <span class="detail-value"><?= htmlspecialchars($doc['cursoEscolar']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Fecha de emisión</span>
                <span class="detail-value"><?= date('d/m/Y H:i', strtotime($doc['fechaGeneracion'])) ?></span>
            </div>
        </div>
        <div class="serial-box"><?= htmlspecialchars($doc['serial']) ?></div>

    <?php else: ?>
        <div class="badge">
            <div class="badge-icon err">✗</div>
            <div>
                <div class="badge-title err">Documento No Encontrado</div>
                <div class="badge-sub">Este código no corresponde a ningún boletín emitido por el centro</div>
            </div>
        </div>
        <div class="not-found-body">
            El número de serie <strong><?= htmlspecialchars($serial) ?></strong>
            no está registrado en nuestro sistema.<br><br>
            Si crees que es un error, contacta con el centro directamente.
        </div>
    <?php endif; ?>

    <div class="footer-note">
        <?= htmlspecialchars($cfg['nombreCentro']) ?> &mdash; Sistema <?= htmlspecialchars($cfg["nombreCentro"] ?? "") ?>
    </div>
</div>
</body>
</html>
