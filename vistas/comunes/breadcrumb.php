<?php
/**
 * Breadcrumb global — incluido por cada vistas/{rol}/comunes/nav.php justo
 * dentro de <div class="content">.
 *
 * Lee automáticamente $seccion (admin/secretaria/tutores) o $seccionActual
 * (profesores/estudiantes) — las dos convenciones ya existentes en el
 * proyecto — y resuelve una etiqueta legible desde el mapa de abajo.
 *
 * Para un tramo final dinámico (nombre de alumno, módulo, carpeta...) la
 * vista puede definir, ANTES de requerir nav.php:
 *
 *   $breadcrumbExtra = [
 *       ['label' => 'Módulo X', 'url' => 'modulo.php?id=5'],
 *       ['label' => 'Carpeta actual', 'url' => null], // null = página actual, no es enlace
 *   ];
 */

$__bcSeccion = $seccion ?? $seccionActual ?? 'inicio';

$__bcLabels = [
    'inicio'                  => 'Inicio',
    'estudiantes'             => 'Estudiantes',
    'ciclos'                  => 'Ciclos',
    'modulos'                 => 'Módulos',
    'fp_dual'                 => 'FP Dual',
    'retos'                   => 'Retos',
    'notas_modulos'           => 'Notas de módulos',
    'notas_retos'             => 'Notas de retos',
    'notas_tfg'               => 'Notas de TFG',
    'resultados_modulos'      => 'Resultados finales',
    'resultados_finales'      => 'Resultados finales',
    'fct'                     => 'FCT',
    'configuracion_academica' => 'Configuración académica',
    'horario'                 => 'Horario',
    'asistencias'             => 'Asistencias',
    'justificaciones'         => 'Justificaciones',
    'admisiones'              => 'Admisiones',
    'directores'              => 'Dirección',
    'profesores'              => 'Profesores',
    'tutores'                 => 'Tutores',
    'secretarias'             => 'Secretaría',
    'anuncios'                => 'Anuncios',
    'reclamaciones'           => 'Mensajes',
    'mensajes'                => 'Mensajes',
    'chat'                    => 'Chat',
    'eventos'                 => 'Eventos',
    'pagos'                   => 'Pagos',
    'gastos'                  => 'Gastos',
    'inventario'              => 'Inventario',
    'prestamos'               => 'Préstamos',
    'aulas'                   => 'Aulas',
    'informes'                => 'Informes',
    'landing'                 => 'Landing',
    'blog'                    => 'Blog',
    'ofertaCiclos'            => 'Oferta de ciclos',
    'configuracion'           => 'Configuración',
    'rgpd'                    => 'RGPD',
    'saas_estado'             => 'Estado SaaS',
    'calificaciones'          => 'Calificaciones',
    'aula_sesiones'           => 'Aula',
    'aula_recursos'           => 'Recursos',
    'aula_tareas'             => 'Tareas',
    'aula_favoritos'          => 'Favoritos',
    'aula_entregas'           => 'Entregas',
    'perfil'                  => 'Mi perfil',
    'tfg'                     => 'TFG',
    'papelera'                => 'Papelera',
];

$__bcSeccionLabel = $__bcLabels[$__bcSeccion] ?? ucfirst(str_replace('_', ' ', $__bcSeccion));
$__bcExtra = $breadcrumbExtra ?? [];
?>
<nav class="breadcrumb-bar" aria-label="Ruta de navegación">
  <a href="../inicio/dashboard.php" class="breadcrumb-item breadcrumb-home">
    <i class="fas fa-house" aria-hidden="true"></i>
    <span>Inicio</span>
  </a>
  <?php if ($__bcSeccion !== 'inicio'): ?>
    <i class="fas fa-chevron-right breadcrumb-sep" aria-hidden="true"></i>
    <?php if (empty($__bcExtra)): ?>
      <span class="breadcrumb-item breadcrumb-current" aria-current="page"><?= Security::escapeHtml($__bcSeccionLabel) ?></span>
    <?php elseif (!empty($breadcrumbSectionUrl)): ?>
      <a href="<?= Security::escapeHtml($breadcrumbSectionUrl) ?>" class="breadcrumb-item"><?= Security::escapeHtml($__bcSeccionLabel) ?></a>
    <?php else: ?>
      <button type="button" class="breadcrumb-item breadcrumb-back" data-breadcrumb-back="../inicio/dashboard.php"><?= Security::escapeHtml($__bcSeccionLabel) ?></button>
    <?php endif; ?>
    <?php if (!empty($__bcExtra)): foreach ($__bcExtra as $__bcCrumb):
        $__bcIsLast = $__bcCrumb === end($__bcExtra); ?>
      <i class="fas fa-chevron-right breadcrumb-sep" aria-hidden="true"></i>
      <?php if (!empty($__bcCrumb['url']) && !$__bcIsLast): ?>
        <a href="<?= Security::escapeHtml($__bcCrumb['url']) ?>" class="breadcrumb-item"><?= Security::escapeHtml($__bcCrumb['label']) ?></a>
      <?php else: ?>
        <span class="breadcrumb-item breadcrumb-current" aria-current="page"><?= Security::escapeHtml($__bcCrumb['label']) ?></span>
      <?php endif; ?>
    <?php endforeach; endif; ?>
  <?php endif; ?>
</nav>
