<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaDeTodosLosCiclos = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$idNivelFiltro = (int)($_GET['idNivel'] ?? 0);
$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($listaDeTodosLosCiclos, fn($ciclo) => (int)$ciclo['idNivel'] === $idNivelFiltro))
    : $listaDeTodosLosCiclos;

$idCicloElegidoParaVer = (int)($_GET['idCiclo'] ?? 0);

if ($idNivelFiltro && $idCicloElegidoParaVer) {
    if (!in_array($idCicloElegidoParaVer, array_column($ciclosFiltrados, 'idCiclo'))) {
        $idCicloElegidoParaVer = 0;
    }
}

$listaDeDatosFinalesAMostrar = [];
if (!empty($idCicloElegidoParaVer)) {
    $listaDeDatosFinalesAMostrar = listarResultadosFinalesCiclo($idCicloElegidoParaVer);
}

// El "75% módulos / 25% retos" solo es cierto con la fórmula heredada; con
// el motor configurable (feature_academico_config) los pesos los define
// cada centro en Configuración Académica, así que el texto fijo sería falso.
$motorConfigurableActivo = motorAcademicoActivo();

$titulo_pagina = "Resultados Finales";
$seccion = 'resultados_modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<style>
.filtros-resultados {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px;
}
.filtros-resultados > form.relleno {
    flex: 1 1 340px;
    min-width: 0;
}
.filtros-resultados > form.relleno .caja.alinear-centro {
    flex-wrap: wrap;
}
.filtros-resultados > form.btn-accion-resultados {
    flex: 0 0 auto;
}
@media (max-width: 640px) {
    .filtros-resultados { flex-direction: column; align-items: stretch; }
    .filtros-resultados > form.relleno { flex: 1 1 100%; }
    .filtros-resultados > form.btn-accion-resultados { width: 100%; }
    .filtros-resultados .boton-primario { width: 100%; justify-content: center; }
    .tabla-datos th, .tabla-datos td { padding: 10px 8px; font-size: 12px; }
}
</style>

<div class="cabecera">
    <h1>Resultados Finales por Estudiante</h1>
    <p class="subtitulo-encabezado"><?= $motorConfigurableActivo
        ? 'Promedio global del ciclo según el motor de calificación configurado'
        : 'Promedio global del ciclo (75% Módulos / 25% Retos)' ?></p>
</div>


<div class="panel">
    <div class="filtros-resultados">
        <form method="GET" action="resultadosFinales.php" class="relleno caja alinear-centro espacio-grande">
            <div class="campo relleno">
                <label>Filtrar por Nivel:</label>
                <select name="idNivel" onchange="this.form.submit()">
                    <option value="">-- Todos los Niveles --</option>
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?= (int)$nivel['idNivel'] ?>" <?= ((int)$nivel['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($nivel['nombreNivel']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="campo relleno">
                <label>Seleccione un Ciclo formativo para ver el resumen:</label>
                <select name="idCiclo" id="selectCicloFinal" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($ciclosFiltrados as $cicloItem) { ?>
                        <option value="<?= (int)$cicloItem['idCiclo'] ?>" <?= ($idCicloElegidoParaVer == $cicloItem['idCiclo']) ? 'selected' : '' ?>>
                            [<?= Security::escapeHtml($cicloItem['nombreNivel']) ?>] <?= mb_strtoupper(Security::escapeHtml($cicloItem['nombreCiclo']), 'UTF-8') ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="campo relleno">
                <label>Filtrar por Curso:</label>
                <select id="filtroCursoEstudiante" onchange="filtrarResultadosPorCurso()">
                    <option value="">-- Todos los Cursos --</option>
                    <option value="1º">1º Año</option>
                    <option value="2º">2º Año</option>
                </select>
            </div>
        </form>

        <?php if (!empty($idCicloElegidoParaVer) && !empty($listaDeDatosFinalesAMostrar)) { ?>
            <form action="../../../controladores/admin/academico/enviarNotasMasivo.php" method="POST" class="btn-accion-resultados">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idCiclo" value="<?= $idCicloElegidoParaVer ?>">
                <input type="submit" class="boton-primario" value="ENVIAR RESULTADOS POR EMAIL A TODOS">
            </form>
            <form action="../../../controladores/admin/academico/exportarCalificaciones.php" method="POST" class="btn-accion-resultados">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idCiclo" value="<?= $idCicloElegidoParaVer ?>">
                <button type="submit" class="boton-primario" style="background:var(--verde);">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </button>
            </form>
        <?php } ?>
    </div>
</div>


<?php if (!empty($idCicloElegidoParaVer)) { ?>
    <div class="panel margen-arriba">
        <div class="contenedor-tabla">
            <table class="tabla-datos" id="tabla-resultados-finales">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Año</th>
                        <th>Media Global Módulos<?= $motorConfigurableActivo ? '' : ' (75%)' ?></th>
                        <th>Media Global Retos<?= $motorConfigurableActivo ? '' : ' (25%)' ?></th>
                        <th>Nota TFG</th>
                        <th>Nota Final Ciclo</th>
                        <th>Estado Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaDeDatosFinalesAMostrar)) { ?>
                        <tr><td colspan="7" class="vacio">No hay estudiantes en este ciclo</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaDeDatosFinalesAMostrar as $fila) { 
                            $claseDelColor = "texto-rojo";
                            if ($fila['estado_global'] == "APROBADO") { $claseDelColor = "texto-verde"; }
                            if ($fila['estado_global'] == "PENDIENTE") { $claseDelColor = "texto-gris"; }
                        ?>
                        <tr class="fila-curso" data-curso="<?= Security::escapeHtml($fila['anioEstudio'] ?? '') ?>">
                            <td><?= Security::escapeHtml($fila['nombreEstudiante']) ?></td>
                            <td><?= Security::escapeHtml($fila['anioEstudio'] ?? '-') ?></td>
                            <td><?= Security::escapeHtml($fila['media_modulos']) ?></td>
                            <td><?= Security::escapeHtml($fila['media_retos']) ?></td>
                            <td class="color-primario texto-negrita"><?= Security::escapeHtml($fila['nota_tfg'] ?? ' ') ?></td>
                            <td class="texto-negrita"><?= Security::escapeHtml($fila['promedio_global']) ?></td>
                            <td class="<?= $claseDelColor ?> texto-negrita">
                                <?= Security::escapeHtml($fila['estado_global']) ?>
                                <?php if($fila['tiene_suspensos'] == true && strpos($fila['estado_global'], "PENDIENTE") === false) { 
                                    echo " <span title='Tiene módulos suspensos'>(!)</span>"; 
                                } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div class="tarjeta-alerta-info">
            <p><b>Cálculo Global:</b> <?= $motorConfigurableActivo
                ? 'Se aplica el motor de calificación configurado en Configuración Académica (tipos de evaluación y pesos propios del centro).'
                : 'Se promedian todas las calificaciones de todos los módulos (75%) y todos los retos (25%).' ?></p>
            <p><b>Estados:</b> <span class="texto-verde">APROBADO (>= 5.0 y sin módulos pendientes)</span>, <span class="texto-rojo">SUSPENSO (< 5.0 o con pendientes)</span>, <span class="texto-gris">PENDIENTE</span>.</p>
        </div>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
<script>
function filtrarResultadosPorCurso() {
    var curso = document.getElementById('filtroCursoEstudiante').value;
    var filas = document.querySelectorAll('.fila-curso');
    filas.forEach(function(fila) {
        var optCurso = fila.getAttribute('data-curso');
        if (curso === '' || optCurso === curso) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}
</script>
