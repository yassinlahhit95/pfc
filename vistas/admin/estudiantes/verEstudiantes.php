<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeEstudiantesActuales = listarEstudiantes();

$titulo_pagina = "AULAPRO | LISTADO DE ESTUDIANTES";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

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

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

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
            <label for="selectFiltroNivel">FILTRAR POR NIVEL:</label>
            <select id="selectFiltroNivel" onchange="aplicarFiltrosEstudiantes()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivelFiltro) { ?>
                    <option value="<?= Security::escapeHtml($nivelFiltro['idNivel']) ?>">
                        <?= Security::escapeHtml($nivelFiltro['nombreNivel']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="selectFiltroCiclo">FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo" onchange="aplicarFiltrosEstudiantes()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= strtoupper(Security::escapeHtml($cicloFiltro['nombreCiclo'])) ?>">
                        <?= strtoupper(Security::escapeHtml($cicloFiltro['nombreCiclo'])) ?>
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
                        <td><?= Security::escapeHtml($estudianteIndividual['idEstudiante']) ?></td>
                        <td>
                            <span class="texto-estado <?= $estudianteIndividual['idNivel'] == 1 ? 'azul' : 'verde' ?>"><?= $estudianteIndividual['idNivel'] == 1 ? 'Grado Medio' : 'Grado Superior' ?></span>
                            <span class="texto-estado gris"><?= Security::escapeHtml($estudianteIndividual['curso']) ?></span>
                        </td>
                        <td><b><?= mb_strtoupper(Security::escapeHtml($estudianteIndividual['nombreEstudiante']), 'UTF-8') ?></b></td>
                        <td><?= Security::escapeHtml($estudianteIndividual['emailEstudiante']) ?></td>
                        <td><?= strtoupper(Security::escapeHtml($estudianteIndividual['nombreCiclo'])) ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="verDetallesEstudiantes.php?idEstudiante=<?= Security::escapeHtml($estudianteIndividual['idEstudiante']) ?>"><i class="fas fa-id-card"></i> Ver detalles</a>
                                    <a class="recurso-menu-item" href="modificarEstudiantes.php?idEstudiante=<?= Security::escapeHtml($estudianteIndividual['idEstudiante']) ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarEstudiante.php?id=<?= Security::escapeHtml($estudianteIndividual['idEstudiante']) ?>" onclick="return confirm('¿Eliminar este estudiante?')"><i class="fas fa-trash"></i> Eliminar</a>
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
function aplicarFiltrosEstudiantes() {
    var idNivel = $('#selectFiltroNivel').val();
    var textoCiclo = $('#selectFiltroCiclo').val().toLowerCase();

    $('#tablaEstudiantes tbody tr').each(function() {
        var $fila = $(this);
        var pasaNivel = idNivel === '' || $fila.hasClass('fila-nivel-' + idNivel);
        var textoCelda = $fila.find('td').eq(4).text().toLowerCase();
        var pasaCiclo = textoCiclo === '' || textoCelda.indexOf(textoCiclo) !== -1;
        if (pasaNivel && pasaCiclo) {
            $fila.removeClass('fila-filtro-oculta');
        } else {
            $fila.addClass('fila-filtro-oculta');
        }
    });

    if (typeof resetearPaginacion === 'function' && _paginaciones && _paginaciones['tablaEstudiantes']) {
        resetearPaginacion('tablaEstudiantes');
    }
}

iniciarPaginacion('tablaEstudiantes', 15);
</script>

