<?php
require_once __DIR__ . "/../../config/Config.php";
require_once __DIR__ . "/../../include/Security.php";
Security::initSession();
require_once __DIR__ . "/../../controladores/comunes/email_helper.php";
require_once __DIR__ . "/../../include/Logger.php";

$config = Config::getInstance();
$expectedKey = $config->get('ADMIN_API_KEY');

// Bloqueo de seguridad: accesible con la API Key (?key=) o si el usuario tiene sesión activa de Administrador
$hasAccess = false;
if (!empty($expectedKey) && ($_GET['key'] ?? '') === $expectedKey) {
    $hasAccess = true;
}
if (isset($_SESSION['idAdmin'])) {
    $hasAccess = true;
}

if (!$hasAccess) {
    header('HTTP/1.1 403 Forbidden');
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Acceso Denegado</title><style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}div{text-align:center;padding:2rem;background:#1e293b;border-radius:12px;border:1px solid #334155;box-shadow:0 10px 15px -3px rgba(0,0,0,0.3);max-width:450px;line-height:1.6;}p{margin:10px 0;}</style></head><body><div><h2>Acceso Denegado</h2><p>La llave de API (?key=...) es incorrecta o no está configurada en el archivo <code>.env</code> de producción.</p><p style='font-size:0.9rem;color:#94a3b8;margin-top:1rem;'><strong>Tip para acceder:</strong> Inicia sesión como administrador en tu panel de Centro Educativo y vuelve a entrar a esta URL. Se te dará acceso automático sin necesidad de usar la llave.</p></div></body></html>";
    exit;
}


// 1. Estado de Base de Datos
require_once __DIR__ . "/../../modelos/conectar.php";
$dbStatus = "Desconectado";
$dbError = "";
$resetsCount = 0;
try {
    $con = obtenerConexion();
    if ($con) {
        $dbStatus = "Conectado exitosamente";
        $q = mysqli_query($con, "SELECT COUNT(*) as total FROM password_resets");
        if ($q) {
            $row = mysqli_fetch_assoc($q);
            $resetsCount = $row['total'];
        } else {
            $dbError = mysqli_error($con);
        }
    }
} catch (\Throwable $e) {
    $dbStatus = "Fallo de conexión";
    $dbError = $e->getMessage();
}

// 2. Estado de Brevo
$brevoKey = $config->get('BREVO_API_KEY');
$brevoKeyStatus = empty($brevoKey) ? "No configurada en .env" : "Configurada (" . substr($brevoKey, 0, 10) . "..." . substr($brevoKey, -6) . ")";

// 3. Estado de Firebase
$fbApiKey = $config->get('FIREBASE_API_KEY');
$fbProjectId = $config->get('FIREBASE_PROJECT_ID');
$fbVapidKey = $config->get('FIREBASE_VAPID_KEY');
$fbStatus = (empty($fbApiKey) || empty($fbProjectId) || empty($fbVapidKey)) ? "Configuración incompleta en .env" : "Configurado";

// 4. Test de Búsqueda de Usuario por Email
$searchEmailResult = "";
$searchedEmail = $_POST['search_email'] ?? '';

// CSRF protection for session-authenticated requests (API-key access skips this)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['idAdmin']) && !Security::validateCSRFToken()) {
    die('Solicitud inválida. Recarga la página e inténtalo de nuevo.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'search' && !empty($searchedEmail)) {
    require_once __DIR__ . "/../../modelos/password_reset.php";
    $searchEmail = trim($searchedEmail);
    $userFound = buscarUsuarioPorEmail($searchEmail);
    if ($userFound) {
        $roleName = strtoupper($userFound['tipo']);
        $row = $userFound['row'];
        $idField = $userFound['campoId'];
        $nameField = $userFound['tipo'] === 'admin' ? 'nombreDirector' : ($userFound['tipo'] === 'profesor' ? 'nombreProfesor' : ($userFound['tipo'] === 'estudiante' ? 'nombreEstudiante' : 'nombreTutor'));
        $userName = $row[$nameField] ?? 'N/A';
        $searchEmailResult = "<div class='alerta exito'>✓ Encontrado: <strong>Rol $roleName</strong> | ID: <strong>" . Security::escapeHtml($row[$idField]) . "</strong> | Nombre: <strong>" . Security::escapeHtml($userName) . "</strong></div>";
    } else {
        $searchEmailResult = "<div class='alerta error'>✗ Email '<strong>" . Security::escapeHtml($searchEmail) . "</strong>' no encontrado en ninguna tabla de usuarios (directores, profesores, estudiantes o tutores).</div>";
    }
}

// 5. Test de Envío de Email
$testResult = "";
$testEmailVal = $_POST['test_email'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'test_mail' && !empty($testEmailVal)) {
    $testEmail = filter_var($testEmailVal, FILTER_VALIDATE_EMAIL);
    if ($testEmail) {
        $sent = sendEmail($testEmail, "Test de Correo — Centro Educativo Diagnóstico", "<h3>Test de Conexión</h3><p>Si recibes este correo, significa que el servidor web se conecta correctamente a la API de Brevo y el remitente es aceptado.</p>");
        if ($sent) {
            $testResult = "<div class='alerta exito'>✓ ¡Email de prueba enviado exitosamente! Revisa tu bandeja de entrada o spam.</div>";
        } else {
            $lastErr = $_SESSION['ultimo_error_email'] ?? 'Error desconocido';
            unset($_SESSION['ultimo_error_email']);
            $testResult = "<div class='alerta error'>✗ Error al enviar el email: <strong>$lastErr</strong>. Consulta la sección de logs abajo.</div>";
        }
    } else {
        $testResult = "<div class='alerta advertencia'>✗ Dirección de email para prueba no válida.</div>";
    }
}

// 6. Leer logs de la aplicación
$errorLogs = Logger::getTail('error.log', 20);
$warningLogs = Logger::getTail('warning.log', 20);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Centro Educativo — Diagnóstico Integrado</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --surface-hover: #334155;
            --border: #334155;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #0ea5e9;
            --accent-hover: #0284c7;
            --green: #10b981;
            --red: #ef4444;
            --orange: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.5;
            padding: 2rem;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            font-size: 1.875rem;
            color: var(--text);
            font-weight: 700;
        }

        h1 span {
            color: var(--accent);
        }

        h2 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--text);
            border-left: 4px solid var(--accent);
            padding-left: 0.5rem;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            text-transform: uppercase;
        }

        .badge-green { background: rgba(16, 185, 129, 0.2); color: var(--green); border: 1px solid var(--green); }
        .badge-red { background: rgba(239, 68, 68, 0.2); color: var(--red); border: 1px solid var(--red); }
        .badge-blue { background: rgba(14, 165, 233, 0.2); color: var(--accent); border: 1px solid var(--accent); }

        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 992px) {
            .grid {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        .card {
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            text-align: right;
            word-break: break-all;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus {
            border-color: var(--accent);
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--accent);
            color: var(--text);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: var(--accent-hover);
        }

        .alerta {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            border: 1px solid transparent;
        }

        .alerta.exito { background: rgba(16, 185, 129, 0.15); color: #a7f3d0; border-color: rgba(16, 185, 129, 0.3); }
        .alerta.error { background: rgba(239, 68, 68, 0.15); color: #fca5a5; border-color: rgba(239, 68, 68, 0.3); }
        .alerta.advertencia { background: rgba(245, 158, 11, 0.15); color: #fde68a; border-color: rgba(245, 158, 11, 0.3); }

        .terminal {
            background-color: #030712;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem;
            max-height: 250px;
            overflow-y: auto;
            white-space: pre-wrap;
            color: #10b981;
        }

        .terminal.empty {
            color: var(--text-muted);
            font-style: italic;
        }

        .terminal.errors {
            color: #f87171;
        }

        .terminal.warnings {
            color: #fbbf24;
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div>
            <h1>Centro Educativo — <span>Centro de Diagnóstico Integrado</span></h1>
            <p style="color: var(--text-muted);">Herramienta de depuración de conexión, emails, base de datos y Firebase FCM</p>
        </div>
        <span class="badge badge-blue">Modo Diagnóstico</span>
    </header>

    <div class="grid">
        <!-- Columna 1: Estado General y Backend -->
        <div class="card">
            <h2>Estado del Servidor</h2>
            <div class="info-row">
                <span class="info-label">Entorno (APP_ENV)</span>
                <span class="info-value">
                    <span class="badge <?= $config->get('APP_ENV') === 'production' ? 'badge-green' : 'badge-blue' ?>">
                        <?= Security::escapeHtml($config->get('APP_ENV')) ?>
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Modo Debug (APP_DEBUG)</span>
                <span class="info-value">
                    <span class="badge <?= $config->getBoolean('APP_DEBUG') ? 'badge-red' : 'badge-green' ?>">
                        <?= $config->getBoolean('APP_DEBUG') ? 'Activo' : 'Desactivado' ?>
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">URL Base (APP_URL)</span>
                <span class="info-value"><?= Security::escapeHtml($config->get('APP_URL')) ?></span>
            </div>

            <h2 style="margin-top: 1.5rem;">Conexión a Base de Datos</h2>
            <div class="info-row">
                <span class="info-label">Estado</span>
                <span class="info-value">
                    <span class="badge <?= $con ? 'badge-green' : 'badge-red' ?>">
                        <?= $dbStatus ?>
                    </span>
                </span>
            </div>
            <?php if (!empty($dbError)): ?>
            <div class="info-row" style="color: var(--red);">
                <span class="info-label">Error DB</span>
                <span class="info-value"><?= Security::escapeHtml($dbError) ?></span>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label">Resets en BD</span>
                <span class="info-value"><?= (int)$resetsCount ?> registros</span>
            </div>

            <h2 style="margin-top: 1.5rem;">Configuraciones de APIs</h2>
            <div class="info-row">
                <span class="info-label">API Key Brevo</span>
                <span class="info-value"><?= Security::escapeHtml($brevoKeyStatus) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Firebase Status</span>
                <span class="info-value">
                    <span class="badge <?= $fbStatus === 'Configurado' ? 'badge-green' : 'badge-red' ?>">
                        <?= $fbStatus ?>
                    </span>
                </span>
            </div>
        </div>

        <!-- Columna 2: Test de Correo & Base de Datos -->
        <div class="card">
            <h2>Comprobación de Email en la BD</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.75rem;">
                Comprueba si la cuenta de correo existe en alguna de las tablas de la BD. Si no existe, no se le enviará ningún email por seguridad.
            </p>
            <form method="POST" style="margin-bottom: 1.5rem;">
                <input type="hidden" name="action" value="search">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <div class="form-group">
                    <input type="email" name="search_email" placeholder="ejemplo@correo.com" value="<?= Security::escapeHtml($searchedEmail) ?>" required>
                </div>
                <button type="submit">Buscar en DB</button>
            </form>
            <?= $searchEmailResult ?>

            <h2 style="margin-top: 1.5rem;">Prueba de Envío de Email</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.75rem;">
                Envía un correo mediante la API de Brevo para comprobar la autenticación y la IP del servidor.
            </p>
            <form method="POST">
                <input type="hidden" name="action" value="test_mail">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <div class="form-group">
                    <input type="email" name="test_email" placeholder="destinatario@correo.com" value="<?= Security::escapeHtml($testEmailVal) ?>" required>
                </div>
                <button type="submit" style="background-color: var(--green);">Enviar Email de Prueba</button>
            </form>
            <?= $testResult ?>
        </div>

        <!-- Columna 3: Test de Firebase FCM (Cliente / Navegador) -->
        <div class="card">
            <h2>Diagnóstico Firebase FCM (Navegador)</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">
                Esta prueba se ejecuta localmente en tu navegador. Comprueba permisos de notificaciones push, registro del Service Worker en raíz, autenticación de la llave VAPID y conexión a los servidores de Firebase.
            </p>
            <button id="btn-run-fb-test" style="background-color: var(--accent); margin-bottom: 1rem;">Iniciar Test Firebase FCM</button>
            
            <label>Consola de Diagnóstico Firebase</label>
            <div id="fb-terminal" class="terminal empty" style="height: 250px;">Haz clic en el botón de arriba para iniciar la prueba.</div>
        </div>
    </div>

    <!-- Sección de Logs -->
    <div class="card" style="margin-top: 1.5rem; width: 100%;">
        <h2>Visor de Logs de Servidor (Backend)</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
            Muestra las últimas líneas del archivo protegido de logs para identificar bloqueos de IP (401 en Brevo) o errores de Firebase del lado del servidor.
        </p>

        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
                <label>Logs de Errores (error.log)</label>
                <div class="terminal terminal-errors"><?php
                    if (empty($errorLogs)) {
                        echo "No se encontraron logs de errores recientes.";
                    } else {
                        foreach ($errorLogs as $line) {
                            echo Security::escapeHtml($line);
                        }
                    }
                ?></div>
            </div>

            <div>
                <label>Logs de Advertencia (warning.log)</label>
                <div class="terminal terminal-warnings"><?php
                    if (empty($warningLogs)) {
                        echo "No se encontraron advertencias recientes.";
                    } else {
                        foreach ($warningLogs as $line) {
                            echo Security::escapeHtml($line);
                        }
                    }
                ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Firebase Modular SDK para ejecutar el diagnóstico en el cliente -->
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
    import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging.js";

    document.getElementById('btn-run-fb-test').addEventListener('click', runFirebaseDiagnostic);

    async function runFirebaseDiagnostic() {
        const term = document.getElementById('fb-terminal');
        term.innerHTML = "";
        term.classList.remove('empty');
        
        function log(msg, type = 'info') {
            const div = document.createElement('div');
            div.style.padding = "2px 0";
            if (type === 'error') {
                div.style.color = '#f87171';
                div.innerHTML = `[ERROR] ${msg}`;
            } else if (type === 'success') {
                div.style.color = '#34d399';
                div.innerHTML = `[ÉXITO] ${msg}`;
            } else {
                div.style.color = '#94a3b8';
                div.innerHTML = `[INFO] ${msg}`;
            }
            term.appendChild(div);
            term.scrollTop = term.scrollHeight;
        }

        log("Iniciando diagnóstico de Firebase Cloud Messaging...");

        // 1. Verificar HTTPS
        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            log("Las notificaciones push de Firebase y los Service Workers requieren HTTPS en producción. Estás conectado vía HTTP.", "error");
            return;
        } else {
            log("Protocolo seguro verificado (" + location.protocol + ").", "success");
        }

        // 2. Soporte de APIs del Navegador
        if (!('Notification' in window)) {
            log("Este navegador no soporta la API de Notificaciones HTML5.", "error");
            return;
        }
        if (!('serviceWorker' in navigator)) {
            log("Este navegador no soporta Service Workers.", "error");
            return;
        }
        log("Soporte de Notificaciones y Service Workers del navegador verificado.", "success");

        // 3. Solicitar permisos de notificación
        log("Solicitando permiso de notificaciones al navegador...");
        try {
            const perm = await Notification.requestPermission();
            log(`Permiso de notificaciones del navegador: ${perm}`, perm === 'granted' ? 'success' : 'error');
            if (perm !== 'granted') {
                log("FALTA PERMISO: Por favor, concede permisos de notificación a esta página en la barra de direcciones del navegador e inténtalo de nuevo.", "error");
                return;
            }
        } catch (e) {
            log("Error al solicitar permisos: " + e.message, "error");
            return;
        }

        // 4. Cargar configuraciones del backend
        const apiKey = <?= Security::jsonEncodeSafe($config->get('FIREBASE_API_KEY')) ?>;
        const appId = <?= Security::jsonEncodeSafe($config->get('FIREBASE_APP_ID')) ?>;
        const projectId = <?= Security::jsonEncodeSafe($config->get('FIREBASE_PROJECT_ID')) ?>;
        const senderId = <?= Security::jsonEncodeSafe($config->get('FIREBASE_MESSAGING_SENDER_ID')) ?>;
        const authDomain = <?= Security::jsonEncodeSafe($config->get('FIREBASE_AUTH_DOMAIN')) ?>;
        const databaseURL = <?= Security::jsonEncodeSafe($config->get('FIREBASE_DATABASE_URL')) ?>;
        const vapidKey = <?= Security::jsonEncodeSafe($config->get('FIREBASE_VAPID_KEY')) ?>;

        if (!apiKey || !appId || !projectId || !senderId || !vapidKey) {
            log("Faltan variables de Firebase en tu archivo `.env`. Verifica que todas estén definidas.", "error");
            return;
        }
        log("Configuraciones de Firebase cargadas correctamente desde `.env`.", "success");

        // 5. Inicializar Firebase App SDK
        let app, messaging;
        try {
            log("Cargando e inicializando Firebase App SDK...");
            app = initializeApp({
                apiKey: apiKey,
                authDomain: authDomain,
                projectId: projectId,
                storageBucket: `${projectId}.firebasestorage.app`,
                messagingSenderId: senderId,
                appId: appId,
                databaseURL: databaseURL
            });
            log("Firebase App inicializada con éxito.", "success");
        } catch (e) {
            log("Error al inicializar Firebase App: " + e.message, "error");
            return;
        }

        // 6. Obtener instancia de Firebase Messaging
        try {
            log("Obteniendo instancia de Firebase Messaging...");
            messaging = getMessaging(app);
            log("Instancia de Firebase Messaging obtenida con éxito.", "success");
        } catch (e) {
            log("Error al obtener Firebase Messaging (verificar bloqueos de red o políticas CSP): " + e.message, "error");
            return;
        }

        // 7. Registrar Service Worker en raíz
        log("Buscando registros existentes de Service Workers...");
        let swReg;
        try {
            const regs = await navigator.serviceWorker.getRegistrations();
            swReg = regs.find(reg => {
                const scriptUrl = (reg.active || reg.installing || reg.waiting || {}).scriptURL || '';
                return scriptUrl.includes('firebase-messaging-sw.js');
            });

            if (swReg) {
                log("Service Worker `firebase-messaging-sw.js` ya registrado. Reutilizando registro.", "info");
            } else {
                // Obtener ruta base
                const relativePath = location.pathname.substring(0, location.pathname.lastIndexOf('/'));
                const appPath = relativePath.split('/vistas/')[0] || '';
                const swUrl = appPath + '/firebase-messaging-sw.js';
                log(`Registrando Service Worker en: ${swUrl}...`, "info");
                
                swReg = await navigator.serviceWorker.register(swUrl);
                log("Service Worker registrado con éxito.", "success");
            }

            // Esperar activación si está en progreso
            log("Comprobando estado de activación del Service Worker...");
            if (swReg.installing || swReg.waiting) {
                log("El Service Worker está instalándose o en espera. Aguardando activación...", "info");
                await new Promise((resolve, reject) => {
                    const target = swReg.installing || swReg.waiting;
                    target.addEventListener('statechange', function onStateChange() {
                        if (this.state === 'activated') {
                            target.removeEventListener('statechange', onStateChange);
                            resolve();
                        } else if (this.state === 'redundant') {
                            target.removeEventListener('statechange', onStateChange);
                            reject(new Error('El Service Worker se volvió redundante'));
                        }
                    });
                });
            }
            if (!swReg.active) {
                throw new Error("El Service Worker no está en estado activo.");
            }
            log("Service Worker activo y listo.", "success");
        } catch (e) {
            log("Error al registrar/activar el Service Worker: " + e.message, "error");
            log("CONSEJO: Asegúrate de que el archivo `firebase-messaging-sw.js` esté físicamente en el directorio raíz de la aplicación web en producción.", "info");
            return;
        }

        // 8. Recuperar Token FCM
        log("Solicitando Token Push FCM con llave VAPID...");
        try {
            const token = await getToken(messaging, {
                vapidKey: vapidKey,
                serviceWorkerRegistration: swReg
            });
            if (token) {
                log("¡Token de Notificaciones Push FCM obtenido con éxito!", "success");
                log(`FCM TOKEN: <span style='color: #0ea5e9; word-break: break-all;'>${token}</span>`, "info");
                log("El navegador está preparado. El backend podrá enviar notificaciones a este navegador.", "success");
            } else {
                log("La petición de Token no arrojó ningún error, pero devolvió un valor vacío. Comprueba los permisos.", "error");
            }
        } catch (e) {
            log("Error al obtener token FCM: " + e.message, "error");
            if (e.message.includes("permission-denied")) {
                log("CONSEJO: Ve a la configuración de seguridad del navegador para esta página, elimina la denegación de notificaciones push, recarga e inténtalo de nuevo.", "info");
            } else if (e.message.includes("bad-sender-identity") || e.message.includes("sender-id-mismatch")) {
                log("CONSEJO CRÍTICO: La clave `FIREBASE_VAPID_KEY` o el `FIREBASE_MESSAGING_SENDER_ID` en el archivo `.env` son incorrectos o pertenecen a proyectos diferentes de Firebase. Compáralos en la consola de Firebase.", "error");
            } else if (e.name === 'AbortError') {
                log("Suscripción previa inválida o obsoleta. Intentando limpiar suscripción y reintentar...", "info");
                try {
                    const sub = await swReg.pushManager.getSubscription();
                    if (sub) {
                        await sub.unsubscribe();
                        log("Suscripción antigua revocada. Reintentando...", "info");
                        const tokenRetry = await getToken(messaging, {
                            vapidKey: vapidKey,
                            serviceWorkerRegistration: swReg
                        });
                        if (tokenRetry) {
                            log("¡Token obtenido tras reintento!", "success");
                            log(`FCM TOKEN: ${tokenRetry}`, "success");
                            return;
                        }
                    }
                } catch (retryErr) {
                    log("Fallo al reintentar: " + retryErr.message, "error");
                }
            }
        }
    }
</script>

</body>
</html>
