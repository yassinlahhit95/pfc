<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_retos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$todos_los_retos = listarRetos();
$listaDeCiclosParaFiltro = listarTodosLosCiclos();
$listaNiveles = listarNiveles();
$mapaCicloNivel = [];
foreach ($listaDeCiclosParaFiltro as $cicloFiltro) {
    $mapaCicloNivel[$cicloFiltro['idCiclo']] = $cicloFiltro['idNivel'];
}

$titulo_pagina = "AULAPRO | GESTIÓN DE RETOS";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>RETOS / PROYECTOS</h1>
    <a href="agregarRetos.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO RETO
    </a>
</div>


<div class="panel margen-abajo">
    <div class="caja caja-libre espacio-grande">
        <div class="campo relleno">
            <label>FILTRAR POR NIVEL:</label>
            <select id="selectFiltroNivel" onchange="aplicarFiltrosRetos()">
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
            <select id="selectFiltroCiclo" onchange="aplicarFiltrosRetos()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= $cicloFiltro['idCiclo'] ?>">
                        <?= $cicloFiltro['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaRetos">
            <thead>
                <tr>
                    <th>Nombre del Reto</th>
                    <th>Módulos</th>
                    <th>Materiales</th>
                    <th>Horas</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_retos)) { ?>
                    <tr><td colspan="7" class="vacio">No hay retos configurados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_retos as $reto) {
                        $modulos = listarModulosDeReto($reto['idReto']);
                        $nombresModulos = array_column($modulos, 'nombreModulo');
                        $textoModulos = !empty($nombresModulos) ? implode(", ", $nombresModulos) : "<em>Sin módulos</em>";
                        $idCicloReto = !empty($modulos) ? $modulos[0]['idCiclo'] : '';
                        $archivos = obtenerArchivosReto($reto['idReto']);
                    ?>
                    <tr class="fila-ciclo-<?= $idCicloReto ?> fila-nivel-<?= $mapaCicloNivel[$idCicloReto] ?? '' ?>">
                        <td><b><?= $reto['nombreReto'] ?></b></td>
                        <td><?= $textoModulos ?></td>
                        <td>
                            <?php if (empty($archivos)): ?>
                                <span class="texto-suave small">Sin adjuntos</span>
                            <?php else: ?>
                                <div class="materiales-container">
                                    <a href="../../../controladores/comunes/descargar_zip_reto.php?id=<?= $reto['idReto'] ?>" class="materiales-main-btn">
                                        <i class="fas fa-file-archive"></i> ZIP
                                    </a>
                                    <div class="materiales-dropdown">
                                        <div class="small fw-bold mb-2 px-2 text-muted text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Archivos individuales:</div>
                                        <?php foreach ($archivos as $arch): 
                                            $isPdf = ($arch['tipoArchivo'] === 'pdf');
                                            $icon = $isPdf ? 'fa-file-pdf text-danger' : 'fa-image text-primary';
                                        ?>
                                            <a href="../../../<?= $arch['rutaArchivo'] ?>" target="_blank" class="dropdown-file-item">
                                                <i class="fas <?= $icon ?>"></i>
                                                <span class="text-truncate"><?= Security::escapeHtml($arch['nombreArchivo']) ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                        <hr class="my-2 opacity-10">
                                        <a href="../../../controladores/comunes/descargar_zip_reto.php?id=<?= $reto['idReto'] ?>" class="dropdown-file-item fw-bold">
                                            <i class="fas fa-download"></i> Descargar Todo (.zip)
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= $reto['horasReto'] ?>h</td>
                        <td><?= date('d/m/Y', strtotime($reto['fechaInicio'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($reto['fechaFin'])) ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="modificarRetos.php?idReto=<?= $reto['idReto'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$reto['idReto'] ?>"
                                       data-tipo="Reto"
                                       data-nombre="<?= Security::escapeHtml($reto['nombreReto']) ?>"
                                       data-url="/controladores/admin/retos/borrar.php"
                                       data-campo="idReto"><i class="fas fa-trash"></i> Eliminar</a>
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
iniciarPaginacion('tablaRetos', 15);
function aplicarFiltrosRetos() {
    var idNivel = $('#selectFiltroNivel').val();
    var idCiclo = $('#selectFiltroCiclo').val();

    $('#tablaRetos tbody tr').each(function() {
        var $fila = $(this);
        var pasaNivel = idNivel === '' || $fila.hasClass('fila-nivel-' + idNivel);
        var pasaCiclo = idCiclo === '' || $fila.hasClass('fila-ciclo-' + idCiclo);
        if (pasaNivel && pasaCiclo) {
            $fila.removeClass('fila-filtro-oculta');
        } else {
            $fila.addClass('fila-filtro-oculta');
        }
    });
    resetearPaginacion('tablaRetos');
}
</script>

