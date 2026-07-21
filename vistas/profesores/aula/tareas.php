<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
$datos   = $_SESSION['datos_tarea'] ?? [];
unset($_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_tarea']);

$idProfesor = (int)$_SESSION['idProfesor'];
$modulos    = listarModulosDeProfesor($idProfesor);

$idModuloSeleccionado = (int)($_GET['idModulo'] ?? ($modulos[0]['idModulo'] ?? 0));
$moduloSeleccionado   = null;
foreach ($modulos as $modulo) {
    if ((int)$modulo['idModulo'] === $idModuloSeleccionado) { $moduloSeleccionado = $modulo; break; }
}
if (!$moduloSeleccionado) { $idModuloSeleccionado = 0; }

// Modo edición: cargar la tarea en el formulario
$idEditar     = (int)($_GET['editar'] ?? 0);
$tareaEditar  = null;
if ($idEditar > 0 && $idModuloSeleccionado) {
    $tareaCandidata = obtenerTareaPorIdAula($idEditar);
    if ($tareaCandidata && (int)$tareaCandidata['idModulo'] === $idModuloSeleccionado) $tareaEditar = $tareaCandidata;
}
$mostrarForm = $tareaEditar || isset($_GET['nueva']) || !empty($datos);

$tareas = $idModuloSeleccionado ? listarTareasPorModuloProfesorAula($idModuloSeleccionado) : [];

$tituloDelPagina = 'AULAPRO | TAREAS DEL AULA';
$seccionActual   = 'aula_tareas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-clipboard-list"></i> Tareas del Aula Digital</h1>
        <p class="subtitulo-encabezado">Crea tareas, publícalas para tus estudiantes y corrige sus entregas.</p>
    </div>
    <?php if ($idModuloSeleccionado && !$mostrarForm): ?>
    <a href="tareas.php?idModulo=<?= $idModuloSeleccionado ?>&nueva=1" class="boton-primario">
        <i class="fas fa-plus"></i> Nueva Tarea
    </a>
    <?php endif; ?>
</div>

<?php if ($exito): ?>
<div class="alerta-exito" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if ($errores): ?>
<div class="alerta-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-triangle"></i>
    <?= is_array($errores) ? Security::escapeHtml(implode(' ', $errores)) : Security::escapeHtml($errores) ?>
</div>
<?php endif; ?>

<?php if (empty($modulos)): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-book"></i></div>
        <div class="panel-vacio-titulo">Sin módulos asignados</div>
        <div class="panel-vacio-desc">No tienes módulos asignados. Contacta con administración.</div>
    </div>
</div>
<?php else: ?>

<!-- Selector de módulo -->
<div class="panel margen-abajo">
    <form method="GET" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <label for="idModulo" style="font-weight:600;">Módulo:</label>
        <select name="idModulo" id="idModulo" onchange="this.form.submit()" style="min-width:280px;">
            <?php foreach ($modulos as $modulo): ?>
                <option value="<?= (int)$modulo['idModulo'] ?>" <?= (int)$modulo['idModulo'] === $idModuloSeleccionado ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($modulo['nombreModulo']) ?> (<?= Security::escapeHtml($modulo['nombreCiclo'] ?? '') ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ($mostrarForm && $idModuloSeleccionado): ?>
<!-- Formulario crear / editar -->
<div class="panel margen-abajo">
    <div class="panel-titulo-seccion"><?= $tareaEditar ? 'Editar Tarea' : 'Nueva Tarea' ?></div>
    <form method="POST" action="../../../controladores/profesores/aula/guardarTarea.php" enctype="multipart/form-data" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idModulo" value="<?= $idModuloSeleccionado ?>">
        <input type="hidden" name="idTarea" value="<?= (int)($tareaEditar['idTarea'] ?? 0) ?>">

        <div class="campo ancho-total">
            <label for="titulo">Título *</label>
            <input type="text" name="titulo" id="titulo" maxlength="150" required
                   value="<?= Security::escapeHtml($datos['titulo'] ?? $tareaEditar['titulo'] ?? '') ?>">
        </div>

        <div class="campo ancho-total">
            <label for="descripcion">Descripción / Enunciado</label>
            <textarea name="descripcion" id="descripcion" rows="5"><?= Security::escapeHtml($datos['descripcion'] ?? $tareaEditar['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-fila">
            <div class="campo">
                <label for="archivoAdjunto">Archivo adjunto (opcional)</label>
                <input type="file" name="archivoAdjunto" id="archivoAdjunto" accept=".pdf,.docx,.txt,.zip,.png,.jpg,.jpeg">
                <?php if (!empty($tareaEditar['archivoAdjunto'])): ?>
                <small class="texto-suave">Ya hay un adjunto; si subes otro lo sustituirá.</small>
                <?php else: ?>
                <small class="texto-suave">PDF, DOCX, TXT, ZIP o imagen. Máx. 20 MB.</small>
                <?php endif; ?>
            </div>

            <div class="campo">
                <label for="publicado">Visibilidad</label>
                <label style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;">
                    <input type="checkbox" name="publicado" id="publicado" value="1"
                        <?= !empty($datos) ? (!empty($datos['publicado']) ? 'checked' : '') : (($tareaEditar === null || !empty($tareaEditar['publicado'])) ? 'checked' : '') ?>>
                    Publicada (visible para los estudiantes)
                </label>
                <small class="texto-suave">Al publicar se notifica a los estudiantes del módulo.</small>
            </div>
        </div>

        <div class="campo ancho-total" style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="tareas.php?idModulo=<?= $idModuloSeleccionado ?>" class="boton-secundario">Cancelar</a>
            <button type="submit" class="boton-primario">
                <i class="fas fa-save"></i> <?= $tareaEditar ? 'Guardar Cambios' : 'Crear Tarea' ?>
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Listado de tareas -->
<div class="panel">
    <?php if (empty($tareas)): ?>
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-clipboard-list"></i></div>
            <div class="panel-vacio-titulo">Sin tareas</div>
            <div class="panel-vacio-desc">Todavía no has creado tareas para este módulo. Usa «Nueva Tarea» para empezar.</div>
        </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaTareas">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th style="text-align:center">Entregas</th>
                    <th style="text-align:center">Corregidas</th>
                    <th>Creada</th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tareas as $tarea): ?>
                <tr>
                    <td>
                        <strong><?= Security::escapeHtml($tarea['titulo']) ?></strong>
                        <?php if (!empty($tarea['archivoAdjunto'])): ?> <i class="fas fa-paperclip texto-suave" title="Con adjunto"></i><?php endif; ?>
                    </td>
                    <td>
                        <span class="texto-estado <?= $tarea['publicado'] ? 'verde' : 'gris' ?>">
                            <?= $tarea['publicado'] ? 'Publicada' : 'Borrador' ?>
                        </span>
                    </td>
                    <td style="text-align:center"><?= (int)$tarea['totalEntregas'] ?></td>
                    <td style="text-align:center"><?= (int)$tarea['totalCorregidas'] ?></td>
                    <td><?= date('d/m/Y', strtotime($tarea['fechaCreacion'])) ?></td>
                    <td style="text-align:right">
                        <div class="recurso-menu-wrap">
                            <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="tareaEntregas.php?id=<?= (int)$tarea['idTarea'] ?>">
                                    <i class="fas fa-inbox"></i> Ver Entregas
                                </a>
                                <a class="recurso-menu-item" href="tareas.php?idModulo=<?= $idModuloSeleccionado ?>&editar=<?= (int)$tarea['idTarea'] ?>">
                                    <i class="fas fa-pen"></i> Editar
                                </a>
                                <form method="POST" action="../../../controladores/profesores/aula/publicarTarea.php" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                    <input type="hidden" name="idTarea" value="<?= (int)$tarea['idTarea'] ?>">
                                    <button type="submit" class="recurso-menu-item" style="width:100%;background:none;border:0;cursor:pointer;text-align:left;">
                                        <i class="fas fa-<?= $tarea['publicado'] ? 'eye-slash' : 'bullhorn' ?>"></i>
                                        <?= $tarea['publicado'] ? 'Ocultar' : 'Publicar' ?>
                                    </button>
                                </form>
                                <div class="recurso-menu-sep"></div>
                                <a class="recurso-menu-item peligro" href="#"
                                   data-modal-borrar
                                   data-id="<?= (int)$tarea['idTarea'] ?>"
                                   data-tipo="Tarea"
                                   data-nombre="<?= Security::escapeHtml($tarea['titulo']) ?>"
                                   data-extra="Se eliminarán también todas las entregas"
                                   data-url="/controladores/profesores/aula/borrarTarea.php"
                                   data-campo="idTarea">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php include_once __DIR__ . "/../comunes/footer.php"; ?>
<script>
if (typeof iniciarPaginacion === 'function' && document.getElementById('tablaTareas')) {
    iniciarPaginacion('tablaTareas', 15);
}
</script>
