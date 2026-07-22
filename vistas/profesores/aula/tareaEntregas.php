<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = (int)$_SESSION['idProfesor'];
$idTarea    = (int)($_GET['id'] ?? 0);

$tarea = $idTarea > 0 ? obtenerTareaPorIdAula($idTarea) : null;
if (!$tarea) {
    $_SESSION['errores'] = "Tarea no encontrada.";
    header("Location: tareas.php");
    exit;
}

// Solo módulos que imparte el profesor
$misModulos = listarModulosDeProfesor($idProfesor);
if (!in_array((int)$tarea['idModulo'], array_column($misModulos, 'idModulo'))) {
    $_SESSION['errores'] = "No tienes permiso para ver estas entregas.";
    header("Location: tareas.php");
    exit;
}

$filas = listarEntregasPorTareaAula($idTarea);

$totalEstudiantes = count($filas);
$totalEntregadas  = count(array_filter($filas, fn($fila) => !empty($fila['idEntrega'])));
$totalCorregidas  = count(array_filter($filas, fn($fila) => ($fila['estado'] ?? '') === 'corregida'));

$tituloDelPagina = 'AULAPRO | ENTREGAS — ' . mb_strtoupper($tarea['titulo'], 'UTF-8');
$seccionActual   = 'aula_tareas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-inbox"></i> Entregas — <?= Security::escapeHtml($tarea['titulo']) ?></h1>
        <p class="subtitulo-encabezado">
            <?= Security::escapeHtml($tarea['nombreModulo']) ?> ·
            <span class="texto-estado <?= $tarea['publicado'] ? 'verde' : 'gris' ?>"><?= $tarea['publicado'] ? 'Publicada' : 'Borrador' ?></span>
        </p>
    </div>
    <a href="tareas.php?idModulo=<?= (int)$tarea['idModulo'] ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver a Tareas</a>
</div>

<?php if ($exito): ?>
<div class="alerta-exito" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if ($errores): ?>
<div class="alerta-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-triangle"></i>
    <?= is_array($errores) ? Security::escapeHtml(implode(' ', $errores)) : Security::escapeHtml($errores) ?>
</div>
<?php endif; ?>

<!-- Resumen -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:20px">
    <div class="panel" style="padding:16px 20px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Estudiantes</div>
        <div style="font-size:1.6rem;font-weight:700"><?= $totalEstudiantes ?></div>
    </div>
    <div class="panel" style="padding:16px 20px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Entregadas</div>
        <div style="font-size:1.6rem;font-weight:700;color:var(--azul)"><?= $totalEntregadas ?></div>
    </div>
    <div class="panel" style="padding:16px 20px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Corregidas</div>
        <div style="font-size:1.6rem;font-weight:700;color:var(--verde)"><?= $totalCorregidas ?></div>
    </div>
</div>

<div class="panel">
    <?php if (empty($filas)): ?>
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-user-graduate"></i></div>
            <div class="panel-vacio-titulo">Sin estudiantes</div>
            <div class="panel-vacio-desc">No hay estudiantes matriculados en el ciclo de este módulo.</div>
        </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEntregas">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Estado</th>
                    <th>Entrega</th>
                    <th>Fecha</th>
                    <th style="min-width:260px">Calificación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filas as $fila): ?>
                <tr>
                    <td><strong><?= Security::escapeHtml($fila['nombreEstudiante']) ?></strong></td>
                    <td>
                        <?php if (empty($fila['idEntrega'])): ?>
                            <span class="texto-estado gris">Sin entregar</span>
                        <?php elseif ($fila['estado'] === 'corregida'): ?>
                            <span class="texto-estado verde">Corregida</span>
                        <?php else: ?>
                            <span class="texto-estado azul">Enviada (v<?= (int)$fila['version'] ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($fila['archivoEntrega'])):
                            $archivoEntregaUrl = R2Client::documentoUrl(
                                __DIR__ . '/../../../public/uploads/aula/entregas/' . $fila['archivoEntrega'],
                                '../../../public/uploads/aula/entregas/' . $fila['archivoEntrega'],
                                'aula/entregas/' . $fila['archivoEntrega']
                            );
                        ?>
                            <a href="<?= Security::escapeHtml($archivoEntregaUrl) ?>"
                               target="_blank" class="boton-secundario btn-pequeno">
                                <i class="fas fa-file-download"></i> Archivo
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($fila['respuesta'])): ?>
                            <div class="texto-suave" style="font-size:.82rem;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                                 title="<?= Security::escapeHtml($fila['respuesta']) ?>">
                                <?= Security::escapeHtml(mb_substr($fila['respuesta'], 0, 80)) ?><?= mb_strlen($fila['respuesta']) > 80 ? '…' : '' ?>
                            </div>
                        <?php endif; ?>
                        <?php if (empty($fila['archivoEntrega']) && empty($fila['respuesta'])): ?>
                            <span class="texto-suave">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= !empty($fila['fechaEntrega']) && !empty($fila['idEntrega'])
                            ? date('d/m/Y H:i', strtotime($fila['fechaEntrega']))
                            : '<span class="texto-suave">—</span>' ?>
                    </td>
                    <td>
                        <?php if (!empty($fila['idEntrega'])): ?>
                        <form method="POST" action="../../../controladores/profesores/aula/calificarEntrega.php"
                              style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="idEntrega" value="<?= (int)$fila['idEntrega'] ?>">
                            <input type="number" name="nota" min="0" max="10" step="0.01" required
                                   value="<?= $fila['nota'] !== null ? Security::escapeHtml((string)$fila['nota']) : '' ?>"
                                   placeholder="Nota" style="width:80px;">
                            <input type="text" name="comentario" maxlength="500"
                                   value="<?= Security::escapeHtml($fila['comentarioCalificacion'] ?? '') ?>"
                                   placeholder="Comentario (opcional)" style="flex:1;min-width:120px;">
                            <button type="submit" class="boton-primario btn-pequeno" title="Guardar calificación">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <?php else: ?>
                            <span class="texto-suave">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . "/../comunes/footer.php"; ?>
<script>
if (typeof iniciarPaginacion === 'function' && document.getElementById('tablaEntregas')) {
    iniciarPaginacion('tablaEntregas', 20);
}
</script>
