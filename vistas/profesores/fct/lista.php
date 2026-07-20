<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_fct');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/fct.php";

$idProfesor = (int)$_SESSION['idProfesor'];
$fcts = listarFCTPorProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | FCT";
$seccionActual   = 'fct';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-briefcase"></i> FCT — Formación en Centros de Trabajo</h1>
        <p class="subtitulo-encabezado">Alta y seguimiento de las prácticas de tus alumnos.</p>
    </div>
    <a href="agregar.php" class="boton-primario"><i class="fas fa-plus"></i> Nueva FCT</a>
</div>

<?php if ($exito): ?>
<div class="alerta-exito" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if ($errores): ?>
<div class="alerta-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-triangle"></i>
    <?= is_array($errores) ? Security::escapeHtml(implode(' ', $errores)) : Security::escapeHtml($errores) ?>
</div>
<?php endif; ?>

<div class="panel">
    <?php if (empty($fcts)): ?>
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-briefcase"></i></div>
            <div class="panel-vacio-titulo">Sin FCT registradas</div>
            <div class="panel-vacio-desc">Todavía no has dado de alta ninguna práctica. Usa «Nueva FCT» para empezar.</div>
        </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaFCT">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Empresa</th>
                    <th>Fechas</th>
                    <th style="text-align:center">Horas</th>
                    <th style="text-align:center">Nota / Apto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fcts as $fct): ?>
                <tr>
                    <td><strong><?= Security::escapeHtml($fct['nombreEstudiante']) ?></strong><?php if ((int)$fct['fase'] > 1): ?> <span class="texto-suave">(fase <?= (int)$fct['fase'] ?>)</span><?php endif; ?></td>
                    <td><?= Security::escapeHtml($fct['nombreCiclo']) ?></td>
                    <td><?= Security::escapeHtml($fct['empresa']) ?></td>
                    <td>
                        <?= !empty($fct['fechaInicio']) ? date('d/m/Y', strtotime($fct['fechaInicio'])) : '—' ?>
                        —
                        <?= !empty($fct['fechaFin']) ? date('d/m/Y', strtotime($fct['fechaFin'])) : '—' ?>
                    </td>
                    <td style="text-align:center">
                        <?= (int)($fct['horasRealizadas'] ?? 0) ?> / <?= $fct['horasTotales'] !== null ? (int)$fct['horasTotales'] : '—' ?>
                    </td>
                    <td style="text-align:center">
                        <?php if ($fct['nota'] !== null): ?>
                            <span class="texto-estado <?= (float)$fct['nota'] >= 5 ? 'verde' : 'rojo' ?>"><?= Security::escapeHtml($fct['nota']) ?></span>
                        <?php elseif ($fct['apto'] !== null): ?>
                            <span class="texto-estado <?= (int)$fct['apto'] === 1 ? 'verde' : 'rojo' ?>"><?= (int)$fct['apto'] === 1 ? 'Apto' : 'No apto' ?></span>
                        <?php else: ?>
                            <span class="texto-suave">Sin evaluar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button type="button" class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="editar.php?id=<?= (int)$fct['idFCT'] ?>"><i class="fas fa-edit"></i> Editar / Calificar</a>
                                <div class="recurso-menu-sep"></div>
                                <a class="recurso-menu-item peligro" href="#"
                                   data-modal-borrar
                                   data-id="<?= (int)$fct['idFCT'] ?>"
                                   data-tipo="FCT"
                                   data-nombre="<?= Security::escapeHtml($fct['nombreEstudiante']) ?>"
                                   data-extra="<?= Security::escapeHtml($fct['empresa']) ?>"
                                   data-url="/controladores/profesores/fct/borrar.php"
                                   data-campo="idFCT">
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
if (typeof iniciarPaginacion === 'function' && document.getElementById('tablaFCT')) {
    iniciarPaginacion('tablaFCT', 15);
}
</script>
