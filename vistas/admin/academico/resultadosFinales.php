<?php
session_start();

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
    ? array_values(array_filter($listaDeTodosLosCiclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
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

$titulo_pagina = "AULAPRO | RESULTADOS FINALES";
$seccion = 'resultados_modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>RESULTADOS FINALES POR ESTUDIANTE</h1>
    <p class="subtitulo">Promedio global del ciclo (75% Módulos / 25% Retos)</p>
</div>

<?php if (!empty($exito)) { ?> <div class="mensaje-exito"><?= $exito ?></div> <?php } ?>
<?php if ($errores) { ?><div class="mensaje-error"><?= $errores ?></div><?php } ?>

<div class="panel">
    <div class="caja alinear-centro espacio-grande">
        <form method="GET" action="resultadosFinales.php" class="relleno caja alinear-centro">
            <div class="campo relleno">
                <label>Filtrar por Nivel:</label>
                <select name="idNivel" onchange="this.form.submit()">
                    <option value="">-- Todos los Niveles --</option>
                    <?php foreach ($listaNiveles as $n) { ?>
                        <option value="<?= (int)$n['idNivel'] ?>" <?= ((int)$n['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($n['nombreNivel']) ?>
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
                            [<?= htmlspecialchars($cicloItem['nombreNivel']) ?>] <?= strtoupper(htmlspecialchars($cicloItem['nombreCiclo'])) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <?php if (!empty($idCicloElegidoParaVer) && !empty($listaDeDatosFinalesAMostrar)) { ?>
            <form action="../../../controladores/admin/academico/enviarNotasMasivo.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idCiclo" value="<?= $idCicloElegidoParaVer ?>">
                <input type="submit" class="boton-primario" value="ENVIAR RESULTADOS POR EMAIL A TODOS">
            </form>
        <?php } ?>
    </div>
</div>

<?php if (!empty($idCicloElegidoParaVer)) { ?>
    <div class="panel margen-arriba">
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Media Global Módulos (75%)</th>
                        <th>Media Global Retos (25%)</th>
                        <th>Nota TFG</th>
                        <th>Nota Final Ciclo</th>
                        <th>Estado Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaDeDatosFinalesAMostrar)) { ?>
                        <tr><td colspan="6" class="vacio">No hay estudiantes en este ciclo</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaDeDatosFinalesAMostrar as $fila) { 
                            $claseDelColor = "texto-rojo";
                            if ($fila['estado_global'] == "APROBADO") { $claseDelColor = "texto-verde"; }
                            if ($fila['estado_global'] == "PENDIENTE") { $claseDelColor = "texto-gris"; }
                        ?>
                        <tr>
                            <td><?= $fila['nombreEstudiante'] ?></td>
                            <td><?= $fila['media_modulos'] ?></td>
                            <td><?= $fila['media_retos'] ?></td>
                            <td class="color-primario texto-negrita"><?= $fila['nota_tfg'] ?? ' ' ?></td>
                            <td class="texto-negrita"><?= $fila['promedio_global'] ?></td>
                            <td class="<?= $claseDelColor ?> texto-negrita">
                                <?= $fila['estado_global'] ?>
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
            <p><b>Cálculo Global:</b> Se promedian todas las calificaciones de todos los módulos (75%) y todos los retos (25%).</p>
            <p><b>Estados:</b> <span class="texto-verde">APROBADO (>= 5.0 y sin módulos pendientes)</span>, <span class="texto-rojo">SUSPENSO (< 5.0 o con pendientes)</span>, <span class="texto-gris">PENDIENTE</span>.</p>
        </div>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>

