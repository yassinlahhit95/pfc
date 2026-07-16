<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../modelos/academico_config.php';
require_once __DIR__ . '/../../../modelos/plantillas_academicas.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';

$configActiva = obtenerConfigAcademicaActiva();
$idConfig = $configActiva['idConfig'] ?? null;
$motorActivo = motorAcademicoActivo();

$periodos = $idConfig ? listarPeriodosAcademicos((int)$idConfig) : [];
$tipos = $idConfig ? listarTiposEvaluacion((int)$idConfig) : [];
$ciclos = listarTodosLosCiclos();
$idCicloSeleccionado = (int)($_GET['idCiclo'] ?? ($ciclos[0]['idCiclo'] ?? 0));
$cursosDelCiclo = $idCicloSeleccionado ? listarCursosDeCiclo($idCicloSeleccionado) : [];
$politica = $idConfig ? obtenerPoliticaCalificacion((int)$idConfig) : null;
$promocion = $idConfig ? obtenerReglasPromocion((int)$idConfig) : null;
$configFCT = $idConfig ? obtenerConfigFCT((int)$idConfig) : null;
$configTFG = $idConfig ? obtenerConfigTFG((int)$idConfig) : null;
$configRetos = $idConfig ? obtenerConfigRetos((int)$idConfig) : null;
$plantillas = listarPlantillasAcademicas();

$titulo_pagina = "AULAPRO | CONFIGURACIÓN ACADÉMICA";
$seccion = 'configuracion_academica';
include_once __DIR__ . '/../comunes/nav.php';
?>

<link rel="stylesheet" href="../../../public/css/features/academico-wizard.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/academico-wizard.css') ?>">

<div class="cabecera">
  <div>
    <h1>CONFIGURACIÓN ACADÉMICA</h1>
    <p class="subtitulo">Sustituye las reglas de nota fijas (peso examen/reto, aprobado=5, 2 evaluaciones) por una configuración propia del centro.</p>
  </div>
  <div>
    <?php if ($motorActivo): ?>
      <span class="texto-estado verde"><i class="fas fa-check-circle"></i> Motor configurable ACTIVO</span>
    <?php else: ?>
      <span class="texto-estado gris"><i class="fas fa-circle-pause"></i> Usando reglas por defecto (sin activar)</span>
    <?php endif; ?>
  </div>
</div>

<input type="hidden" id="aw-csrf" value="<?= Security::escapeHtml(Security::generateCSRFToken()) ?>">
<input type="hidden" id="aw-idConfig" value="<?= (int)($idConfig ?? 0) ?>">

<div class="panel aw-wizard">

  <nav class="aw-pasos">
    <button class="aw-paso-btn activo" data-paso="general"><span>1</span> General</button>
    <button class="aw-paso-btn" data-paso="cursos"><span>2</span> Cursos</button>
    <button class="aw-paso-btn" data-paso="periodos"><span>4</span> Períodos</button>
    <button class="aw-paso-btn" data-paso="tipos"><span>5</span> Tipos de evaluación</button>
    <button class="aw-paso-btn" data-paso="calificacion"><span>6</span> Reglas de calificación</button>
    <button class="aw-paso-btn" data-paso="fct"><span>7</span> FCT</button>
    <button class="aw-paso-btn" data-paso="tfg"><span>8</span> TFG / Proyecto</button>
    <button class="aw-paso-btn" data-paso="retos"><span>9</span> Retos</button>
    <button class="aw-paso-btn" data-paso="plantillas"><span>10</span> Plantillas</button>
  </nav>

  <div class="aw-contenido">

    <!-- PASO 1: GENERAL -->
    <section class="aw-paso" data-paso="general">
      <h2>Información general</h2>
      <p class="aw-ayuda">Si aún no has creado una configuración, empieza aquí. Puedes tener varias configuraciones guardadas; solo la que actives (paso final) afecta a las notas reales.</p>
      <?php if (!$idConfig): ?>
      <form class="formulario" id="aw-form-crear">
        <div class="campo">
          <label>Nombre de la configuración</label>
          <input type="text" name="nombre" required maxlength="150" placeholder="p.ej. Curso 2026-2027">
        </div>
        <div class="campo">
          <label>Tipo de educación</label>
          <select name="tipoEducacion">
            <option value="grado_medio">FP Grado Medio</option>
            <option value="grado_superior">FP Grado Superior</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="campo">
          <label>Año académico</label>
          <input type="text" name="anioAcademico" maxlength="9" placeholder="2026-2027">
        </div>
        <button type="submit" class="boton-primario">Crear configuración</button>
      </form>
      <?php else: ?>
      <form class="formulario" id="aw-form-general">
        <div class="campo">
          <label>Nombre</label>
          <input type="text" name="nombre" required maxlength="150" value="<?= Security::escapeHtml($configActiva['nombre']) ?>">
        </div>
        <div class="campo">
          <label>Tipo de educación</label>
          <select name="tipoEducacion">
            <option value="grado_medio" <?= $configActiva['tipoEducacion']==='grado_medio'?'selected':'' ?>>FP Grado Medio</option>
            <option value="grado_superior" <?= $configActiva['tipoEducacion']==='grado_superior'?'selected':'' ?>>FP Grado Superior</option>
            <option value="otro" <?= $configActiva['tipoEducacion']==='otro'?'selected':'' ?>>Otro</option>
          </select>
        </div>
        <div class="campo">
          <label>Año académico</label>
          <input type="text" name="anioAcademico" maxlength="9" value="<?= Security::escapeHtml($configActiva['anioAcademico'] ?? '') ?>">
        </div>
        <button type="submit" class="boton-primario">Guardar</button>
      </form>
      <?php if (!$motorActivo): ?>
      <div class="aw-activar-caja">
        <p>Cuando esta configuración esté lista, actívala para que empiece a usarse en el cálculo de notas.</p>
        <button type="button" class="boton-primario" id="aw-activar"><i class="fas fa-power-off"></i> Activar esta configuración</button>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </section>

    <!-- PASO 2: CURSOS -->
    <section class="aw-paso oculto" data-paso="cursos">
      <h2>Cursos por ciclo</h2>
      <p class="aw-ayuda">Sustituye el 1º/2º fijo: define los cursos de cada ciclo formativo (puedes añadir más de 2, o nombrarlos como quieras).</p>
      <div class="campo">
        <label>Ciclo</label>
        <select id="aw-curso-ciclo">
          <?php foreach ($ciclos as $ciclo): ?>
          <option value="<?= (int)$ciclo['idCiclo'] ?>" <?= (int)$ciclo['idCiclo'] === $idCicloSeleccionado ? 'selected' : '' ?>><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <table class="tabla-datos" id="tabla-cursos">
        <thead><tr><th>Nombre</th><th>Orden</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($cursosDelCiclo as $curso): ?>
          <tr data-id="<?= (int)$curso['idCurso'] ?>">
            <td><?= Security::escapeHtml($curso['nombre']) ?></td>
            <td><?= (int)$curso['orden'] ?></td>
            <td><button type="button" class="boton-peligro aw-eliminar-curso" data-id="<?= (int)$curso['idCurso'] ?>">Eliminar</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <form class="formulario" id="aw-form-curso">
        <input type="hidden" name="idCiclo" value="<?= $idCicloSeleccionado ?>">
        <div class="campo">
          <label>Nombre</label>
          <select id="aw-curso-nombre-preset">
            <option value="1º">1º</option>
            <option value="2º">2º</option>
            <option value="3º">3º</option>
            <option value="4º">4º</option>
            <option value="__custom__">Otro (personalizado)…</option>
          </select>
          <input type="text" name="nombre" id="aw-curso-nombre" maxlength="40" required placeholder="Nombre personalizado" value="1º" style="display:none;margin-top:6px;">
        </div>
        <div class="campo"><label>Orden</label><input type="number" name="orden" value="1" min="1"></div>
        <button type="submit" class="boton-secundario">Añadir curso</button>
      </form>
    </section>

    <!-- PASO 4: PERÍODOS -->
    <section class="aw-paso oculto" data-paso="periodos">
      <h2>Períodos académicos</h2>
      <p class="aw-ayuda">Sustituye las 2 evaluaciones fijas: crea los períodos que uses (evaluaciones, recuperaciones, extraordinaria...). Una recuperación puede enlazarse a su período ordinario para que solo cuente si mejora la nota.</p>
      <table class="tabla-datos" id="tabla-periodos">
        <thead><tr><th>Nombre</th><th>Tipo</th><th>Orden</th><th>Recupera a</th><th>Visible</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($periodos as $periodo): ?>
          <tr data-id="<?= (int)$periodo['idPeriodo'] ?>">
            <td><?= Security::escapeHtml($periodo['nombre']) ?></td>
            <td><?= Security::escapeHtml($periodo['tipo']) ?></td>
            <td><?= (int)$periodo['orden'] ?></td>
            <td><?= $periodo['idPeriodoRecuperaDe'] ? '#'.(int)$periodo['idPeriodoRecuperaDe'] : '—' ?></td>
            <td><?= $periodo['visible'] ? 'Sí' : 'No' ?></td>
            <td><button type="button" class="boton-peligro aw-eliminar-periodo" data-id="<?= (int)$periodo['idPeriodo'] ?>">Eliminar</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <form class="formulario" id="aw-form-periodo">
        <div class="campo"><label>Nombre</label><input type="text" name="nombre" maxlength="80" required placeholder="p.ej. 3ª Evaluación"></div>
        <div class="campo">
          <label>Tipo</label>
          <select name="tipo">
            <option value="evaluacion">Evaluación</option>
            <option value="recuperacion">Recuperación</option>
            <option value="ordinaria">Ordinaria</option>
            <option value="extraordinaria">Extraordinaria</option>
            <option value="final">Final</option>
            <option value="proyecto">Proyecto</option>
            <option value="practicas">Prácticas</option>
            <option value="certificacion">Certificación</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="campo"><label>Orden</label><input type="number" name="orden" value="1" min="1"></div>
        <div class="campo">
          <label>Recupera al período (opcional)</label>
          <select name="idPeriodoRecuperaDe">
            <option value="">— Ninguno —</option>
            <?php foreach ($periodos as $periodo): if ($periodo['tipo'] === 'recuperacion') continue; ?>
            <option value="<?= (int)$periodo['idPeriodo'] ?>">#<?= (int)$periodo['idPeriodo'] ?> — <?= Security::escapeHtml($periodo['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="visible" checked> Visible</label></div>
        <button type="submit" class="boton-secundario">Añadir período</button>
      </form>
    </section>

    <!-- PASO 5: TIPOS DE EVALUACIÓN -->
    <section class="aw-paso oculto" data-paso="tipos">
      <h2>Tipos de evaluación</h2>
      <p class="aw-ayuda">Sustituye el peso fijo 75%/25% examen/reto: cada tipo tiene un peso relativo (p.ej. Examen=3, Reto=1 reproduce 75%/25%). El "origen" indica de dónde saca la nota el motor.</p>
      <table class="tabla-datos" id="tabla-tipos">
        <thead><tr><th>Nombre</th><th>Peso</th><th>Origen</th><th>Obligatorio</th><th>En media</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($tipos as $tipo): ?>
          <tr data-id="<?= (int)$tipo['idTipo'] ?>">
            <td><?= Security::escapeHtml($tipo['nombre']) ?></td>
            <td><?= Security::escapeHtml($tipo['peso']) ?></td>
            <td><?= Security::escapeHtml($tipo['origen']) ?></td>
            <td><?= $tipo['obligatorio'] ? 'Sí' : 'No' ?></td>
            <td><?= $tipo['incluirEnMedia'] ? 'Sí' : 'No' ?></td>
            <td><button type="button" class="boton-peligro aw-eliminar-tipo" data-id="<?= (int)$tipo['idTipo'] ?>">Eliminar</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <form class="formulario" id="aw-form-tipo">
        <div class="campo"><label>Nombre</label><input type="text" name="nombre" maxlength="80" required placeholder="p.ej. Portafolio"></div>
        <div class="campo"><label>Nota máxima</label><input type="number" name="notaMaxima" value="10" step="0.01" min="0"></div>
        <div class="campo"><label>Peso relativo</label><input type="number" name="peso" value="1" step="0.01" min="0" required></div>
        <div class="campo">
          <label>Origen (de dónde saca la nota)</label>
          <select name="origen">
            <option value="examen">Examen (evaluaciones/períodos)</option>
            <option value="reto">Reto (sistema de retos existente)</option>
            <option value="ra_ce">RA/CE (resultados de aprendizaje)</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="campo"><label>Orden</label><input type="number" name="orden" value="1" min="1"></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="obligatorio"> Obligatorio (sin nota = módulo Pendiente)</label></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="recuperable" checked> Recuperable</label></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="incluirEnMedia" checked> Incluir en la media del módulo</label></div>
        <button type="submit" class="boton-secundario">Añadir tipo</button>
      </form>
    </section>

    <!-- PASO 6: REGLAS DE CALIFICACIÓN -->
    <section class="aw-paso oculto" data-paso="calificacion">
      <h2>Reglas de calificación</h2>
      <p class="aw-ayuda">Sustituye el aprobado=5 y el redondeo a 2 decimales fijos, y las reglas de promoción.</p>
      <form class="formulario" id="aw-form-calificacion">
        <div class="campo"><label>Escala mínima</label><input type="number" name="escalaMin" step="0.01" value="<?= Security::escapeHtml($politica['escalaMin'] ?? 0) ?>"></div>
        <div class="campo"><label>Escala máxima</label><input type="number" name="escalaMax" step="0.01" value="<?= Security::escapeHtml($politica['escalaMax'] ?? 10) ?>"></div>
        <div class="campo"><label>Nota de aprobado</label><input type="number" name="notaAprobado" step="0.01" value="<?= Security::escapeHtml($politica['notaAprobado'] ?? 5) ?>"></div>
        <div class="campo"><label>Decimales</label><input type="number" name="decimales" min="0" max="4" value="<?= Security::escapeHtml($politica['decimales'] ?? 2) ?>"></div>
        <div class="campo"><label>Peso del TFG en la media global</label><input type="number" name="pesoTfgEnMedia" step="0.01" value="<?= Security::escapeHtml($politica['pesoTfgEnMedia'] ?? 1) ?>"></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="requiereTodosModulos" <?= empty($promocion) || $promocion['requiereTodosModulos'] ? 'checked' : '' ?>> Requiere todos los módulos calificados para aprobar el curso</label></div>
        <div class="campo"><label>Nota mínima global</label><input type="number" name="notaMinimaGlobal" step="0.01" value="<?= Security::escapeHtml($promocion['notaMinimaGlobal'] ?? 5) ?>"></div>
        <div class="campo"><label>Módulos pendientes permitidos para promocionar</label><input type="number" name="permiteModulosPendientes" min="0" value="<?= Security::escapeHtml($promocion['permiteModulosPendientes'] ?? 0) ?>"></div>
        <button type="submit" class="boton-primario">Guardar reglas</button>
      </form>
    </section>

    <!-- PASO 7: FCT -->
    <section class="aw-paso oculto" data-paso="fct">
      <h2>FCT (Formación en Centros de Trabajo)</h2>
      <form class="formulario" id="aw-form-fct">
        <div class="campo campo-checkbox"><label><input type="checkbox" name="habilitado" <?= !empty($configFCT['habilitado']) ? 'checked' : '' ?>> Habilitada</label></div>
        <div class="campo"><label>Horas requeridas por defecto</label><input type="number" name="horasRequeridasDefecto" min="0" value="<?= Security::escapeHtml($configFCT['horasRequeridasDefecto'] ?? 0) ?>"></div>
        <div class="campo">
          <label>Método de evaluación</label>
          <select name="metodoEvaluacion">
            <option value="nota" <?= ($configFCT['metodoEvaluacion'] ?? '')==='nota'?'selected':'' ?>>Solo nota numérica</option>
            <option value="apto_no_apto" <?= ($configFCT['metodoEvaluacion'] ?? '')==='apto_no_apto'?'selected':'' ?>>Solo apto/no apto</option>
            <option value="ambos" <?= ($configFCT['metodoEvaluacion'] ?? 'ambos')==='ambos'?'selected':'' ?>>Ambos (nota si existe, si no apto/no apto)</option>
          </select>
        </div>
        <div class="campo"><label>Peso en la media global</label><input type="number" name="pesoEnMedia" step="0.01" min="0" value="<?= Security::escapeHtml($configFCT['pesoEnMedia'] ?? 0) ?>"></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="requiereAprobarParaTitular" <?= !empty($configFCT['requiereAprobarParaTitular']) ? 'checked' : '' ?>> Requiere aprobar la FCT para titular</label></div>
        <button type="submit" class="boton-primario">Guardar FCT</button>
      </form>
    </section>

    <!-- PASO 8: TFG -->
    <section class="aw-paso oculto" data-paso="tfg">
      <h2>TFG / Proyecto Final</h2>
      <form class="formulario" id="aw-form-tfg">
        <div class="campo campo-checkbox"><label><input type="checkbox" name="habilitado" <?= !empty($configTFG['habilitado']) ? 'checked' : '' ?>> Habilitado</label></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="requiereComite" <?= !empty($configTFG['requiereComite']) ? 'checked' : '' ?>> Requiere comité evaluador</label></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="requiereDefensa" <?= !empty($configTFG['requiereDefensa']) ? 'checked' : '' ?>> Requiere defensa</label></div>
        <div class="campo"><label>Nota mínima</label><input type="number" name="notaMinima" step="0.01" value="<?= Security::escapeHtml($configTFG['notaMinima'] ?? 5) ?>"></div>
        <div class="campo"><label>Peso en la media global</label><input type="number" name="pesoEnMedia" step="0.01" value="<?= Security::escapeHtml($configTFG['pesoEnMedia'] ?? 1) ?>"></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="permiteRecuperacion" <?= !empty($configTFG['permiteRecuperacion']) ? 'checked' : '' ?>> Permite convocatoria extraordinaria</label></div>
        <button type="submit" class="boton-primario">Guardar TFG</button>
      </form>
    </section>

    <!-- PASO 9: RETOS -->
    <section class="aw-paso oculto" data-paso="retos">
      <h2>Retos (Challenge-Based Learning)</h2>
      <p class="aw-ayuda">El sistema de retos existente sigue funcionando igual; esto son ajustes adicionales para cuando se usen grupos, fases o rúbricas.</p>
      <form class="formulario" id="aw-form-retos">
        <div class="campo"><label>Peso por defecto de un reto nuevo</label><input type="number" name="pesoDefecto" step="0.01" value="<?= Security::escapeHtml($configRetos['pesoDefecto'] ?? 1) ?>"></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="permiteGrupal" <?= !empty($configRetos['permiteGrupal']) ? 'checked' : '' ?>> Permite retos grupales</label></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="permiteFases" <?= !empty($configRetos['permiteFases']) ? 'checked' : '' ?>> Permite retos con varias fases</label></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="requiereRubrica" <?= !empty($configRetos['requiereRubrica']) ? 'checked' : '' ?>> Requiere rúbrica de evaluación</label></div>
        <div class="campo campo-checkbox"><label><input type="checkbox" name="evaluacionPares" <?= !empty($configRetos['evaluacionPares']) ? 'checked' : '' ?>> Permite evaluación entre compañeros</label></div>
        <button type="submit" class="boton-primario">Guardar retos</button>
      </form>
    </section>

    <!-- PASO 10: PLANTILLAS -->
    <section class="aw-paso oculto" data-paso="plantillas">
      <h2>Plantillas</h2>
      <p class="aw-ayuda">Aplica una plantilla para arrancar rápido (crea una configuración nueva, no toca la activa), o guarda la configuración actual como plantilla reutilizable.</p>
      <table class="tabla-datos">
        <thead><tr><th>Nombre</th><th>Descripción</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($plantillas as $plantilla): ?>
          <tr>
            <td><?= Security::escapeHtml($plantilla['nombre']) ?></td>
            <td><?= Security::escapeHtml($plantilla['descripcion'] ?? '') ?></td>
            <td><button type="button" class="boton-secundario aw-aplicar-plantilla" data-id="<?= (int)$plantilla['idPlantilla'] ?>">Aplicar</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if ($idConfig): ?>
      <form class="formulario" id="aw-form-guardar-plantilla">
        <div class="campo"><label>Nombre de la nueva plantilla</label><input type="text" name="nombre" maxlength="150" required></div>
        <div class="campo campo-ancho-total"><label>Descripción</label><textarea name="descripcion" maxlength="500"></textarea></div>
        <button type="submit" class="boton-secundario">Guardar configuración actual como plantilla</button>
      </form>
      <?php endif; ?>
    </section>

  </div>
</div>

<script src="../../../public/js/features/academico-wizard.js?v=<?= @filemtime(__DIR__.'/../../../public/js/features/academico-wizard.js') ?>"></script>

<?php include '../comunes/footer.php'; ?>
