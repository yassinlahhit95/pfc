<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeEstudiantesActuales = listarEstudiantes();
$listaDeCiclosParaFiltro    = listarTodosLosCiclos();
$listaNiveles               = listarNiveles();

$titulo_pagina = 'AULAPRO | ESTUDIANTES';
$seccion = 'estudiantes';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>LISTADO DE ESTUDIANTES</h1>
    <a href="agregarEstudiantes.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO ESTUDIANTE
    </a>
</div>

<div class="panel margen-abajo">
    <div class="caja caja-libre espacio-grande">
        <div class="campo relleno">
            <label for="selectFiltroNivel">FILTRAR POR NIVEL:</label>
            <select id="selectFiltroNivel" onchange="aplicarFiltrosEstudiantes()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $n): ?>
                    <option value="<?= (int)$n['idNivel'] ?>"><?= Security::escapeHtml($n['nombreNivel']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="selectFiltroCiclo">FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo" onchange="aplicarFiltrosEstudiantes()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $c): ?>
                    <option value="<?= strtoupper(Security::escapeHtml($c['nombreCiclo'])) ?>">
                        <?= strtoupper(Security::escapeHtml($c['nombreCiclo'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="selectFiltroAnio">FILTRAR POR AÑO:</label>
            <select id="selectFiltroAnio" onchange="aplicarFiltrosEstudiantes()">
                <option value="">-- Todos los Años --</option>
                <option value="1º">1º Año</option>
                <option value="2º">2º Año</option>
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
                    <th>AÑO</th>
                    <th>CICLO ASIGNADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeEstudiantesActuales)): ?>
                    <tr><td colspan="6" class="vacio">No hay estudiantes registrados en el sistema.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaDeEstudiantesActuales as $e): ?>
                    <tr class="fila-nivel-<?= (int)($e['idNivel'] ?? 0) ?>">
                        <td><?= (int)$e['idEstudiante'] ?></td>
                        <td>
                            <span class="texto-estado <?= ($e['idNivel'] ?? 0) == 1 ? 'azul' : 'verde' ?>">
                                <?= ($e['idNivel'] ?? 0) == 1 ? 'Grado Medio' : 'Grado Superior' ?>
                            </span>
                        </td>
                        <td><b><?= mb_strtoupper(Security::escapeHtml($e['nombreEstudiante']), 'UTF-8') ?></b></td>
                        <td><?= Security::escapeHtml($e['emailEstudiante']) ?></td>
                        <td><?= Security::escapeHtml($e['anioEstudio'] ?? '') ?></td>
                        <td><?= mb_strtoupper(Security::escapeHtml($e['nombreCiclo'] ?? '—'), 'UTF-8') ?></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="verDetallesEstudiantes.php?id=<?= (int)$e['idEstudiante'] ?>" class="boton-secundario boton-pequeno" title="Ver detalles">
                                    <i class="fas fa-id-card"></i>
                                </a>
                                <a href="modificarEstudiantes.php?id=<?= (int)$e['idEstudiante'] ?>" class="boton-primario boton-pequeno" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
function aplicarFiltrosEstudiantes() {
    var idNivel    = $('#selectFiltroNivel').val();
    var textoCiclo = $('#selectFiltroCiclo').val().toLowerCase();
    var textoAnio  = $('#selectFiltroAnio').val().toLowerCase();

    $('#tablaEstudiantes tbody tr').each(function () {
        var $f = $(this);
        var pasaNivel = idNivel === '' || $f.hasClass('fila-nivel-' + idNivel);
        var pasaCiclo = textoCiclo === '' || $f.find('td').eq(5).text().toLowerCase().indexOf(textoCiclo) !== -1;
        var pasaAnio  = textoAnio === '' || $f.find('td').eq(4).text().toLowerCase().indexOf(textoAnio) !== -1;
        $f.toggleClass('fila-filtro-oculta', !(pasaNivel && pasaCiclo && pasaAnio));
    });

    if (typeof resetearPaginacion === 'function') resetearPaginacion('tablaEstudiantes');
}

iniciarPaginacion('tablaEstudiantes', 15);
</script>
