<?php
session_start();
if (isset($_SESSION['idProfesor']) == false || $_SESSION['idProfesor'] == "") {
    header("Location: /pfc/index.php");
    exit;
}

$idProfesor = $_SESSION['idProfesor'];
$tituloDelPagina = strtoupper("Resultados Finales - Portal Profesores");
$seccionActual = 'resultados_finales';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/calificaciones.php";
require_once "../../../modelos/retos.php";
require_once "../../../modelos/ciclos.php";

// Obtenemos solo los ciclos del profesor
$todos_los_ciclos = obtenerCiclosDeProfesor($idProfesor);

$id_ciclo_elegido = 0;
if (isset($_GET['idCiclo']) && $_GET['idCiclo'] != "") {
    $id_ciclo_elegido = intval($_GET['idCiclo']);
    
    // Verificar que el profesor tiene acceso a este ciclo
    $tieneAcceso = false;
    $cantidad_ciclos = count($todos_los_ciclos);
    for ($indiceCiclo = 0; $indiceCiclo < $cantidad_ciclos; $indiceCiclo = $indiceCiclo + 1) {
        $cicloIndividual = $todos_los_ciclos[$indiceCiclo];
        if ($cicloIndividual['idCiclo'] == $id_ciclo_elegido) {
            $tieneAcceso = true;
        }
    }
    
    if ($tieneAcceso == false) {
        $id_ciclo_elegido = 0;
    }
}

$datos_finales = array();

if ($id_ciclo_elegido != 0) {
    // 1. Obtener todos los estudiantes del ciclo
    $estudiantes_lista = listarEstudiantesPorCiclo($id_ciclo_elegido);
    
    // 2. Obtener todos los módulos del ciclo
    $lista_modulos = obtenerModulosPorCiclo($id_ciclo_elegido);
    
    foreach ($estudiantes_lista as $estudianteIndividual) {
        $id_est = $estudianteIndividual['idEstudiante'];
        $nombre_est = strtoupper($estudianteIndividual['nombreEstudiante']);
        
        $suma_total_modulos = 0;
        $contador_total_notas_modulos = 0;
        $suma_total_retos = 0;
        $contador_modulos_con_reto = 0;
        $hayModuloSuspenso = false;

        foreach ($lista_modulos as $moduloItem) {
            $id_mod = $moduloItem['idModulo'];
            $notas_mod = obtenerNotasModulo($id_est, $id_mod);
            
            $notasDeEsteModulo = [];
            if (isset($notas_mod['nota_1ev']) && is_numeric($notas_mod['nota_1ev']) && $notas_mod['nota_1ev'] > 0) { $notasDeEsteModulo[] = $notas_mod['nota_1ev']; }
            if (isset($notas_mod['nota_1final']) && is_numeric($notas_mod['nota_1final']) && $notas_mod['nota_1final'] > 0) { $notasDeEsteModulo[] = $notas_mod['nota_1final']; }
            if (isset($notas_mod['nota_2ev']) && is_numeric($notas_mod['nota_2ev']) && $notas_mod['nota_2ev'] > 0) { $notasDeEsteModulo[] = $notas_mod['nota_2ev']; }
            if (isset($notas_mod['nota_2final']) && is_numeric($notas_mod['nota_2final']) && $notas_mod['nota_2final'] > 0) { $notasDeEsteModulo[] = $notas_mod['nota_2final']; }
            
            if (count($notasDeEsteModulo) > 0) {
                $mediaMod = array_sum($notasDeEsteModulo) / count($notasDeEsteModulo);
                $suma_total_modulos = $suma_total_modulos + $mediaMod;
                $contador_total_notas_modulos = $contador_total_notas_modulos + 1;
                if ($mediaMod < 5) {
                    $hayModuloSuspenso = true;
                }
            }
            
            $medias_retos_del_modulo = listarCalificacionesRetoPorModulo($id_mod);
            if (isset($medias_retos_del_modulo[$id_est])) {
                $suma_total_retos = $suma_total_retos + $medias_retos_del_modulo[$id_est];
                $contador_modulos_con_reto = $contador_modulos_con_reto + 1;
            }
        }
        
        $media_global_modulo = 0;
        if ($contador_total_notas_modulos > 0) {
            $media_global_modulo = $suma_total_modulos / $contador_total_notas_modulos;
        }

        $media_global_reto = 0;
        if ($contador_modulos_con_reto > 0) {
            $media_global_reto = $suma_total_retos / $contador_modulos_con_reto;
        }

        $nota_final = ($media_global_modulo * 0.75) + ($media_global_reto * 0.25);
        
        $estado = "SUSPENSO";
        if ($contador_total_notas_modulos == 0) {
            $estado = "PENDIENTE";
        } else {
            if ($nota_final >= 5.00 && $hayModuloSuspenso == false) {
                $estado = "APROBADO";
            }
        }
        
        $datos_finales[] = array(
            'nombre' => $nombre_est,
            'media_modulo' => round($media_global_modulo, 2),
            'media_reto' => round($media_global_reto, 2),
            'nota_final' => round($nota_final, 2),
            'estado' => $estado,
            'alert' => $hayModuloSuspenso
        );
    }
}
?>

<div class="encabezado-pagina">
    <h1>RESULTADOS FINALES DE MIS ALUMNOS</h1>
    <p class="subtitulo">Resumen global (75% Módulos / 25% Retos)</p>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro separacion-grande">
        <form method="GET" action="" class="flexible-rellenar disposicion-flexible alinear-centro">
            <div class="campo-formulario flexible-rellenar">
                <label>Seleccione uno de sus Ciclos:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($todos_los_ciclos as $cicloItem) { ?>
                        <option value="<?php echo $cicloItem['idCiclo']; ?>" <?php if($id_ciclo_elegido == $cicloItem['idCiclo']) echo "selected"; ?>>
                            <?php echo strtoupper($cicloItem['nombreCiclo']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <?php if ($id_ciclo_elegido != 0 && $datos_finales) { ?>
            <form action="/pfc/controladores/admin/academico/enviarNotasMasivo.php" method="POST" onsubmit="return confirm('¿Enviar resultados por email a todos los alumnos de este ciclo?')">
                <input type="hidden" name="idCiclo" value="<?php echo $id_ciclo_elegido; ?>">
                <button type="submit" class="boton-primario">
                    <i class="fas fa-paper-plane"></i> NOTIFICAR A TODOS
                </button>
            </form>
        <?php } ?>
    </div>
</div>

<?php if ($id_ciclo_elegido != 0) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Media Módulos (75%)</th>
                        <th>Media Retos (25%)</th>
                        <th>Nota Final</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($datos_finales == false || count($datos_finales) == 0) { ?>
                        <tr><td colspan="5" class="sin-datos">No hay estudiantes en este ciclo</td></tr>
                    <?php } else { ?>
                        <?php foreach ($datos_finales as $filaIndividual) { 
                            $clase_estado = "texto-rojo";
                            if ($filaIndividual['estado'] == "APROBADO") { $clase_estado = "texto-verde"; }
                            if ($filaIndividual['estado'] == "PENDIENTE") { $clase_estado = "texto-gris"; }
                        ?>
                        <tr>
                            <td><strong><?php echo $filaIndividual['nombre']; ?></strong></td>
                            <td><?php echo $filaIndividual['media_modulo']; ?></td>
                            <td><?php echo $filaIndividual['media_reto']; ?></td>
                            <td class="texto-negrita"><?php echo $filaIndividual['nota_final']; ?></td>
                            <td class="<?php echo $clase_estado; ?> texto-negrita">
                                <?php echo $filaIndividual['estado']; ?>
                                <?php if($filaIndividual['alert'] == true) { echo " <small title='Tiene módulos suspensos'>(!)</small>"; } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
