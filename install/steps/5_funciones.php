<?php
// Paso 5 — selección inicial de funciones (columnas feature_* de
// `configuracion_centro`). Mismas funciones/etiquetas que
// vistas/admin/configuracion/configuracion.php, para que lo que el asistente
// muestra coincida exactamente con lo que se ve luego en el panel. Todas
// activadas por defecto — es más fácil desactivar algo que no se usa que
// descubrir que algo necesario está apagado.
require_once __DIR__ . '/../../modelos/conectar.php';

const INSTALL_FEATURES = [
    'feature_prematricula' => 'Pre-matrícula — portal de admisión pública',
    'feature_chat'          => 'Chat — mensajería instantánea',
    'feature_inventario'    => 'Inventario — recursos y préstamos',
    'feature_subida_tfg'    => 'Entrega de TFG',
    'feature_anuncios'      => 'Anuncios — tablón de avisos del centro',
    'feature_eventos'       => 'Eventos — calendario de actividades',
    'feature_retos'         => 'Retos — actividades académicas gamificadas',
    'feature_mensajes'      => 'Mensajería — reclamaciones y mensajes internos',
    'feature_pagos'         => 'Pagos — gestión de pagos y matrículas',
    'feature_gastos'        => 'Gastos — control de gastos del centro',
    'feature_informes'      => 'Informes PDF — boletines, listados y horarios',
    'feature_horario'       => 'Cuadro horario',
    'feature_fp_dual'       => 'FP Dual / Empresas',
    'feature_fct'           => 'FCT — Formación en Centros de Trabajo',
    'feature_landing'       => 'Página web pública del centro',
];

function handlePost(): array {
    $con = obtenerConexion();

    $sets = [];
    $vals = [];
    $types = '';
    foreach (INSTALL_FEATURES as $key => $label) {
        $sets[] = "`$key` = ?";
        $vals[] = !empty($_POST[$key]) ? 1 : 0;
        $types .= 'i';
    }
    $sql = "UPDATE configuracion_centro SET " . implode(', ', $sets) . " WHERE idConfig = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$vals);

    if (!mysqli_stmt_execute($stmt)) {
        return ['ok' => false, 'msg' => 'No se pudo guardar: ' . mysqli_error($con)];
    }

    lockInstall();
    $_SESSION['exito'] = 'Instalación completada. Inicia sesión con la cuenta de administrador que acabas de crear.';
    return ['ok' => true];
}

function renderStep(string $csrfToken): void {
    ?>
    <p class="install-intro">Activa las funciones que va a usar el centro — se pueden cambiar en cualquier momento desde Configuración.</p>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
      <ul class="install-checklist install-features">
        <?php foreach (INSTALL_FEATURES as $key => $label): ?>
          <li>
            <label class="install-feature-toggle">
              <input type="checkbox" name="<?= htmlspecialchars($key) ?>" checked>
              <span><?= htmlspecialchars($label) ?></span>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>
      <button type="submit" class="install-btn">Finalizar instalación</button>
    </form>
    <?php
}
