<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// ob_start captura cualquier warning/notice inesperado de PHP para que no
// contamine el cuerpo de la respuesta JSON (se llama a ob_clean() antes de cada echo).
ob_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';
require_once __DIR__ . '/../../../modelos/log.php';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
// Bloquear si la plataforma SaaS ha bloqueado el control de funcionalidades
if (FeatureGuard::isLocked()) {
    ob_clean();
    echo json_encode([
        'ok'       => false,
        'msg'      => 'Las funcionalidades están bloqueadas por la plataforma SaaS. Contacta con el proveedor para modificarlas.',
        'new_csrf' => Security::generateCSRFToken(),
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken($_POST['csrf_token'] ?? '', false)) {
    ob_clean();
    echo json_encode([
        'ok'       => false,
        'msg'      => 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.',
        'new_csrf' => Security::generateCSRFToken(),
    ]);
    exit;
}

$feature = $_POST['feature'] ?? '';
$estado  = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

$etiquetas = [
    'feature_prematricula' => 'Pre-matrícula',
    'feature_chat'         => 'Sistema de Chat',
    'feature_inventario'   => 'Inventario',
    'feature_subida_tfg'   => 'Entrega de TFG',
    'feature_anuncios'     => 'Anuncios',
    'feature_eventos'      => 'Eventos',
    'feature_retos'        => 'Retos',
    'feature_mensajes'     => 'Mensajería',
    'feature_pagos'        => 'Pagos',
    'feature_gastos'       => 'Gastos',
    'feature_informes'     => 'Informes PDF',
    'feature_horario'      => 'Cuadro Horario',
    'feature_landing'      => 'Página Web Pública',
    'feature_geoblock_admin' => 'Geo-Block (España)',
    'feature_ra_ce'        => 'Eval. LOMLOE (RA/CE)',
    'feature_fp_dual'      => 'FP Dual / Empresas',
    'feature_fct'          => 'FCT',
    'feature_modulos'      => 'Módulos Académicos',
    'feature_academico_config' => 'Motor de Calificaciones Configurable',
    'prematricula_filtrar_niveles' => 'Filtrado de niveles en Pre-Matrícula',
];
$etiqueta = $etiquetas[$feature] ?? $feature;
$accion   = $estado === 1 ? 'activado' : 'desactivado';

$resultado = actualizarFeatureToggle($feature, $estado);
ob_clean();
if ($resultado === true) {
    registrarAccion('toggle_feature', 'configuracion', null, "$feature=$estado");
    FeatureGuard::clearCache();
    echo json_encode([
        'ok'       => true,
        'msg'      => "Módulo «{$etiqueta}» {$accion} correctamente.",
        'new_csrf' => Security::generateCSRFToken(),
    ]);
} else {
    $detalle = is_string($resultado) ? $resultado : 'Error desconocido al actualizar la configuración.';
    echo json_encode([
        'ok'       => false,
        'msg'      => "No se pudo {$accion} «{$etiqueta}». {$detalle}",
        'new_csrf' => Security::generateCSRFToken(),
    ]);
}
