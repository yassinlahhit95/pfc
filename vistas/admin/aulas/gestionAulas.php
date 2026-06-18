<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/aulas.php";

$aulas = listarAulas();

$tiposLegibles = ['teoria' => 'Teoría', 'laboratorio' => 'Laboratorio', 'taller' => 'Taller', 'otro' => 'Otro'];

$titulo_pagina = "AULAPRO | GESTIÓN DE AULAS";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>AULAS</h1>
    <div class="botones-accion">
        <a href="ocupacionAula.php" class="boton-secundario"><i class="fas fa-table"></i> OCUPACIÓN POR AULA</a>
        <a href="agregarAula.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVA AULA</a>
    </div>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores)): ?>if (window.Toast) Toast.show(<?= json_encode(is_array($errores) ? implode(' ', $errores) : $errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="panel">
    <div class="titulo-tarjeta"><h3>Listado de Aulas</h3></div>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAulas">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Planta</th>
                    <th>Tipo</th>
                    <th>Capacidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($aulas)) { ?>
                    <tr><td colspan="7" class="vacio">No hay aulas registradas.</td></tr>
                <?php } else { ?>
                    <?php foreach ($aulas as $aula) { ?>
                    <tr>
                        <td class="texto-negrita">Aula <?= Security::escapeHtml($aula['codigoAula']) ?></td>
                        <td><?= Security::escapeHtml($aula['nombreAula'] ?: '—') ?></td>
                        <td><?= Security::escapeHtml(etiquetaPlanta($aula['planta'])) ?></td>
                        <td><?= Security::escapeHtml($tiposLegibles[$aula['tipoAula']] ?? $aula['tipoAula']) ?></td>
                        <td><?= Security::escapeHtml($aula['capacidad'] !== null ? $aula['capacidad'] : '—') ?></td>
                        <td>
                            <?php if ($aula['activa']) { ?>
                                <span class="indicador-estado activo-verde">Activa</span>
                            <?php } else { ?>
                                <span class="indicador-estado inactivo-rojo">Inactiva</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="ocupacionAula.php?aula=<?= Security::escapeHtml($aula['idAula']) ?>"><i class="fas fa-table"></i> Ver ocupación</a>
                                    <a class="recurso-menu-item" href="modificarAula.php?id=<?= Security::escapeHtml($aula['idAula']) ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarAula.php?id=<?= Security::escapeHtml($aula['idAula']) ?>" onclick="return confirm('¿Eliminar esta aula?')"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaAulas', 10);
</script>
