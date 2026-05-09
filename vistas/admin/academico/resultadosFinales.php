<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "RESULTADOS FINALES - ADMIN";
$seccion = 'resultados_modulos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idCicloElegidoParaVer = 0;
$idCicloElegidoParaVer = (int)($_GET['idCiclo'] ?? 0);

$listaDeTodosLosCiclos = listarTodosLosCiclos();
$listaDeDatosFinalesAMostrar = [];

if (!empty($idCicloElegidoParaVer)) {
    $listaDeDatosFinalesAMostrar = obtenerResultadosFinalesCiclo($idCicloElegidoParaVer);
}

$mensajeExito = $_SESSION['exito'] ?? '';
$mensajeError = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <h1>RESULTADOS FINALES POR ESTUDIANTE</h1>
    <p class="subtitulo">Promedio global del ciclo (75% Módulos / 25% Retos)</p>
</div>

<?php if (!empty($mensajeExito)) { ?> <div class="mensaje-exito"><?= $mensajeExito ?></div> <?php } ?>
<?php if (!empty($mensajeError)) { ?> <div class="mensaje-error"><?= $mensajeError ?></div> <?php } ?>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro separacion-grande">
        <form method="GET" action="resultadosFinales.php" class="flexible-rellenar disposicion-flexible alinear-centro">
            <div class="campo-formulario flexible-rellenar">
                <label>Seleccione un Ciclo formativo para ver el resumen:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($listaDeTodosLosCiclos as $cicloItem) { ?>
                        <option value="<?= $cicloItem['idCiclo'] ?>" <?php if($idCicloElegidoParaVer == $cicloItem['idCiclo']) { echo "selected"; } ?>>
                            <?= strtoupper($cicloItem['nombreCiclo']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <?php if (!empty($idCicloElegidoParaVer) && !empty($listaDeDatosFinalesAMostrar)) { ?>
            <form action="../../../controladores/admin/academico/enviarNotasMasivo.php" method="POST" onsubmit="return confirm('Â¿EstÃ¡ seguro de enviar las notas por email a todos los estudiantes de este ciclo?')">
                <input type="hidden" name="idCiclo" value="<?= $idCicloElegidoParaVer ?>">
                <button type="submit" class="boton-primario">
                    <i class="fas fa-paper-plane"></i> ENVIAR RESULTADOS POR EMAIL A TODOS
                </button>
            </form>
        <?php } ?>
    </div>
</div>

<?php if (!empty($idCicloElegidoParaVer)) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Media Global MÃ³dulos (75%)</th>
                        <th>Media Global Retos (25%)</th>
                        <th>Nota Final Ciclo</th>
                        <th>Estado Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaDeDatosFinalesAMostrar)) { ?>
                        <tr><td colspan="5" class="sin-datos">No hay estudiantes en este ciclo</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaDeDatosFinalesAMostrar as $fila) { 
                            $claseDelColor = "texto-rojo";
                            if ($fila['estado_global'] == "APROBADO") { $claseDelColor = "texto-verde"; }
                            if (strpos($fila['estado_global'], "PENDIENTE") !== false) { $claseDelColor = "texto-gris"; }
                        ?>
                        <tr>
                            <td><strong><?= $fila['nombreEstudiante'] ?></strong></td>
                            <td><?= $fila['media_modulos'] ?></td>
                            <td><?= $fila['media_retos'] ?></td>
                            <td class="texto-negrita"><?= $fila['promedio_global'] ?></td>
                            <td class="<?= $claseDelColor ?> texto-negrita">
                                <?= $fila['estado_global'] ?>
                                <?php if($fila['tiene_suspensos'] == true && strpos($fila['estado_global'], "PENDIENTE") === false) { 
                                    echo " <small title='Tiene módulos suspensos'>(!)</small>"; 
                                } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div class="tarjeta-alerta-info">
            <p><i class="fas fa-info-circle"></i> <strong>Cálculo Global:</strong> Se promedian todas las calificaciones de todos los módulos (75%) y todos los retos (25%).</p>
            <p><i class="fas fa-info-circle"></i> <strong>Estados:</strong> <span class="texto-verde">APROBADO (>= 5.0 y sin módulos pendientes)</span>, <span class="texto-rojo">SUSPENSO (< 5.0 o con pendientes)</span>, <span class="texto-gris">PENDIENTE</span>.</p>
        </div>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>



