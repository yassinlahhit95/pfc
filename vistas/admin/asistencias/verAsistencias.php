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
$estadosPermitidos = ['presente', 'ausente', 'retraso', 'justificado'];
$estado       = in_array($_GET['estado'] ?? '', $estadosPermitidos, true) ? $_GET['estado'] : '';

$ciclos      = listarTodosLosCiclos();
$modulos     = $idCiclo ? listarModulosPorCiclo($idCiclo) : [];
$estudiantesCiclo = $idCiclo ? listarEstudiantesPorCiclo($idCiclo) : [];

$asistencias = listarAsistenciasFiltradas(
    $idCiclo, $idModulo, $idEstudiante,
    $fechaDesde ?: null,
    $fechaHasta ?: null,
    $estado ?: null
);

$titulo_pagina = "AulaPro | Asistencias";
$seccion       = "asistencias";
require_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-clipboard-check"></i> Registro de Asistencias</h1>
  </div>
  <a href="../../../controladores/admin/asistencias/exportarCSV.php?<?= http_build_query(array_filter([
    'idCiclo' => $idCiclo, 'idModulo' => $idModulo, 'idEstudiante' => $idEstudiante,
    'fechaDesde' => $fechaDesde, 'fechaHasta' => $fechaHasta, 'estado' => $estado
  ])) ?>" class="boton-secundario">
    <i class="fas fa-download"></i> Exportar CSV
  </a>
</div>

<div class="panel margen-abajo">
  <form method="GET" action="" class="formulario" id="form-filtros-asistencias">
    <div class="form-fila">
      <div class="campo">
        <label for="sel-ciclo-asist">Ciclo</label>
        <select name="idCiclo" id="sel-ciclo-asist" onchange="this.form.submit()">
          <option value="">Todos los ciclos</option>
          <?php foreach ($ciclos as $ciclo): ?>
          <option value="<?= (int)$ciclo['idCiclo'] ?>" <?= $idCiclo === (int)$ciclo['idCiclo'] ? 'selected' : '' ?>>
            <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php if ($idCiclo && $modulos): ?>
      <div class="campo">
        <label for="sel-modulo-asist">Módulo</label>
        <select name="idModulo" id="sel-modulo-asist" onchange="this.form.submit()">
          <option value="">Todos los módulos</option>
          <?php foreach ($modulos as $modulo): ?>
          <option value="<?= (int)$modulo['idModulo'] ?>" <?= $idModulo === (int)$modulo['idModulo'] ? 'selected' : '' ?>>
            <?= Security::escapeHtml($modulo['nombreModulo']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
      <?php if ($idCiclo && $estudiantesCiclo): ?>
      <div class="campo">
        <label for="sel-estudiante-asist">Estudiante</label>
        <select name="idEstudiante" id="sel-estudiante-asist" onchange="this.form.submit()">
          <option value="">Todos los estudiantes</option>
          <?php foreach ($estudiantesCiclo as $estudiante): ?>
          <option value="<?= (int)$estudiante['idEstudiante'] ?>" <?= $idEstudiante === (int)$estudiante['idEstudiante'] ? 'selected' : '' ?>>
            <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php elseif ($idEstudiante): ?>
        <input type="hidden" name="idEstudiante" value="<?= (int)$idEstudiante ?>">
      <?php endif; ?>
    </div>
    <div class="form-fila">
      <div class="campo">
        <label for="sel-estado-asist">Estado</label>
        <select name="estado" id="sel-estado-asist" onchange="this.form.submit()">
          <option value="">Todos los estados</option>
          <option value="presente" <?= $estado === 'presente' ? 'selected' : '' ?>>Presente</option>
          <option value="ausente" <?= $estado === 'ausente' ? 'selected' : '' ?>>Ausente</option>
          <option value="retraso" <?= $estado === 'retraso' ? 'selected' : '' ?>>Retraso</option>
          <option value="justificado" <?= $estado === 'justificado' ? 'selected' : '' ?>>Justificado</option>
        </select>
      </div>
      <div class="campo">
        <label for="fecha-desde-asist">Desde</label>
        <input type="date" name="fechaDesde" id="fecha-desde-asist" value="<?= Security::escapeHtml($fechaDesde) ?>">
      </div>
      <div class="campo">
        <label for="fecha-hasta-asist">Hasta</label>
        <input type="date" name="fechaHasta" id="fecha-hasta-asist" value="<?= Security::escapeHtml($fechaHasta) ?>">
      </div>
    </div>
    <div class="acciones">
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
        <?php foreach ($asistencias as $asistencia): ?>
        <tr>
          <td><?= Security::escapeHtml(date('d/m/Y', strtotime($asistencia['fecha']))) ?></td>
          <td><?= Security::escapeHtml($asistencia['nombreEstudiante']) ?></td>
          <td><?= Security::escapeHtml($asistencia['nombreModulo']) ?></td>
          <td><?= Security::escapeHtml($asistencia['nombreCiclo']) ?></td>
          <td>
            <?php
            $chip = match($asistencia['estado']) {
                'presente'    => 'verde',
                'ausente'     => 'rojo',
                'retraso'     => 'naranja',
                'justificado' => 'azul',
                default       => 'gris',
            };
            $label = match($asistencia['estado']) {
                'presente'    => 'Presente',
                'ausente'     => 'Ausente',
                'retraso'     => 'Retraso',
                'justificado' => 'Justificado',
                default       => $asistencia['estado'],
            };
            ?>
            <span class="texto-estado <?= $chip ?>"><?= Security::escapeHtml($label) ?></span>
          </td>
          <td><?= Security::escapeHtml($asistencia['observacion'] ?? '') ?></td>
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
if (document.getElementById('tablaAsistencias')) {
    iniciarPaginacion('tablaAsistencias', 25);
}
</script>
