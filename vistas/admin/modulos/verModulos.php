<?php
session_start();

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeModulosActuales = listarModulos();
$listaDeCiclosParaFiltro = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$titulo_pagina = "AULAPRO | MÓDULOS PROFESIONALES";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <div>
        <h1>MÓDULOS PROFESIONALES</h1>
    </div>
    <div class="acciones-pagina">
        <a href="agregarModulos.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO MÓDULO
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div>
<?php } ?>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div>
<?php } ?>

<div class="panel margen-abajo">
    <div class="caja caja-libre espacio-grande">
        <div class="campo relleno">
            <label for="selectFiltroCiclo">FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo" onchange="filtrarTabla('selectFiltroCiclo', 'tablaModulos')">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= Security::escapeHtml(strtoupper($cicloFiltro['nombreCiclo'])) ?>">
                        [<?= Security::escapeHtml($cicloFiltro['nombreNivel']) ?>] <?= Security::escapeHtml(strtoupper($cicloFiltro['nombreCiclo'])) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaModulos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NIVEL</th>
                    <th>NOMBRE DEL MÓDULO</th>
                    <th>CICLO FORMATIVO</th>
                    <th>PROFESORES ASIGNADOS</th>
                    <th>HORAS TOTALES</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeModulosActuales)) { ?>
                    <tr>
                        <td colspan="7" class="vacio">No hay módulos registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeModulosActuales as $moduloIndividual) {
                        $nombresProfesores = listarNombresProfesoresDeModulo($moduloIndividual['idModulo']);
                    ?>
                    <tr>
                        <td><?= Security::escapeHtml($moduloIndividual['idModulo']) ?></td>
                        <td>
                            <span class="texto-estado <?= $moduloIndividual['idNivel'] == 1 ? 'azul' : 'verde' ?>"><?= $moduloIndividual['idNivel'] == 1 ? 'Grado Medio' : 'Grado Superior' ?></span>
                        </td>
                        <td><b><?= Security::escapeHtml(mb_strtoupper($moduloIndividual['nombreModulo'], 'UTF-8')) ?></b></td>
                        <td>
                            <?php if (!empty($moduloIndividual['abreviaturaCiclo'])) { ?>
                                <b>[<?= Security::escapeHtml($moduloIndividual['abreviaturaCiclo']) ?>]</b> 
                            <?php } ?>
                            <?= Security::escapeHtml(mb_strtoupper($moduloIndividual['nombreCiclo'], 'UTF-8')) ?>
                        </td>
                        <td>
                            <?php if (empty($nombresProfesores)) { ?>
                                <span class="texto-rojo texto-pequeno">
                                    <i class="fas fa-exclamation-triangle"></i> SIN PROFESOR
                                </span>
                            <?php } else { ?>
                                <div class="texto-pequeno">
                                    <?php
                                $listaNombres = '';
                                foreach ($nombresProfesores as $np) {
                                    if ($listaNombres) $listaNombres .= ', ';
                                    $listaNombres .= mb_strtoupper($np, 'UTF-8');
                                }
                                echo Security::escapeHtml($listaNombres);
                                ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?= Security::escapeHtml($moduloIndividual['horasMaximas']) ?> H</td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="asignarProfesorModulo.php?idModulo=<?= Security::escapeHtml($moduloIndividual['idModulo']) ?>"><i class="fas fa-chalkboard-teacher"></i> Asignar profesor</a>
                                    <a class="recurso-menu-item" href="modificarModulos.php?idModulo=<?= Security::escapeHtml($moduloIndividual['idModulo']) ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarModulo.php?id=<?= Security::escapeHtml($moduloIndividual['idModulo']) ?>" onclick="return confirm('¿Eliminar este módulo?')"><i class="fas fa-trash"></i> Eliminar</a>
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>

