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
    <p class="subtitulo-encabezado">Sustituye las reglas de nota fijas (peso examen/reto, aprobado=5, 2 evaluaciones) por una configuración propia del centro.</p>
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
    <button class="aw-paso-btn" data-paso="periodos"><span>3</span> Períodos</button>
    <button class="aw-paso-btn" data-paso="tipos"><span>4</span> Tipos de evaluación</button>
    <button class="aw-paso-btn" data-paso="calificacion"><span>5</span> Reglas de promoción</button>
    <button class="aw-paso-btn" data-paso="fct"><span>6</span> FCT</button>
    <button class="aw-paso-btn" data-paso="tfg"><span>7</span> TFG / Proyecto</button>
    <button class="aw-paso-btn" data-paso="retos"><span>8</span> Retos</button>
    <button class="aw-paso-btn" data-paso="plantillas"><span>9</span> Plantillas</button>
  </nav>

  <div class="aw-contenido">

    <!-- PASO 1: GENERAL -->
    <section class="aw-paso" data-paso="general">
      <h2>Información general</h2>
      <p class="aw-ayuda">Si aún no has creado una configuración, empieza aquí. Puedes tener varias configuraciones guardadas; solo la que actives (paso final) afecta a las notas reales.</p>
      <?php if (!$idConfig): ?>
      <form class="formulario" id="aw-form-crear">
        <div class="campo">
          <label for="awc-nombre">Nombre de la configuración</label>
          <input type="text" id="awc-nombre" name="nombre" required maxlength="150" placeholder="p.ej. Curso 2026-2027">
        </div>
        <div class="campo">
          <label for="awc-tipoEducacion">Tipo de educación</label>
          <select id="awc-tipoEducacion" name="tipoEducacion">
            <option value="grado_basico">FP Grado Básico</option>
            <option value="grado_medio">FP Grado Medio</option>
            <option value="grado_superior">FP Grado Superior</option>
            <option value="colegio">Colegio (Primaria/ESO/Bachillerato)</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="campo">
          <label for="awc-anioAcademico">Año académico</label>
          <input type="text" id="awc-anioAcademico" name="anioAcademico" maxlength="9" placeholder="2026-2027">
        </div>
        <div class="acciones">
          <button type="submit" class="boton-primario">Crear configuración</button>
        </div>
      </form>
      <?php else: ?>
      <form class="formulario" id="aw-form-general">
        <div class="campo">
          <label for="awg-nombre">Nombre</label>
          <input type="text" id="awg-nombre" name="nombre" required maxlength="150" value="<?= Security::escapeHtml($configActiva['nombre']) ?>">
        </div>
        <div class="campo">
          <label for="awg-tipoEducacion">Tipo de educación</label>
          <select id="awg-tipoEducacion" name="tipoEducacion">
            <option value="grado_basico" <?= $configActiva['tipoEducacion']==='grado_basico'?'selected':'' ?>>FP Grado Básico</option>
            <option value="grado_medio" <?= $configActiva['tipoEducacion']==='grado_medio'?'selected':'' ?>>FP Grado Medio</option>
            <option value="grado_superior" <?= $configActiva['tipoEducacion']==='grado_superior'?'selected':'' ?>>FP Grado Superior</option>
            <option value="colegio" <?= $configActiva['tipoEducacion']==='colegio'?'selected':'' ?>>Colegio (Primaria/ESO/Bachillerato)</option>
            <option value="otro" <?= $configActiva['tipoEducacion']==='otro'?'selected':'' ?>>Otro</option>
          </select>
        </div>
        <div class="campo">
          <label for="awg-anioAcademico">Año académico</label>
          <input type="text" id="awg-anioAcademico" name="anioAcademico" maxlength="9" value="<?= Security::escapeHtml($configActiva['anioAcademico'] ?? '') ?>">
        </div>
        <div class="acciones">
          <button type="submit" class="boton-primario">Guardar</button>
        </div>
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
      <div class="formulario">
        <div class="campo">
          <label for="aw-curso-ciclo">Ciclo</label>
          <select id="aw-curso-ciclo">
            <?php foreach ($ciclos as $ciclo): ?>
            <option value="<?= (int)$ciclo['idCiclo'] ?>" <?= (int)$ciclo['idCiclo'] === $idCicloSeleccionado ? 'selected' : '' ?>><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="contenedor-tabla">
      <table class="tabla-datos" id="tabla-cursos">
        <thead><tr><th>Nombre</th><th>Orden</th><th></th></tr></thead>
        <tbody>
          <?php if (!$cursosDelCiclo): ?>
          <tr><td colspan="3" class="vacio">Este ciclo todavía no tiene cursos definidos.</td></tr>
          <?php endif; ?>
          <?php foreach ($cursosDelCiclo as $curso): ?>
          <tr data-id="<?= (int)$curso['idCurso'] ?>">
            <td><?= Security::escapeHtml($curso['nombre']) ?></td>
            <td><?= (int)$curso['orden'] ?></td>
            <td><button type="button" class="btn-accion btn-eliminar aw-eliminar-curso" data-id="<?= (int)$curso['idCurso'] ?>" title="Eliminar"><i class="fas fa-trash"></i></button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
      <form class="formulario" id="aw-form-curso">
        <input type="hidden" name="idCiclo" value="<?= $idCicloSeleccionado ?>">
        <div class="campo">
          <label for="aw-curso-nombre-preset">Nombre</label>
          <select id="aw-curso-nombre-preset">
            <option value="1º">1º</option>
            <option value="2º">2º</option>
            <option value="3º">3º</option>
            <option value="4º">4º</option>
            <option value="__custom__">Otro (personalizado)…</option>
          </select>
          <input type="text" name="nombre" id="aw-curso-nombre" maxlength="40" required placeholder="Nombre personalizado" value="1º" style="display:none;margin-top:6px;">
        </div>
        <div class="campo"><label for="awcur-orden">Orden</label><input type="number" id="awcur-orden" name="orden" value="1" min="1"></div>
        <div class="acciones">
          <button type="submit" class="boton-secundario">Añadir curso</button>
        </div>
      </form>
    </section>

    <!-- PASO 3: PERÍODOS -->
    <section class="aw-paso oculto" data-paso="periodos">
      <h2>Períodos académicos</h2>
      <p class="aw-ayuda">Sustituye las 2 evaluaciones fijas: crea los períodos que uses (evaluaciones, recuperaciones, extraordinaria...). Una recuperación puede enlazarse a su período ordinario para que solo cuente si mejora la nota.</p>
      <div class="contenedor-tabla">
      <table class="tabla-datos" id="tabla-periodos">
        <thead><tr><th>Nombre</th><th>Tipo</th><th>Orden</th><th>Recupera a</th><th>Visible</th><th></th></tr></thead>
        <tbody>
          <?php if (!$periodos): ?>
          <tr><td colspan="6" class="vacio">Todavía no hay períodos académicos definidos.</td></tr>
          <?php endif; ?>
          <?php foreach ($periodos as $periodo): ?>
          <tr data-id="<?= (int)$periodo['idPeriodo'] ?>">
            <td><?= Security::escapeHtml($periodo['nombre']) ?></td>
            <td><?= Security::escapeHtml($periodo['tipo']) ?></td>
            <td><?= (int)$periodo['orden'] ?></td>
            <td><?= $periodo['idPeriodoRecuperaDe'] ? '#'.(int)$periodo['idPeriodoRecuperaDe'] : '—' ?></td>
            <td><?= $periodo['visible'] ? 'Sí' : 'No' ?></td>
            <td><button type="button" class="btn-accion btn-eliminar aw-eliminar-periodo" data-id="<?= (int)$periodo['idPeriodo'] ?>" title="Eliminar"><i class="fas fa-trash"></i></button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
      <form class="formulario" id="aw-form-periodo">
        <div class="campo"><label for="awp-nombre">Nombre</label><input type="text" id="awp-nombre" name="nombre" maxlength="80" required placeholder="p.ej. 3ª Evaluación"></div>
        <div class="campo">
          <label for="awp-tipo">Tipo</label>
          <select id="awp-tipo" name="tipo">
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
        <div class="campo"><label for="awp-orden">Orden</label><input type="number" id="awp-orden" name="orden" value="1" min="1"></div>
        <div class="campo">
          <label for="awp-recuperaDe">Recupera al período (opcional)</label>
          <select id="awp-recuperaDe" name="idPeriodoRecuperaDe">
            <option value="">— Ninguno —</option>
            <?php foreach ($periodos as $periodo): if ($periodo['tipo'] === 'recuperacion') continue; ?>
            <option value="<?= (int)$periodo['idPeriodo'] ?>">#<?= (int)$periodo['idPeriodo'] ?> — <?= Security::escapeHtml($periodo['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="campo-checkbox-grupo">
          <label class="campo-checkbox"><input type="checkbox" name="visible" checked> Visible</label>
        </div>
        <div class="acciones">
          <button type="submit" class="boton-secundario">Añadir período</button>
        </div>
      </form>
    </section>

    <!-- PASO 4: TIPOS DE EVALUACIÓN -->
    <section class="aw-paso oculto" data-paso="tipos">
      <h2>Tipos de evaluación</h2>
      <p class="aw-ayuda">Sustituye el peso fijo 75%/25% examen/reto: cada tipo tiene un peso relativo (p.ej. Examen=3, Reto=1 reproduce 75%/25%). El "origen" indica de dónde saca la nota el motor.</p>
      <div class="contenedor-tabla">
      <table class="tabla-datos" id="tabla-tipos">
        <thead><tr><th>Nombre</th><th>Peso</th><th>Origen</th><th>Obligatorio</th><th>En media</th><th></th></tr></thead>
        <tbody>
          <?php if (!$tipos): ?>
          <tr><td colspan="6" class="vacio">Todavía no hay tipos de evaluación definidos.</td></tr>
          <?php endif; ?>
          <?php foreach ($tipos as $tipo): ?>
          <tr data-id="<?= (int)$tipo['idTipo'] ?>">
            <td><?= Security::escapeHtml($tipo['nombre']) ?></td>
            <td><?= Security::escapeHtml($tipo['peso']) ?></td>
            <td><?= Security::escapeHtml($tipo['origen']) ?></td>
            <td><?= $tipo['obligatorio'] ? 'Sí' : 'No' ?></td>
            <td><?= $tipo['incluirEnMedia'] ? 'Sí' : 'No' ?></td>
            <td><button type="button" class="btn-accion btn-eliminar aw-eliminar-tipo" data-id="<?= (int)$tipo['idTipo'] ?>" title="Eliminar"><i class="fas fa-trash"></i></button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
      <form class="formulario" id="aw-form-tipo">
        <div class="campo"><label for="awt-nombre">Nombre</label><input type="text" id="awt-nombre" name="nombre" maxlength="80" required placeholder="p.ej. Portafolio"></div>
        <div class="campo"><label for="awt-peso">Peso relativo</label><input type="number" id="awt-peso" name="peso" value="1" step="0.01" min="0" required></div>
        <div class="campo">
          <label for="awt-origen">Origen (de dónde saca la nota)</label>
          <select id="awt-origen" name="origen">
            <option value="examen">Examen (evaluaciones/períodos)</option>
            <option value="reto">Reto (sistema de retos existente)</option>
            <option value="ra_ce">RA/CE (resultados de aprendizaje)</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="campo"><label for="awt-orden">Orden</label><input type="number" id="awt-orden" name="orden" value="1" min="1"></div>
        <div class="campo-checkbox-grupo">
          <label class="campo-checkbox"><input type="checkbox" name="obligatorio"> Obligatorio (sin nota = módulo Pendiente)</label>
          <label class="campo-checkbox"><input type="checkbox" name="incluirEnMedia" checked> Incluir en la media del módulo</label>
        </div>
        <div class="acciones">
          <button type="submit" class="boton-secundario">Añadir tipo</button>
        </div>
      </form>
    </section>

    <!-- PASO 5: REGLAS DE PROMOCIÓN -->
    <section class="aw-paso oculto" data-paso="calificacion">
      <h2>Reglas de promoción</h2>
      <p class="aw-ayuda">La escala (0-10), el aprobado (5) y que la nota final del módulo sea un número entero los fija la normativa de FP española — no son configurables aquí. Lo que sí decide cada centro es cómo pesa el TFG y qué hace falta para promocionar de curso.</p>
      <form class="formulario" id="aw-form-calificacion">
        <div class="campo"><label for="awcal-pesoTfg">Peso del TFG en la media global</label><input type="number" id="awcal-pesoTfg" name="pesoTfgEnMedia" step="0.01" value="<?= Security::escapeHtml($politica['pesoTfgEnMedia'] ?? 1) ?>"></div>
        <div class="campo"><label for="awcal-notaMinGlobal">Nota mínima global</label><input type="number" id="awcal-notaMinGlobal" name="notaMinimaGlobal" step="0.01" value="<?= Security::escapeHtml($promocion['notaMinimaGlobal'] ?? 5) ?>"></div>
        <div class="campo"><label for="awcal-modPendientes">Módulos pendientes permitidos para promocionar</label><input type="number" id="awcal-modPendientes" name="permiteModulosPendientes" min="0" value="<?= Security::escapeHtml($promocion['permiteModulosPendientes'] ?? 0) ?>"></div>
        <div class="campo-checkbox-grupo">
          <label class="campo-checkbox"><input type="checkbox" name="requiereTodosModulos" <?= empty($promocion) || $promocion['requiereTodosModulos'] ? 'checked' : '' ?>> Requiere todos los módulos calificados para aprobar el curso</label>
        </div>
        <div class="acciones">
          <button type="submit" class="boton-primario">Guardar reglas</button>
        </div>
      </form>
    </section>

    <!-- PASO 6: FCT -->
    <section class="aw-paso oculto" data-paso="fct">
      <h2>FCT (Formación en Centros de Trabajo)</h2>
      <form class="formulario" id="aw-form-fct">
        <div class="campo-checkbox-grupo">
          <label class="campo-checkbox"><input type="checkbox" name="habilitado" <?= !empty($configFCT['habilitado']) ? 'checked' : '' ?>> Habilitada</label>
        </div>
        <div class="campo"><label for="awfct-horas">Horas requeridas por defecto</label><input type="number" id="awfct-horas" name="horasRequeridasDefecto" min="0" value="<?= Security::escapeHtml($configFCT['horasRequeridasDefecto'] ?? 0) ?>"></div>
        <div class="campo">
          <label for="awfct-metodo">Método de evaluación</label>
          <select id="awfct-metodo" name="metodoEvaluacion">
            <option value="nota" <?= ($configFCT['metodoEvaluacion'] ?? '')==='nota'?'selected':'' ?>>Solo nota numérica</option>
            <option value="apto_no_apto" <?= ($configFCT['metodoEvaluacion'] ?? '')==='apto_no_apto'?'selected':'' ?>>Solo apto/no apto</option>
            <option value="ambos" <?= ($configFCT['metodoEvaluacion'] ?? 'ambos')==='ambos'?'selected':'' ?>>Ambos (nota si existe, si no apto/no apto)</option>
          </select>
        </div>
        <div class="campo"><label for="awfct-peso">Peso en la media global</label><input type="number" id="awfct-peso" name="pesoEnMedia" step="0.01" min="0" value="<?= Security::escapeHtml($configFCT['pesoEnMedia'] ?? 0) ?>"></div>
        <div class="campo-checkbox-grupo">
          <label class="campo-checkbox"><input type="checkbox" name="requiereAprobarParaTitular" <?= !empty($configFCT['requiereAprobarParaTitular']) ? 'checked' : '' ?>> Requiere aprobar la FCT para titular</label>
        </div>
        <div class="acciones">
          <button type="submit" class="boton-primario">Guardar FCT</button>
        </div>
      </form>
    </section>

    <!-- PASO 7: TFG -->
    <section class="aw-paso oculto" data-paso="tfg">
      <h2>TFG / Proyecto Final</h2>
      <form class="formulario" id="aw-form-tfg">
        <div class="campo-checkbox-grupo">
          <label class="campo-checkbox"><input type="checkbox" name="habilitado" <?= !empty($configTFG['habilitado']) ? 'checked' : '' ?>> Habilitado</label>
        </div>
        <div class="campo"><label for="awtfg-notaMinima">Nota mínima</label><input type="number" id="awtfg-notaMinima" name="notaMinima" step="0.01" value="<?= Security::escapeHtml($configTFG['notaMinima'] ?? 5) ?>"></div>
        <div class="campo"><label for="awtfg-peso">Peso en la media global</label><input type="number" id="awtfg-peso" name="pesoEnMedia" step="0.01" value="<?= Security::escapeHtml($configTFG['pesoEnMedia'] ?? 1) ?>"></div>
        <div class="acciones">
          <button type="submit" class="boton-primario">Guardar TFG</button>
        </div>
      </form>
    </section>

    <!-- PASO 8: RETOS -->
    <section class="aw-paso oculto" data-paso="retos">
      <h2>Retos (Challenge-Based Learning)</h2>
      <p class="aw-ayuda">El sistema de retos existente sigue funcionando igual; este peso solo afecta a cómo cuenta un reto nuevo en la media del módulo.</p>
      <form class="formulario" id="aw-form-retos">
        <div class="campo"><label for="awretos-peso">Peso por defecto de un reto nuevo</label><input type="number" id="awretos-peso" name="pesoDefecto" step="0.01" value="<?= Security::escapeHtml($configRetos['pesoDefecto'] ?? 1) ?>"></div>
        <div class="acciones">
          <button type="submit" class="boton-primario">Guardar retos</button>
        </div>
      </form>
    </section>

    <!-- PASO 9: PLANTILLAS -->
    <section class="aw-paso oculto" data-paso="plantillas">
      <h2>Plantillas</h2>
      <p class="aw-ayuda">Aplica una plantilla para arrancar rápido (crea una configuración nueva, no toca la activa), o guarda la configuración actual como plantilla reutilizable.</p>
      <div class="contenedor-tabla">
      <table class="tabla-datos">
        <thead><tr><th>Nombre</th><th>Descripción</th><th></th></tr></thead>
        <tbody>
          <?php if (!$plantillas): ?>
          <tr><td colspan="3" class="vacio">No hay plantillas guardadas todavía.</td></tr>
          <?php endif; ?>
          <?php foreach ($plantillas as $plantilla): ?>
          <tr>
            <td><?= Security::escapeHtml($plantilla['nombre']) ?></td>
            <td><?= Security::escapeHtml($plantilla['descripcion'] ?? '') ?></td>
            <td><button type="button" class="boton-secundario aw-aplicar-plantilla" data-id="<?= (int)$plantilla['idPlantilla'] ?>">Aplicar</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
      <?php if ($idConfig): ?>
      <form class="formulario" id="aw-form-guardar-plantilla">
        <div class="campo campo-ancho-total"><label for="awplant-nombre">Nombre de la nueva plantilla</label><input type="text" id="awplant-nombre" name="nombre" maxlength="150" required></div>
        <div class="campo campo-ancho-total"><label for="awplant-descripcion">Descripción</label><textarea id="awplant-descripcion" name="descripcion" maxlength="500"></textarea></div>
        <div class="acciones">
          <button type="submit" class="boton-secundario">Guardar configuración actual como plantilla</button>
        </div>
      </form>
      <?php endif; ?>
    </section>

  </div>
</div>

<script src="../../../public/js/features/academico-wizard.js?v=<?= @filemtime(__DIR__.'/../../../public/js/features/academico-wizard.js') ?>"></script>

<?php include '../comunes/footer.php'; ?>
