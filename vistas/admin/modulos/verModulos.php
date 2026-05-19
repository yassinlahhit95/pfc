<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "AULAPRO | MÓDULOS PROFESIONALES";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeModulosActuales = listarModulos();
$listaDeCiclosParaFiltro = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
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
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel margen-abajo">
    <div class="caja caja-libre espacio-grande">
        <div class="campo relleno">
            <label>FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo" onchange="filtrarTabla('selectFiltroCiclo', 'tablaModulos')">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= strtoupper($cicloFiltro['nombreCiclo']) ?>">
                        [<?= $cicloFiltro['nombreNivel'] ?>] <?= strtoupper($cicloFiltro['nombreCiclo']) ?>
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
                        <td><?= $moduloIndividual['idModulo'] ?></td>
                        <td>
                            <span class="texto-estado <?= $moduloIndividual['idNivel'] == 1 ? 'azul' : 'verde' ?>"><?= $moduloIndividual['idNivel'] == 1 ? 'Grado Medio' : 'Grado Superior' ?></span>
                        </td>
                        <td><b><?= strtoupper($moduloIndividual['nombreModulo']) ?></b></td>
                        <td>
                            <?php if (!empty($moduloIndividual['abreviaturaCiclo'])) { ?>
                                <b>[<?= $moduloIndividual['abreviaturaCiclo'] ?>]</b> 
                            <?php } ?>
                            <?= strtoupper($moduloIndividual['nombreCiclo']) ?>
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
                                    $listaNombres .= strtoupper($np);
                                }
                                echo $listaNombres;
                                ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?= $moduloIndividual['horasMaximas'] ?> H</td>
                        <td>
                            <div class="botones-accion">
                                <a href="asignarProfesorModulo.php?idModulo=<?= $moduloIndividual['idModulo'] ?>"
                                   class="btn-accion btn-ver">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </a>
                                <a href="modificarModulos.php?idModulo=<?= $moduloIndividual['idModulo'] ?>" 
                                   class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="../../../controladores/admin/modulos/borrar.php" onsubmit="return confirm('Eliminar este módulo?')">
                                    <input type="hidden" name="idModulo" value="<?= $moduloIndividual['idModulo'] ?>">
                                    <input type="submit" class="btn-accion btn-eliminar" value="Borrar">
                                </form>
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




