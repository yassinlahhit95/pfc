    <?php renderStep($csrfToken); ?>
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/lib/helpers.php';
// ══════════════════════════════════════════════════════════════════════
// Asistente de instalación — punto de entrada único.
// Máquina de estados de 5 pasos vía $_SESSION['install_step']. Cada paso
// se auto-protege: no se puede saltar al paso 4 sin haber completado 2-3.
// Antes de renderizar NADA se comprueban los dos candados independientes
// (installIsLocked()) — ver install/lib/helpers.php.
// ══════════════════════════════════════════════════════════════════════
if (installIsLocked()) {
    header('Location: /vistas/login.php');
    exit;
}

Security::initSession();
if (!isset($_SESSION['install_step'])) $_SESSION['install_step'] = 1;

$errores = null;
$paso    = max(1, min(5, (int)$_SESSION['install_step']));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? null, false)) {
        $errores = 'Sesión de instalación expirada. Recarga la página e inténtalo de nuevo.';
    } else {
        $stepFile = __DIR__ . "/steps/{$paso}_" . ['', 'entorno', 'basedatos', 'admin', 'centro', 'funciones'][$paso] . '.php';
        // Cada fichero de paso define handlePost(): array{ok:bool, msg?:string}
        // y solo avanza $_SESSION['install_step'] si ok === true.
        require $stepFile;
        $resultado = handlePost();
        if ($resultado['ok']) {
            if ($paso < 5) {
                $_SESSION['install_step'] = $paso + 1;
                header('Location: /install/');
                exit;
            }
            // Paso 5 completado: handlePost() ya ha llamado a lockInstall()
            header('Location: /vistas/login.php');
            exit;
        }
        $errores = $resultado['msg'] ?? 'No se pudo completar este paso.';
    }
}

$stepFile = __DIR__ . "/steps/{$paso}_" . ['', 'entorno', 'basedatos', 'admin', 'centro', 'funciones'][$paso] . '.php';
require $stepFile;
$csrfToken = Security::generateCSRFToken();
$tituloPaso = ['', 'Entorno', 'Base de datos', 'Cuenta de administrador', 'Datos del centro', 'Funciones'][$paso];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Instalación — AulaPro</title>
<link rel="stylesheet" href="assets/install.css">
</head>
<body>
  <div class="install-card">
    <div class="install-brand">Aula<b>Pro</b></div>
    <div class="install-steps">
        <span class="install-step-dot<?= $i === $paso ? ' activo' : '' ?><?= $i < $paso ? ' hecho' : '' ?>"><?= $i ?></span>
    </div>
    <h1><?= htmlspecialchars($tituloPaso) ?></h1>

      <div class="install-alerta-error"><?= htmlspecialchars($errores) ?></div>
  </div>
</body>
</html>
