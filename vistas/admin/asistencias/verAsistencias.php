<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idCiclo      = (int)($_GET['idCiclo']    ?? 0) ?: null;
$idModulo     = (int)($_GET['idModulo']   ?? 0) ?: null;
$idEstudiante = (int)($_GET['idEstudiante'] ?? 0) ?: null;
$fechaDesde   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fechaDesde'] ?? '') ? $_GET['fechaDesde'] : '';
$fechaHasta   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fechaHasta'] ?? '') ? $_GET['fechaHasta'] : '';

$ciclos   = listarTodosLosCiclos();
$modulos  = $idCiclo ? listarModulosPorCiclo($idCiclo) : [];

$asistencias = listarAsistenciasFiltradas(
    $idCiclo, $idModulo, $idEstudiante,
    $fechaDesde ?: null,
    $fechaHasta ?: null
);

$titulo_pagina = "AulaPro | Asistencias";
$seccion       = "asistencias";
require_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-clipboard-check"></i> Registro de Asistencias</h1>
  </div>
  <a href="/controladores/admin/asistencias/exportarCSV.php?<?= http_build_query(array_filter([
    'idCiclo' => $idCiclo, 'idModulo' => $idModulo, 'idEstudiante' => $idEstudiante,
    'fechaDesde' => $fechaDesde, 'fechaHasta' => $fechaHasta
  ])) ?>" class="boton-secundario">
    <i class="fas fa-download"></i> Exportar CSV
  </a>
</div>

<div class="panel margen-abajo">
  <form method="GET" action="" class="formulario" id="form-filtros-asistencias">
    <div class="campo">
      <label>Ciclo</label>
      <select name="idCiclo" id="sel-ciclo-asist" onchange="this.form.submit()">
        <option value="">Todos los ciclos</option>
        <?php foreach ($ciclos as $c): ?>
        <option value="<?= (int)$c['idCiclo'] ?>" <?= $idCiclo === (int)$c['idCiclo'] ? 'selected' : '' ?>>
          <?= Security::escapeHtml($c['nombreCiclo']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php if ($idCiclo && $modulos): ?>
    <div class="campo">
      <label>Módulo</label>
      <select name="idModulo" onchange="this.form.submit()">
        <option value="">Todos los módulos</option>
        <?php foreach ($modulos as $m): ?>
        <option value="<?= (int)$m['idModulo'] ?>" <?= $idModulo === (int)$m['idModulo'] ? 'selected' : '' ?>>
          <?= Security::escapeHtml($m['nombreModulo']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>
    <div class="campo">
      <label>Desde</label>
      <input type="date" name="fechaDesde" value="<?= Security::escapeHtml($fechaDesde) ?>">
    </div>
    <div class="campo">
      <label>Hasta</label>
      <input type="date" name="fechaHasta" value="<?= Security::escapeHtml($fechaHasta) ?>">
    </div>
    <div class="campo" style="display:flex;align-items:flex-end;gap:8px;">
      <button type="submit" class="boton-primario"><i class="fas fa-search"></i> Filtrar</button>
      <a href="verAsistencias.php" class="boton-secundario">Limpiar</a>
    </div>
  </form>
</div>

<div class="panel">
  <?php if (empty($asistencias)): ?>
  <div class="panel-vacio">
    <div class="panel-vacio-icono"><i class="fas fa-clipboard-check"></i></div>
    <div class="panel-vacio-titulo">Sin registros</div>
    <div class="panel-vacio-desc">Aplica los filtros para ver registros de asistencia, o espera a que los profesores registren sus sesiones.</div>
  </div>
  <?php else: ?>
  <div class="contenedor-tabla">
    <table class="tabla-datos" id="tablaAsistencias">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Estudiante</th>
          <th>Módulo</th>
          <th>Ciclo</th>
          <th>Estado</th>
          <th>Observación</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($asistencias as $a): ?>
        <tr>
          <td><?= Security::escapeHtml(date('d/m/Y', strtotime($a['fecha']))) ?></td>
          <td><?= Security::escapeHtml($a['nombreEstudiante']) ?></td>
          <td><?= Security::escapeHtml($a['nombreModulo']) ?></td>
          <td><?= Security::escapeHtml($a['nombreCiclo']) ?></td>
          <td>
            <?php
            $chip = match($a['estado']) {
                'presente'    => 'verde',
                'ausente'     => 'rojo',
                'retraso'     => 'naranja',
                'justificado' => 'azul',
                default       => 'gris',
            };
            $label = match($a['estado']) {
                'presente'    => 'Presente',
                'ausente'     => 'Ausente',
                'retraso'     => 'Retraso',
                'justificado' => 'Justificado',
                default       => $a['estado'],
            };
            ?>
            <span class="texto-estado <?= $chip ?>"><?= $label ?></span>
          </td>
          <td><?= Security::escapeHtml($a['observacion'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="texto-suave" style="padding:8px 0;font-size:.83rem;"><?= count($asistencias) ?> registro(s) mostrado(s)</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . "/../comunes/footer.php"; ?>
<script>
iniciarPaginacion('tablaAsistencias', 25);
</script>
