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
                        <td><?= strtoupper(Security::escapeHtml($e['nombreCiclo'] ?? '—')) ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="verDetallesEstudiantes.php?id=<?= (int)$e['idEstudiante'] ?>">
                                        <i class="fas fa-id-card"></i> Ver detalles
                                    </a>
                                    <a class="recurso-menu-item" href="modificarEstudiantes.php?id=<?= (int)$e['idEstudiante'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$e['idEstudiante'] ?>"
                                       data-tipo="Estudiante"
                                       data-nombre="<?= Security::escapeHtml($e['nombreEstudiante']) ?>"
                                       data-extra="<?= Security::escapeHtml($e['abreviaturaCiclo'] ?? $e['nombreCiclo'] ?? '') ?>"
                                       data-url="/controladores/admin/estudiantes/borrar.php"
                                       data-campo="idEstudiante">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
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

    $('#tablaEstudiantes tbody tr').each(function () {
        var $f = $(this);
        var pasaNivel = idNivel === '' || $f.hasClass('fila-nivel-' + idNivel);
        var pasaCiclo = textoCiclo === '' || $f.find('td').eq(4).text().toLowerCase().indexOf(textoCiclo) !== -1;
        $f.toggleClass('fila-filtro-oculta', !(pasaNivel && pasaCiclo));
    });

    if (typeof resetearPaginacion === 'function') resetearPaginacion('tablaEstudiantes');
}

iniciarPaginacion('tablaEstudiantes', 15);
</script>
