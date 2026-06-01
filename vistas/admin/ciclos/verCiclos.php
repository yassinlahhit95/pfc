<?php
session_start();

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

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div>
<?php } ?>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div>
<?php } ?>

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
                            <div class="botones-accion">
                                <a href="modificarCiclos.php?idCiclo=<?= Security::escapeHtml($ciclo['idCiclo']) ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="borrarCiclo.php?id=<?= Security::escapeHtml($ciclo['idCiclo']) ?>" class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></a>
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

