<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor    = (int)$_SESSION['idProfesor'];
$esTutor       = !empty($_SESSION['esTutor']);
$idCicloTutor  = (int)($_SESSION['idCicloTutor'] ?? 0);
$misModulos    = ($esTutor && $idCicloTutor)
    ? listarModulosDeCicloConNombre($idCicloTutor)
    : listarModulosDeProfesor($idProfesor);
$idModulo      = (int)($_GET['idModulo'] ?? 0);
$fechaHoy      = date('Y-m-d');
$fecha         = $_GET['fecha'] ?? $fechaHoy;
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) $fecha = $fechaHoy;

$moduloActual = null;
foreach ($misModulos as $m) {
    if ((int)$m['idModulo'] === $idModulo) { $moduloActual = $m; break; }
}
if ($idModulo && !$moduloActual) $idModulo = 0;

$estudiantes       = $idModulo ? listarEstudiantesDeModulo($idModulo)          : [];
$asistenciasHoy    = $idModulo ? listarAsistenciasPorModuloFecha($idModulo, $fecha) : [];
$fechasRegistradas = $idModulo ? listarFechasConRegistro($idModulo)            : [];

// Absentismo: umbral legal típico del 15 % de horas del módulo
$_umbralAbsent  = 15.0;
$_horasModulo   = (int)($moduloActual['horasMaximas'] ?? 0);
$_absentMap     = [];
if ($idModulo && !empty($estudiantes) && $_horasModulo > 0) {
    foreach ($estudiantes as $_e) {
        $_absentMap[(int)$_e['idEstudiante']] = calcularAbsentismoModulo(
            (int)$_e['idEstudiante'], $idModulo, $_horasModulo, $_umbralAbsent
        );
    }
}
$_estudiantesEnRiesgo = array_filter($_absentMap, fn($a) => $a['excede']);

$asistMap = [];
foreach ($asistenciasHoy as $a) {
    $asistMap[(int)$a['idEstudiante']] = $a;
}

// Cuadro horario: clases de este módulo en el día de la semana seleccionado
$_diasEs   = ['Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado','Sunday'=>'Domingo'];
$_diaSemana = $_diasEs[date('l', strtotime($fecha))] ?? '';
$clasesHoy  = $idModulo ? listarClasesDeModuloPorDia($idModulo, $_diaSemana) : [];

$_av_paleta = ['#4F46E5','#0ea5e9','#10b981','#f59e0b','#ec4899','#8b5cf6'];

$tituloDelPagina = "AULAPRO | Asistencia";
$seccionActual   = "asistencias";
require_once __DIR__ . "/../comunes/nav.php";
?>

<style>
/* ── Attendance page ─────────────────────────────── */
.asist-lista { display: flex; flex-direction: column; gap: 8px; }

.asist-card {
  display: flex; align-items: center; gap: 14px; padding: 13px 16px;
  border: 1.5px solid var(--border, rgba(15,23,42,.07)); border-radius: 12px;
  background: var(--surface); transition: border-color .15s, box-shadow .15s;
}
.asist-card:hover { border-color: var(--border-2); box-shadow: var(--shadow-sm); }

.asist-avatar {
  width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 800; color: #fff; letter-spacing: -.02em;
  user-select: none;
}

.asist-nombre {
  font-size: 13.5px; font-weight: 600; color: var(--text);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  min-width: 130px; flex: 1;
}

.asist-controls {
  display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end;
}

/* Status radio pills */
.asist-picker { display: flex; gap: 4px; }

.asist-opt { position: relative; }
.asist-opt input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
.asist-opt span {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 6px 12px; border-radius: 8px;
  font-size: 12px; font-weight: 600; white-space: nowrap;
  border: 1.5px solid var(--border-2, rgba(15,23,42,.12));
  background: var(--surface); color: var(--dim);
  cursor: pointer; transition: all .14s; user-select: none;
}
.asist-opt:hover span { border-color: var(--dim); color: var(--text); }
.asist-opt.opt-p input:checked ~ span { background: #dcfce7; border-color: #4ade80; color: #15803d; }
.asist-opt.opt-a input:checked ~ span { background: #fee2e2; border-color: #f87171; color: #b91c1c; }
.asist-opt.opt-r input:checked ~ span { background: #ffedd5; border-color: #fb923c; color: #c2410c; }
.asist-opt.opt-j input:checked ~ span { background: #dbeafe; border-color: #60a5fa; color: #1d4ed8; }

/* Observación inline input */
.asist-obs {
  padding: 7px 11px;
  border: 1.5px solid var(--border-2, rgba(15,23,42,.12));
  border-radius: 8px; font-size: 13px; font-family: inherit;
  background: var(--surface-2, #f8fafc); color: var(--text);
  width: 180px; transition: border-color .15s, background .15s;
  autocomplete: off;
}
.asist-obs:focus { outline: none; border-color: var(--accent); background: var(--surface); box-shadow: 0 0 0 3px var(--ring, rgba(79,70,229,.2)); }
.asist-obs::placeholder { color: var(--mut); font-size: 12px; }

/* Quick-action bar */
.asist-topbar {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 12px; margin-bottom: 18px;
}
.asist-topbar-left { display: flex; flex-direction: column; gap: 2px; }
.asist-topbar-titulo { font-weight: 700; font-size: 15px; color: var(--text); }
.asist-topbar-fecha  { font-size: 13px; color: var(--mut); }
.asist-acciones { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

/* Live summary chips */
.asist-resumen { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
.asist-chip {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;
}
.chip-p { background: #dcfce7; color: #15803d; }
.chip-a { background: #fee2e2; color: #b91c1c; }
.chip-r { background: #ffedd5; color: #c2410c; }
.chip-j { background: #dbeafe; color: #1d4ed8; }
.chip-t { background: var(--surface-2); color: var(--dim); }

/* Schedule context bar */
.horario-bar {
  display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
  padding: 12px 16px; border-radius: 10px; margin-bottom: 16px;
  font-size: 13px;
}
.horario-bar.tiene-clase {
  background: color-mix(in oklab, var(--accent) 7%, var(--surface));
  border: 1.5px solid color-mix(in oklab, var(--accent) 25%, transparent);
  color: var(--text);
}
.horario-bar.sin-clase {
  background: #fef3c7; border: 1.5px solid #fbbf24; color: #92400e;
}
.horario-franja {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 12px; border-radius: 8px;
  background: var(--surface); border: 1px solid var(--border-2);
  font-weight: 600; font-size: 12px; color: var(--dim);
}
.horario-franja i { color: var(--accent); }
.horario-aula {
  font-size: 11px; font-weight: 500;
  color: var(--mut); margin-top: 1px;
}

@media (max-width: 640px) {
  .asist-obs { width: 100%; }
  .asist-controls { width: 100%; }
  .asist-card { flex-wrap: wrap; }
  .asist-picker { flex-wrap: wrap; }
}
</style>

<div class="cabecera">
  <h1><i class="fas fa-clipboard-check"></i> Registro de Asistencia</h1>
</div>

<?php if ($exito): ?>
<div class="alerta-exito" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if ($errores): ?>
<div class="alerta-error" style="margin-bottom:16px;"><i class="fas fa-times-circle"></i> <?= is_array($errores) ? implode(' ', array_map([Security::class, 'escapeHtml'], $errores)) : Security::escapeHtml($errores) ?></div>
<?php endif; ?>

<!-- Selector de módulo y fecha -->
<div class="panel margen-abajo">
  <form method="GET" action="" class="formulario">
    <div class="campo">
      <label>Módulo</label>
      <select name="idModulo" onchange="this.form.submit()">
        <option value="">Selecciona un módulo</option>
        <?php foreach ($misModulos as $m): ?>
        <option value="<?= (int)$m['idModulo'] ?>" <?= $idModulo === (int)$m['idModulo'] ? 'selected' : '' ?>>
          <?= Security::escapeHtml($m['nombreModulo']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php if ($idModulo): ?>
    <div class="campo">
      <label>Fecha</label>
      <input type="date" name="fecha" value="<?= Security::escapeHtml($fecha) ?>"
             max="<?= $fechaHoy ?>" onchange="this.form.submit()">
    </div>
    <?php endif; ?>
  </form>
</div>

<?php if ($idModulo && $moduloActual): ?>

<?php if (empty($estudiantes)): ?>
<div class="panel">
  <div class="panel-vacio">
    <div class="panel-vacio-icono"><i class="fas fa-users"></i></div>
    <div class="panel-vacio-titulo">Sin estudiantes</div>
    <div class="panel-vacio-desc">No hay estudiantes matriculados en el ciclo de este módulo.</div>
  </div>
</div>
<?php else: ?>

<div class="panel">
  <form method="POST" action="/controladores/profesores/asistencias/guardar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
    <input type="hidden" name="fecha"    value="<?= Security::escapeHtml($fecha) ?>">

    <div class="asist-topbar">
      <div class="asist-topbar-left">
        <span class="asist-topbar-titulo"><?= Security::escapeHtml($moduloActual['nombreModulo']) ?></span>
        <span class="asist-topbar-fecha"><i class="fas fa-calendar-alt" style="margin-right:4px;"></i><?= Security::escapeHtml(date('d/m/Y', strtotime($fecha))) ?></span>
      </div>
      <div class="asist-acciones">
        <?php if (!empty($fechasRegistradas)): ?>
        <select onchange="if(this.value)location.href='registrar.php?idModulo=<?= $idModulo ?>&fecha='+this.value"
                style="padding:7px 12px;border:1.5px solid var(--border-2);border-radius:8px;font-size:13px;background:var(--surface);color:var(--dim);cursor:pointer;">
          <option value="">Historial...</option>
          <?php foreach ($fechasRegistradas as $f): ?>
          <option value="<?= Security::escapeHtml($f) ?>"><?= date('d/m/Y', strtotime($f)) ?></option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <button type="button" class="boton-secundario btn-pequeno" onclick="setAll('presente')">
          <i class="fas fa-check" style="color:#15803d"></i> Todos presentes
        </button>
        <button type="button" class="boton-secundario btn-pequeno" onclick="setAll('ausente')">
          <i class="fas fa-times" style="color:#b91c1c"></i> Todos ausentes
        </button>
      </div>
    </div>

    <!-- Cuadro horario -->
    <?php if (!empty($clasesHoy)): ?>
    <div class="horario-bar tiene-clase">
      <i class="fas fa-calendar-check" style="color:var(--accent);flex-shrink:0;"></i>
      <span style="font-weight:600;margin-right:4px;"><?= Security::escapeHtml($_diaSemana) ?> —</span>
      <?php foreach ($clasesHoy as $cl): ?>
      <div class="horario-franja">
        <i class="fas fa-clock"></i>
        <?= Security::escapeHtml($cl['horaInicio']) ?> – <?= Security::escapeHtml($cl['horaFin']) ?>
        <?php if (!empty($cl['codigoAula'])): ?>
        <span class="horario-aula">· <?= Security::escapeHtml($cl['codigoAula']) ?></span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php elseif ($_diaSemana && $idModulo): ?>
    <div class="horario-bar sin-clase">
      <i class="fas fa-triangle-exclamation"></i>
      <span>Este módulo no tiene clases programadas para el <strong><?= Security::escapeHtml($_diaSemana) ?></strong>. Verifica el cuadro horario.</span>
    </div>
    <?php endif; ?>

    <!-- Alerta de absentismo -->
    <?php if (!empty($_estudiantesEnRiesgo)): ?>
    <div class="horario-bar sin-clase" style="margin-bottom:0;">
      <i class="fas fa-triangle-exclamation" style="color:#b45309;flex-shrink:0;"></i>
      <span>
        <strong><?= count($_estudiantesEnRiesgo) ?> alumno(s) superan el <?= $_umbralAbsent ?>% de absentismo</strong>
        (umbral legal de pérdida de evaluación continua).
        <?php foreach ($_estudiantesEnRiesgo as $_idE => $_datos): ?>
          <?php foreach ($estudiantes as $_est): ?>
            <?php if ((int)$_est['idEstudiante'] === $_idE): ?>
              <span style="display:inline-block;margin:.2em .4em;background:rgba(180,83,9,.12);border-radius:5px;padding:1px 7px;font-size:.8em;">
                <?= Security::escapeHtml($_est['nombreEstudiante']) ?>
                — <?= $_datos['ausencias'] ?>h ausente
                (<?= $_datos['porcentaje'] ?>% / <?= $_umbralAbsent ?>%)
              </span>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </span>
    </div>
    <?php endif; ?>

    <!-- Resumen en vivo -->
    <div class="asist-resumen" id="resumenAsist">
      <span class="asist-chip chip-t"><i class="fas fa-users"></i> <span id="cnt-total"><?= count($estudiantes) ?></span> total</span>
      <span class="asist-chip chip-p"><i class="fas fa-check-circle"></i> <span id="cnt-p">0</span> presentes</span>
      <span class="asist-chip chip-a"><i class="fas fa-times-circle"></i> <span id="cnt-a">0</span> ausentes</span>
      <span class="asist-chip chip-r"><i class="fas fa-clock"></i> <span id="cnt-r">0</span> retrasos</span>
      <span class="asist-chip chip-j"><i class="fas fa-file-alt"></i> <span id="cnt-j">0</span> justificados</span>
    </div>

    <div class="asist-lista">
      <?php foreach ($estudiantes as $e):
        $idEst  = (int)$e['idEstudiante'];
        $actual = $asistMap[$idEst] ?? null;
        $est    = $actual['estado'] ?? 'presente';

        $partes    = explode(' ', trim($e['nombreEstudiante']));
        $iniciales = mb_strtoupper(mb_substr($partes[0], 0, 1));
        if (count($partes) > 1) $iniciales .= mb_strtoupper(mb_substr($partes[1], 0, 1));
        $avColor = $_av_paleta[ord($iniciales[0]) % count($_av_paleta)];
      ?>
      <div class="asist-card">
        <div class="asist-avatar" style="background:<?= $avColor ?>">
          <?= Security::escapeHtml($iniciales) ?>
        </div>
        <div style="flex:1;min-width:0;">
          <span class="asist-nombre"><?= Security::escapeHtml($e['nombreEstudiante']) ?></span>
          <?php if (!empty($_absentMap[$idEst]) && $_absentMap[$idEst]['ausencias'] > 0): ?>
          <span title="<?= $_absentMap[$idEst]['ausencias'] ?>h ausente — <?= $_absentMap[$idEst]['porcentaje'] ?>% del módulo"
                class="texto-estado <?= $_absentMap[$idEst]['excede'] ? 'rojo' : 'naranja' ?>"
                style="font-size:.65rem;display:block;margin-top:2px;">
            <?= $_absentMap[$idEst]['excede'] ? '⚠ Absentismo: ' : '' ?><?= $_absentMap[$idEst]['porcentaje'] ?>%
          </span>
          <?php endif; ?>
        </div>
        <div class="asist-controls">
          <input type="hidden" name="registros[<?= $idEst ?>][idEstudiante]" value="<?= $idEst ?>">
          <div class="asist-picker">
            <label class="asist-opt opt-p">
              <input type="radio" name="registros[<?= $idEst ?>][estado]" value="presente" class="r-estado" <?= $est === 'presente' ? 'checked' : '' ?>>
              <span><i class="fas fa-check-circle"></i> Presente</span>
            </label>
            <label class="asist-opt opt-a">
              <input type="radio" name="registros[<?= $idEst ?>][estado]" value="ausente" class="r-estado" <?= $est === 'ausente' ? 'checked' : '' ?>>
              <span><i class="fas fa-times-circle"></i> Ausente</span>
            </label>
            <label class="asist-opt opt-r">
              <input type="radio" name="registros[<?= $idEst ?>][estado]" value="retraso" class="r-estado" <?= $est === 'retraso' ? 'checked' : '' ?>>
              <span><i class="fas fa-clock"></i> Retraso</span>
            </label>
            <label class="asist-opt opt-j">
              <input type="radio" name="registros[<?= $idEst ?>][estado]" value="justificado" class="r-estado" <?= $est === 'justificado' ? 'checked' : '' ?>>
              <span><i class="fas fa-file-alt"></i> Justificado</span>
            </label>
          </div>
          <input type="text" name="registros[<?= $idEst ?>][observacion]"
                 class="asist-obs" placeholder="Observación..."
                 autocomplete="off"
                 value="<?= Security::escapeHtml($actual['observacion'] ?? '') ?>">
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="acciones">
      <button type="submit" class="boton-primario">
        <i class="fas fa-save"></i> Guardar Asistencia
      </button>
    </div>
  </form>
</div>

<?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . "/../comunes/footer.php"; ?>
<script>
function setAll(estado) {
    document.querySelectorAll('.r-estado[value="' + estado + '"]').forEach(function(r) {
        r.checked = true;
    });
    actualizarResumen();
}

function actualizarResumen() {
    var counts = { presente: 0, ausente: 0, retraso: 0, justificado: 0 };
    document.querySelectorAll('.r-estado:checked').forEach(function(r) {
        if (counts[r.value] !== undefined) counts[r.value]++;
    });
    document.getElementById('cnt-p').textContent = counts.presente;
    document.getElementById('cnt-a').textContent = counts.ausente;
    document.getElementById('cnt-r').textContent = counts.retraso;
    document.getElementById('cnt-j').textContent = counts.justificado;
}

document.querySelectorAll('.r-estado').forEach(function(r) {
    r.addEventListener('change', actualizarResumen);
});

actualizarResumen();
</script>
