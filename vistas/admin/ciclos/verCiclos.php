<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$todos_los_ciclos = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$titulo_pagina = "AULAPRO | CICLOS FORMATIVOS";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CICLOS FORMATIVOS</h1>
    <a href="agregarCiclos.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO CICLO
    </a>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="panel margen-abajo">
    <div class="campo">
        <label>FILTRAR POR NIVEL:</label>
        <select id="selectFiltroNivel" onchange="filtrarTabla('selectFiltroNivel', 'tablaCiclos')">
            <option value="">-- Todos los Niveles --</option>
            <?php foreach ($listaNiveles as $nivelFiltro) { ?>
                <option value="<?= Security::escapeHtml($nivelFiltro['nombreNivel']) ?>">
                    <?= Security::escapeHtml($nivelFiltro['nombreNivel']) ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaCiclos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE DEL CICLO</th>
                    <th>NIVEL</th>
                    <th>TUTORES/PROFESORES</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_ciclos)) { ?>
                    <tr><td colspan="5" class="vacio">No hay ciclos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_ciclos as $ciclo) { 
                        $nombresTutores = array_map(['Security', 'escapeHtml'], listarNombresTutoresCiclo($ciclo['idCiclo']));
                        $textoTutores = !empty($nombresTutores) ? implode(", ", $nombresTutores) : '<span class="texto-suave">Sin asignar</span>';
                    ?>
                    <tr>
                        <td><?= Security::escapeHtml($ciclo['idCiclo']) ?></td>
                        <td><b><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></b></td>
                        <td><?= Security::escapeHtml($ciclo['nombreNivel']) ?></td>
                        <td><?= $textoTutores ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="modificarCiclos.php?idCiclo=<?= Security::escapeHtml($ciclo['idCiclo']) ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarCiclo.php?id=<?= Security::escapeHtml($ciclo['idCiclo']) ?>" onclick="return confirm('¿Eliminar este ciclo?')"><i class="fas fa-trash"></i> Eliminar</a>
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

