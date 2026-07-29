<?php
// Paso 2 — conexión a la base de datos, importación del esquema completo
// (noDeploy/database.sql) y escritura del .env. Es el único paso que toca
// el sistema de ficheros/BD de verdad antes de la cuenta de admin.

function handlePost(): array {
    $host = trim($_POST['db_host'] ?? '');
    $user = trim($_POST['db_user'] ?? '');
    $pass = (string)($_POST['db_pass'] ?? '');
    $name = trim($_POST['db_name'] ?? '');
    $appUrl = trim($_POST['app_url'] ?? '');

    if ($host === '' || $user === '' || $name === '') {
        return ['ok' => false, 'msg' => 'Host, usuario y nombre de base de datos son obligatorios.'];
    }

    // Validate host (alphanumeric, dots, hyphens, max 255 chars)
    if (strlen($host) > 255 || !preg_match('/^[a-zA-Z0-9.\-]+$/', $host)) {
        return ['ok' => false, 'msg' => 'El host contiene caracteres inválidos.'];
    }

    // Validate database username (alphanumeric, underscores, max 32 chars for MySQL)
    if (strlen($user) > 32 || !preg_match('/^[a-zA-Z0-9_]+$/', $user)) {
        return ['ok' => false, 'msg' => 'El usuario debe contener solo letras, números y guiones bajos.'];
    }

    // Validate app_url if provided (basic URL validation)
    if ($appUrl !== '' && strlen($appUrl) > 500) {
        return ['ok' => false, 'msg' => 'La URL del centro es demasiado larga.'];
    }

    $test = testDbConnection($host, $user, $pass, $name);
    if (!$test['ok']) return ['ok' => false, 'msg' => $test['msg']];

    $import = runSchemaImport($host, $user, $pass, $name);
    if (!$import['ok']) return ['ok' => false, 'msg' => $import['msg']];

    $env = writeEnvFile([
        'host' => $host, 'user' => $user, 'pass' => $pass, 'name' => $name,
        'app_url' => $appUrl, 'app_env' => 'production',
    ]);
    if (!$env['ok']) {
        // If .env write fails after successful import, don't leave system in
        // inconsistent state. On retry, user will see the import again but
        // database.sql's DROP IF EXISTS will make it safe to re-import.
        return ['ok' => false, 'msg' => $env['msg']];
    }

    // CORS update is best-effort and should not fail the installation
    updateCorsOrigin($appUrl);

    return ['ok' => true];
}

function renderStep(string $csrfToken): void {
    ?>
    <p class="install-intro">Introduce los datos de conexión de una base de datos MySQL/MariaDB vacía (o ya existente — se puede reimportar sin problema). Se creará si no existe.</p>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
      <label>Host de la base de datos
        <input type="text" name="db_host" value="localhost" required>
      </label>
      <label>Usuario
        <input type="text" name="db_user" required>
      </label>
      <label>Contraseña
        <input type="password" name="db_pass" autocomplete="new-password">
      </label>
      <label>Nombre de la base de datos
        <input type="text" name="db_name" required>
      </label>
      <label>URL pública del centro (opcional, se puede rellenar luego)
        <input type="text" name="app_url" placeholder="https://tudominio.com">
      </label>
      <button type="submit" class="install-btn">Conectar e importar esquema</button>
      <p class="install-nota">La importación del esquema completo puede tardar unos segundos — no cierres esta página.</p>
    </form>
    <?php
}
