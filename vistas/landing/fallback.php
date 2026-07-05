<?php
// Página mínima cuando la landing no está publicada o el módulo está desactivado.
// Espera: $cfg (de index.php)
$logoUrl = '';
if (!empty($cfg['logoCentro'])) {
    $logoFichero = basename($cfg['logoCentro']);
    if (file_exists(__DIR__ . '/../../public/uploads/configuracion/' . $logoFichero)) {
        $logoUrl = '/public/uploads/configuracion/' . $logoFichero;
    }
}
$prematriculaOn = FeatureGuard::check('feature_prematricula');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Security::escapeHtml($cfg['nombreCentro']) ?></title>
<meta name="robots" content="noindex">
<link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'Plus Jakarta Sans',sans-serif; min-height:100vh; display:flex; align-items:center;
         justify-content:center; background:#f8fafc; color:#0f172a; padding:24px; }
  .tarjeta { max-width:480px; width:100%; background:#fff; border:1px solid #e2e8f0; border-radius:16px;
             padding:48px 40px; text-align:center; box-shadow:0 10px 30px rgba(15,23,42,.06); }
  .logo { max-height:72px; max-width:220px; object-fit:contain; margin-bottom:20px; }
  .icono { font-size:44px; color:#1d4ed8; margin-bottom:20px; }
  h1 { font-size:24px; margin-bottom:8px; }
  p  { color:#64748b; font-size:15px; line-height:1.6; margin-bottom:8px; }
  .datos { margin:20px 0 28px; }
  .datos a { color:#1d4ed8; text-decoration:none; }
  .botones { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
  .boton { display:inline-block; padding:12px 24px; border-radius:10px; font-weight:700; font-size:14px;
           text-decoration:none; }
  .boton-primario { background:#1d4ed8; color:#fff; }
  .boton-borde { border:1.5px solid #cbd5e1; color:#0f172a; }
</style>
</head>
<body>
  <div class="tarjeta">
    <?php if ($logoUrl): ?>
    <img class="logo" src="<?= Security::escapeHtml($logoUrl) ?>" alt="<?= Security::escapeHtml($cfg['nombreCentro']) ?>">
    <?php else: ?>
    <div class="icono"><i class="fas fa-graduation-cap"></i></div>
    <?php endif; ?>
    <h1><?= Security::escapeHtml($cfg['nombreCentro']) ?></h1>
    <div class="datos">
      <?php if (!empty($cfg['telefonoCentro'])): ?>
      <p><i class="fas fa-phone"></i> <a href="tel:<?= Security::escapeHtml(preg_replace('/\s+/', '', $cfg['telefonoCentro'])) ?>"><?= Security::escapeHtml($cfg['telefonoCentro']) ?></a></p>
      <?php endif; ?>
      <?php if (!empty($cfg['emailCentro'])): ?>
      <p><i class="fas fa-envelope"></i> <a href="mailto:<?= Security::escapeHtml($cfg['emailCentro']) ?>"><?= Security::escapeHtml($cfg['emailCentro']) ?></a></p>
      <?php endif; ?>
    </div>
    <div class="botones">
      <a class="boton boton-primario" href="/vistas/login.php">Acceso a la plataforma</a>
      <?php if ($prematriculaOn): ?>
      <a class="boton boton-borde" href="/vistas/admisiones/pre-matricula.php">Pre-matrícula</a>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
