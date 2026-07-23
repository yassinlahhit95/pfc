<?php
// Paso 4 — identidad del centro (tabla `configuracion_centro`, fila
// idConfig=1 que ya existe tras la importación del esquema — se actualiza,
// no se inserta).
require_once __DIR__ . '/../../modelos/conectar.php';

function handlePost(): array {
    $nombre  = trim($_POST['nombreCentro'] ?? '');
    $ciudad  = trim($_POST['ciudadCentro'] ?? '');
    $curso   = trim($_POST['cursoEscolar'] ?? '');
    $email   = trim($_POST['emailCentro'] ?? '');

    if ($nombre === '') {
        return ['ok' => false, 'msg' => 'El nombre del centro es obligatorio.'];
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'msg' => 'El email del centro no es válido.'];
    }

    $con  = obtenerConexion();
    // INSERT ... ON DUPLICATE KEY UPDATE en vez de un UPDATE plano: la fila
    // idConfig=1 no se crea sola solo con importar el esquema (comprobado:
    // una importación fresca de database.sql deja configuracion_centro
    // vacía), así que un UPDATE aquí actualizaría cero filas en una
    // instalación nueva. Esto cubre los dos casos (fila ya existe / no existe).
    $stmt = mysqli_prepare($con,
        "INSERT INTO configuracion_centro (idConfig, nombreCentro, ciudadCentro, cursoEscolar, emailCentro)
         VALUES (1, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
           nombreCentro = VALUES(nombreCentro), ciudadCentro = VALUES(ciudadCentro),
           cursoEscolar = VALUES(cursoEscolar), emailCentro = VALUES(emailCentro)");
    mysqli_stmt_bind_param($stmt, 'ssss', $nombre, $ciudad, $curso, $email);

    if (!mysqli_stmt_execute($stmt)) {
        return ['ok' => false, 'msg' => 'No se pudo guardar: ' . mysqli_error($con)];
    }

    return ['ok' => true];
}

function renderStep(string $csrfToken): void {
    $cursoActual = (int)date('Y') . '-' . ((int)date('Y') + 1);
    ?>
    <p class="install-intro">Datos básicos del centro — se pueden editar en cualquier momento desde Configuración.</p>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
      <label>Nombre del centro
        <input type="text" name="nombreCentro" required autofocus>
      </label>
      <label>Ciudad
        <input type="text" name="ciudadCentro">
      </label>
      <label>Curso escolar
        <input type="text" name="cursoEscolar" value="<?= htmlspecialchars($cursoActual) ?>">
      </label>
      <label>Email de contacto del centro
        <input type="email" name="emailCentro">
      </label>
      <button type="submit" class="install-btn">Continuar</button>
    </form>
    <?php
}
