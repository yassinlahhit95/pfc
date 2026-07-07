<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$estudiantesEliminados = listarEstudiantesEliminados();

$titulo_pagina = 'AULAPRO | PAPELERA DE ESTUDIANTES';
$seccion = 'papelera';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-trash-alt" style="color:#ef4444;margin-right:8px;"></i>Papelera de Estudiantes</h1>
        <p class="subtitulo-encabezado">Estudiantes eliminados. Puedes restaurarlos con todos sus datos y notas.</p>
    </div>
    <div class="acciones-pagina">
        <a href="verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver a Estudiantes</a>
    </div>
</div>

<div class="panel">
    <?php if (empty($estudiantesEliminados)): ?>
        <div style="text-align:center;padding:60px 20px;color:var(--dim);">
            <i class="fas fa-check-circle" style="font-size:3rem;color:#10b981;display:block;margin-bottom:16px;"></i>
            <h3 style="margin:0 0 8px;color:var(--text);">La papelera está vacía</h3>
            <p style="margin:0;">No hay estudiantes eliminados en este momento.</p>
        </div>
    <?php else: ?>
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE COMPLETO</th>
                        <th>EMAIL</th>
                        <th>CICLO</th>
                        <th>NIVEL</th>
                        <th>FECHA ELIMINACIÓN</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantesEliminados as $e): ?>
                    <tr>
                        <td><?= (int)$e['idEstudiante'] ?></td>
                        <td><b><?= mb_strtoupper(Security::escapeHtml($e['nombreEstudiante']), 'UTF-8') ?></b></td>
                        <td><?= Security::escapeHtml($e['emailEstudiante']) ?></td>
                        <td><?= strtoupper(Security::escapeHtml($e['nombreCiclo'] ?? '—')) ?></td>
                        <td>
                            <span class="texto-estado <?= ($e['idNivel'] ?? 0) == 2 ? 'verde' : 'azul' ?>">
                                <?= ($e['idNivel'] ?? 0) == 2 ? 'Grado Superior' : 'Grado Medio' ?>
                            </span>
                        </td>
                        <td>
                            <?= !empty($e['fecha_eliminacion'])
                                ? date('d/m/Y H:i', strtotime($e['fecha_eliminacion']))
                                : '<span class="texto-suave">—</span>' ?>
                        </td>
                        <td>
                            <form method="POST" action="../../../controladores/secretaria/estudiantes/restaurar.php"
                                  data-ajax-confirm="¿Restaurar a «<?= Security::escapeHtml($e['nombreEstudiante']) ?>»? Todos sus datos y notas quedarán intactos.">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                <input type="hidden" name="idEstudiante" value="<?= (int)$e['idEstudiante'] ?>">
                                <button type="submit" class="boton-primario boton-pequeno" title="Restaurar estudiante">
                                    <i class="fas fa-undo"></i> Restaurar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p style="margin-top:16px;padding:0 4px;color:var(--dim);font-size:.85rem;">
            <i class="fas fa-info-circle"></i>
            Total: <?= count($estudiantesEliminados) ?> estudiante<?= count($estudiantesEliminados) !== 1 ? 's' : '' ?> en papelera.
            Al restaurar, todos sus datos, notas y archivos quedarán disponibles de nuevo.
        </p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
