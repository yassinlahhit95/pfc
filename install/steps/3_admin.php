<?php
// Paso 3 — cuenta del primer administrador (tabla `directores`).
// A partir de aquí el .env ya existe (escrito en el paso 2), así que se
// puede usar la conexión/helpers normales de la aplicación.
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/BotGuard.php';

function handlePost(): array {
    if (!BotGuard::validate()) {
        return ['ok' => false, 'msg' => 'No se pudo verificar la solicitud. Inténtalo de nuevo.'];
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $pass1  = (string)($_POST['password'] ?? '');
    $pass2  = (string)($_POST['password_confirm'] ?? '');

    if ($nombre === '' || $email === '') {
        return ['ok' => false, 'msg' => 'Nombre y email son obligatorios.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'msg' => 'El email no es válido.'];
    }
    if (strlen($pass1) < 8) {
        return ['ok' => false, 'msg' => 'La contraseña debe tener al menos 8 caracteres.'];
    }
    if ($pass1 !== $pass2) {
        return ['ok' => false, 'msg' => 'Las contraseñas no coinciden.'];
    }

    $con = obtenerConexion();

    // installIsLocked() ya protege el asistente entero, pero esta comprobación
    // es la última línea de defensa contra una doble ejecución concurrente
    // de este paso concreto (dos pestañas, doble clic con red lenta...).
    $existe = mysqli_query($con, "SELECT 1 FROM directores LIMIT 1");
    if ($existe && mysqli_num_rows($existe) > 0) {
        return ['ok' => false, 'msg' => 'Ya existe una cuenta de administrador. Continúa desde el inicio de sesión.'];
    }

    $hash = Security::hashPassword($pass1);
    $stmt = mysqli_prepare($con,
        "INSERT INTO directores (nombreDirector, emailDirector, password, dniDirector, fechaAltaDirector)
         VALUES (?, ?, ?, '', CURDATE())");
    mysqli_stmt_bind_param($stmt, 'sss', $nombre, $email, $hash);

    if (!mysqli_stmt_execute($stmt)) {
        return ['ok' => false, 'msg' => 'No se pudo crear la cuenta: ' . mysqli_error($con)];
    }

    return ['ok' => true];
}

function renderStep(string $csrfToken): void {
    ?>
    <p class="install-intro">Esta será la primera cuenta de administrador del centro. Podrás añadir el resto de datos del perfil (DNI, teléfono...) después, desde el panel.</p>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
      <?= BotGuard::renderFields() ?>
      <label>Nombre completo
        <input type="text" name="nombre" required autofocus>
      </label>
      <label>Email
        <input type="email" name="email" required autocomplete="username">
      </label>
      <label>Contraseña <small>(mín. 8 caracteres)</small>
        <input type="password" name="password" minlength="8" required autocomplete="new-password">
      </label>
      <label>Confirmar contraseña
        <input type="password" name="password_confirm" minlength="8" required autocomplete="new-password">
      </label>
      <button type="submit" class="install-btn">Crear cuenta de administrador</button>
    </form>
    <?php
}
