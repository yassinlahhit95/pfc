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

<div class="encabezado-pagina">
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

<div class="tarjeta-blanca margen-abajo">
    <div class="disposicion-flexible envoltura-flexible separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>FILTRAR POR NIVEL:</label>
            <select id="selectFiltroNivel" onchange="filtrarNivelModulos()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivelFiltro) { ?>
                    <option value="<?= $nivelFiltro['idNivel'] ?>">
                        <?= $nivelFiltro['nombreNivel'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo-formulario flexible-rellenar">
            <label>FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo" onchange="filtrarTabla('selectFiltroCiclo', 'tablaModulos')">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= mb_strtoupper($cicloFiltro['nombreCiclo'], 'UTF-8') ?>" data-nivel="<?= $cicloFiltro['idNivel'] ?>">
                        <?= mb_strtoupper($cicloFiltro['nombreCiclo'], 'UTF-8') ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<div class="tarjeta-blanca">
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
                        <td colspan="7" class="sin-datos">No hay módulos registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeModulosActuales as $moduloIndividual) {
                        $nombresProfesores = obtenerNombresProfesoresDeModulo($moduloIndividual['idModulo']);
                    ?>
                    <tr>
                        <td><?= $moduloIndividual['idModulo'] ?></td>
                        <td>
                            <span class="etiqueta-estado <?= $moduloIndividual['idNivel'] == 1 ? 'azul' : 'verde' ?>"><?= $moduloIndividual['idNivel'] == 1 ? 'Grado Medio' : 'Grado Superior' ?></span>
                            <span class="etiqueta-estado gris"><?= $moduloIndividual['curso'] == 1 ? '1º Curso' : '2º Curso' ?></span>
                        </td>
                        <td><strong><?= mb_strtoupper($moduloIndividual['nombreModulo'], 'UTF-8') ?></strong></td>
                        <td>
                            <?php if (!empty($moduloIndividual['abreviaturaCiclo'])) { ?>
                                <strong>[<?= $moduloIndividual['abreviaturaCiclo'] ?>]</strong> 
                            <?php } ?>
                            <?= mb_strtoupper($moduloIndividual['nombreCiclo'], 'UTF-8') ?>
                        </td>
                        <td>
                            <?php if (empty($nombresProfesores)) { ?>
                                <span class="texto-rojo texto-pequeno">
                                    <i class="fas fa-exclamation-triangle"></i> SIN PROFESOR
                                </span>
                            <?php } else { ?>
                                <div class="texto-pequeno">
                                    <?= implode(", ", array_map('strtoupper', $nombresProfesores)) ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?= $moduloIndividual['horasMaximas'] ?> H</td>
                        <td>
                            <div class="botones-accion">
                                <a href="asignarProfesorModulo.php?idModulo=<?= $moduloIndividual['idModulo'] ?>"
                                   class="btn-accion btn-ver" title="Asignar o cambiar profesor">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </a>
                                <a href="modificarModulos.php?idModulo=<?= $moduloIndividual['idModulo'] ?>" 
                                   class="btn-accion btn-editar" title="Editar módulo">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="../../../controladores/admin/modulos/borrar.php" onsubmit="return confirm('¿Eliminar este módulo?')">
                                    <input type="hidden" name="idModulo" value="<?= $moduloIndividual['idModulo'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar" title="Borrar">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
<script>
function filtrarNivelModulos() {
    var idNivel = document.getElementById('selectFiltroNivel').value;
    var selectCiclo = document.getElementById('selectFiltroCiclo');
    var opciones = selectCiclo.querySelectorAll('option');

    opciones.forEach(function(opcion) {
        if (opcion.value === '') {
            opcion.style.display = '';
            return;
        }
        if (idNivel === '' || opcion.getAttribute('data-nivel') === idNivel) {
            opcion.style.display = '';
        } else {
            opcion.style.display = 'none';
        }
    });

    selectCiclo.value = '';
    filtrarTabla('selectFiltroCiclo', 'tablaModulos');
}
</script>




