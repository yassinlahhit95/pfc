<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "AULAPRO | LISTADO DE ESTUDIANTES";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeEstudiantesActuales = listarEstudiantes();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="cabecera">
    <div>
        <h1>LISTADO DE ESTUDIANTES</h1>
    </div>
    <div class="acciones-pagina">
        <a href="agregarEstudiantes.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO ESTUDIANTE
        </a>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<?php
$listaDeCiclosParaFiltro = listarTodosLosCiclos();
$listaNiveles = listarNiveles();
$mapaCicloNivel = [];
foreach ($listaDeCiclosParaFiltro as $cicloFiltro) {
    $mapaCicloNivel[$cicloFiltro['idCiclo']] = $cicloFiltro['idNivel'];
}
?>
<div class="panel margen-abajo">
    <div class="caja caja-libre espacio-grande">
        <div class="campo relleno">
            <label>FILTRAR POR NIVEL:</label>
            <select id="selectFiltroNivel" onchange="aplicarFiltrosEstudiantes()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivelFiltro) { ?>
                    <option value="<?= $nivelFiltro['idNivel'] ?>">
                        <?= $nivelFiltro['nombreNivel'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label>FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo" onchange="aplicarFiltrosEstudiantes()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= strtoupper($cicloFiltro['nombreCiclo']) ?>">
                        <?= strtoupper($cicloFiltro['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEstudiantes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NIVEL</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>CORREO ELECTRÓNICO</th>
                    <th>CICLO ASIGNADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeEstudiantesActuales)) { ?>
                    <tr>
                        <td colspan="6" class="vacio">No hay estudiantes registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeEstudiantesActuales as $estudianteIndividual) { ?>
                    <tr class="fila-nivel-<?= $mapaCicloNivel[$estudianteIndividual['idCiclo']] ?? '' ?>">
                        <td><?= $estudianteIndividual['idEstudiante'] ?></td>
                        <td>
                            <span class="texto-estado <?= $estudianteIndividual['idNivel'] == 1 ? 'azul' : 'verde' ?>"><?= $estudianteIndividual['idNivel'] == 1 ? 'Grado Medio' : 'Grado Superior' ?></span>
                            <span class="texto-estado gris"><?= $estudianteIndividual['curso'] ?></span>
                        </td>
                        <td><b><?= mb_strtoupper($estudianteIndividual['nombreEstudiante'], 'UTF-8') ?></b></td>
                        <td><?= $estudianteIndividual['emailEstudiante'] ?></td>
                        <td><?= strtoupper($estudianteIndividual['nombreCiclo']) ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="verDetallesEstudiantes.php?idEstudiante=<?= $estudianteIndividual['idEstudiante'] ?>" 
                                   class="btn-accion btn-ver">
                                    <i class="fas fa-id-card"></i>
                                </a>
                                <a href="modificarEstudiantes.php?idEstudiante=<?= $estudianteIndividual['idEstudiante'] ?>" 
                                   class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="../../../controladores/admin/estudiantes/borrar.php" onsubmit="return confirm('¿Está seguro de eliminar a este estudiante?')">
                                    <input type="hidden" name="idEstudiante" value="<?= $estudianteIndividual['idEstudiante'] ?>">
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
<script>
function aplicarFiltrosEstudiantes() {
    var idNivel = document.getElementById('selectFiltroNivel').value;
    var textoCiclo = document.getElementById('selectFiltroCiclo').value.toLowerCase();
    var filas = document.querySelectorAll('#tablaEstudiantes tbody tr');

    filas.forEach(function(fila) {
        var pasaNivel = true;
        var pasaCiclo = true;

        if (idNivel !== '') {
            pasaNivel = fila.classList.contains('fila-nivel-' + idNivel);
        }
        if (textoCiclo !== '') {
            var celdaCiclo = fila.cells[4] ? fila.cells[4].innerText.toLowerCase() : '';
            pasaCiclo = celdaCiclo.includes(textoCiclo);
        }

        if (pasaNivel && pasaCiclo) {
            fila.classList.remove('fila-filtro-oculta');
        } else {
            fila.classList.add('fila-filtro-oculta');
        }
    });

    if (typeof resetearPaginacion === 'function' && _paginaciones && _paginaciones['tablaEstudiantes']) {
        resetearPaginacion('tablaEstudiantes');
    }
}

iniciarPaginacion('tablaEstudiantes', 15);
</script>




