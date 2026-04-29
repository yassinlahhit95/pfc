<?php
session_start();

// Validación de sesión simple
if (empty($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "RESULTADOS FINALES - SUPER ADMIN";
$seccion = 'resultados_modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/calificaciones.php";
require_once "../../../modelos/ciclos.php";

// Captura del ciclo seleccionado
$idCicloElegidoParaVer = 0;
if (isset($_GET['idCiclo'])) {
    $idCicloElegidoParaVer = (int)$_GET['idCiclo'];
}

$listaDeTodosLosCiclos = listarTodosLosCiclos();
$listaDeDatosFinalesAMostrar = array();

if (!empty($idCicloElegidoParaVer)) {
    // Obtenemos los datos procesados desde el Modelo (MVC)
    $listaDeDatosFinalesAMostrar = obtenerResultadosFinalesCiclo($idCicloElegidoParaVer);
}

// Mensajes de sesión
$mensajeExito = ""; if (isset($_SESSION['exito'])) { $mensajeExito = $_SESSION['exito']; }
$mensajeError = ""; if (isset($_SESSION['error'])) { $mensajeError = $_SESSION['error']; }
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <h1>RESULTADOS FINALES POR ESTUDIANTE</h1>
    <p class="subtitulo">Promedio global del ciclo (75% Módulos / 25% Retos)</p>
</div>

<?php if (!empty($mensajeExito)) { ?> <div class="mensaje-exito"><?php echo $mensajeExito; ?></div> <?php } ?>
<?php if (!empty($mensajeError)) { ?> <div class="mensaje-error"><?php echo $mensajeError; ?></div> <?php } ?>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro separacion-grande">
        <form method="GET" action="resultadosFinales.php" class="flexible-rellenar disposicion-flexible alinear-centro">
            <div class="campo-formulario flexible-rellenar">
                <label>Seleccione un Ciclo formativo para ver el resumen:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($listaDeTodosLosCiclos as $cicloItem) { ?>
                        <option value="<?php echo $cicloItem['idCiclo']; ?>" <?php if($idCicloElegidoParaVer == $cicloItem['idCiclo']) { echo "selected"; } ?>>
                            <?php echo strtoupper($cicloItem['nombreCiclo']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <?php if (!empty($idCicloElegidoParaVer) && !empty($listaDeDatosFinalesAMostrar)) { ?>
            <form action="/pfc/controladores/admin/academico/enviarNotasMasivo.php" method="POST" onsubmit="return confirm('¿Está seguro de enviar las notas por email a todos los estudiantes de este ciclo?')">
                <input type="hidden" name="idCiclo" value="<?php echo $idCicloElegidoParaVer; ?>">
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
                        <th>Media Global Módulos (75%)</th>
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
                            if ($fila['estado_global'] == "PENDIENTE") { $claseDelColor = "texto-gris"; }
                        ?>
                        <tr>
                            <td><strong><?php echo $fila['nombreEstudiante']; ?></strong></td>
                            <td><?php echo $fila['promedio_global']; ?> (Notas)</td> 
                            <td>-</td> <!-- En el modelo calculamos el global, podríamos desglosar si fuera necesario -->
                            <td class="texto-negrita"><?php echo $fila['promedio_global']; ?></td>
                            <td class="<?php echo $claseDelColor; ?> texto-negrita">
                                <?php echo $fila['estado_global']; ?>
                                <?php if($fila['tiene_suspensos'] == true && $fila['estado_global'] != "PENDIENTE") { 
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
