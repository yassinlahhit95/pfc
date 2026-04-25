<?php
session_start();
if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

$idProfesor = $_SESSION['idProfesor'];
$tituloDelPagina = "Resultados Finales - Portal Profesores";
$seccionActual = 'resultados_finales';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/calificaciones.php";
require_once "../../../modelos/retos.php";
require_once "../../../modelos/ciclos.php";

// Obtenemos solo los ciclos del profesor
$todos_los_ciclos = listarCiclosPorProfesor($idProfesor);

$id_ciclo_elegido = 0;
if (isset($_GET['idCiclo'])) {
    $id_ciclo_elegido = intval($_GET['idCiclo']);
    
    // Verificar que el profesor tiene acceso a este ciclo
    $tieneAcceso = false;
    foreach ($todos_los_ciclos as $c) {
        if ($c['idCiclo'] == $id_ciclo_elegido) {
            $tieneAcceso = true;
            break;
        }
    }
    if (!$tieneAcceso) $id_ciclo_elegido = 0;
}

$datos_finales = array();

if ($id_ciclo_elegido != 0) {
    // 1. Obtener todos los estudiantes del ciclo
    $estudiantes_lista = listarEstudiantesPorCiclo($id_ciclo_elegido);
    
    // 2. Obtener todos los módulos del ciclo
    $lista_modulos = obtenerModulosPorCiclo($id_ciclo_elegido);
    
    foreach ($estudiantes_lista as $est) {
        $id_est = $est['idEstudiante'];
        $nombre_est = $est['nombreEstudiante'];
        
        $suma_total_modulos = 0;
        $contador_total_notas_modulos = 0;
        $suma_total_retos = 0;
        $contador_modulos_con_reto = 0;
        $hayModuloSuspenso = false;

        foreach ($lista_modulos as $mod_item) {
            $id_mod = $mod_item['idModulo'];
            $notas_mod = obtenerNotasModulo($id_est, $id_mod);
            
            $notasDeEsteModulo = [];
            if (isset($notas_mod['nota_1ev']) && $notas_mod['nota_1ev'] > 0) $notasDeEsteModulo[] = $notas_mod['nota_1ev'];
            if (isset($notas_mod['nota_1final']) && $notas_mod['nota_1final'] > 0) $notasDeEsteModulo[] = $notas_mod['nota_1final'];
            if (isset($notas_mod['nota_2ev']) && $notas_mod['nota_2ev'] > 0) $notasDeEsteModulo[] = $notas_mod['nota_2ev'];
            if (isset($notas_mod['nota_2final']) && $notas_mod['nota_2final'] > 0) $notasDeEsteModulo[] = $notas_mod['nota_2final'];
            
            if (count($notasDeEsteModulo) > 0) {
                $mediaMod = array_sum($notasDeEsteModulo) / count($notasDeEsteModulo);
                $suma_total_modulos += $mediaMod;
                $contador_total_notas_modulos++;
                if ($mediaMod < 5) $hayModuloSuspenso = true;
            }
            
            $medias_retos_del_modulo = listarCalificacionesRetoPorModulo($id_mod);
            if (isset($medias_retos_del_modulo[$id_est])) {
                $suma_total_retos += $medias_retos_del_modulo[$id_est];
                $contador_modulos_con_reto++;
            }
        }
        
        $media_global_modulo = $contador_total_notas_modulos > 0 ? $suma_total_modulos / $contador_total_notas_modulos : 0;
        $media_global_reto = $contador_modulos_con_reto > 0 ? $suma_total_retos / $contador_modulos_con_reto : 0;
        $nota_final = ($media_global_modulo * 0.75) + ($media_global_reto * 0.25);
        
        $estado = "Suspenso";
        if ($contador_total_notas_modulos == 0) {
            $estado = "Pendiente";
        } else if ($nota_final >= 5.00 && !$hayModuloSuspenso) {
            $estado = "Aprobado";
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
    <h1>Resultados Finales de mis Alumnos</h1>
    <p class="subtitulo">Resumen global (75% Módulos / 25% Retos)</p>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro separacion-grande">
        <form method="GET" action="" class="flexible-rellenar disposicion-flexible alinear-centro">
            <div class="campo-formulario flexible-rellenar">
                <label>Seleccione uno de sus Ciclos:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($todos_los_ciclos as $cic) { ?>
                        <option value="<?php echo $cic['idCiclo']; ?>" <?php if($id_ciclo_elegido == $cic['idCiclo']) echo "selected"; ?>>
                            <?php echo $cic['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <?php if ($id_ciclo_elegido != 0 && !empty($datos_finales)) { ?>
            <form action="/pfc/controladores/admin/academico/enviarNotasMasivo.php" method="POST" onsubmit="return confirm('¿Enviar resultados por email a todos los alumnos de este ciclo?')">
                <input type="hidden" name="idCiclo" value="<?php echo $id_ciclo_elegido; ?>">
                <button type="submit" class="boton-primario" style="background-color: #3498db;">
                    <i class="fas fa-paper-plane"></i> Notificar a Todos
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
                    <?php if (empty($datos_finales)) { ?>
                        <tr><td colspan="5" class="sin-datos">No hay estudiantes en este ciclo</td></tr>
                    <?php } else { ?>
                        <?php foreach ($datos_finales as $fila) { 
                            $clase_estado = "texto-rojo";
                            if ($fila['estado'] == "Aprobado") { $clase_estado = "texto-verde"; }
                            if ($fila['estado'] == "Pendiente") { $clase_estado = "texto-gris"; }
                        ?>
                        <tr>
                            <td><strong><?php echo $fila['nombre']; ?></strong></td>
                            <td><?php echo $fila['media_modulo']; ?></td>
                            <td><?php echo $fila['media_reto']; ?></td>
                            <td class="texto-negrita"><?php echo $fila['nota_final']; ?></td>
                            <td class="<?php echo $clase_estado; ?> texto-negrita">
                                <?php echo $fila['estado']; ?>
                                <?php if($fila['alert']) echo " <small title='Tiene módulos suspensos'>(!)</small>"; ?>
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
