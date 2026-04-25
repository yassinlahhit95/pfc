<?php
session_start();
$titulo_pagina = "Resultados Finales - Super Admin";
$seccion = 'resultados_modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/calificaciones.php";
require_once "../../../modelos/retos.php";
require_once "../../../modelos/ciclos.php";

$id_ciclo_elegido = 0;
if (isset($_GET['idCiclo'])) {
    $id_ciclo_elegido = $_GET['idCiclo'];
}

$todos_los_ciclos = listarTodosLosCiclos();
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

        foreach ($lista_modulos as $mod_item) {
            $id_mod = $mod_item['idModulo'];
            
            // --- CÁLCULO MEDIA MÓDULO (Notas de los 4 slots) ---
            $notas_mod = obtenerNotasModulo($id_est, $id_mod);
            $campos_notas = array('nota_1ev', 'nota_1final', 'nota_2ev', 'nota_2final');
            
            foreach ($campos_notas as $campo) {
                if (isset($notas_mod[$campo])) {
                    if ($notas_mod[$campo] > 0) {
                        $suma_total_modulos = $suma_total_modulos + $notas_mod[$campo];
                        $contador_total_notas_modulos = $contador_total_notas_modulos + 1;
                    }
                }
            }
            
            // --- CÁLCULO MEDIA RETOS ---
            $medias_retos_del_modulo = listarCalificacionesRetoPorModulo($id_mod);
            if (isset($medias_retos_del_modulo[$id_est])) {
                $suma_total_retos = $suma_total_retos + $medias_retos_del_modulo[$id_est];
                $contador_modulos_con_reto = $contador_modulos_con_reto + 1;
            }
        }
        
        // Media Global del Módulo (de todas las notas de todos los módulos del ciclo)
        $media_global_modulo = 0;
        if ($contador_total_notas_modulos > 0) {
            $media_global_modulo = $suma_total_modulos / $contador_total_notas_modulos;
        }
        
        // Media Global de Retos (media de las medias de retos por módulo)
        $media_global_reto = 0;
        if ($contador_modulos_con_reto > 0) {
            $media_global_reto = $suma_total_retos / $contador_modulos_con_reto;
        }
        
        // Cálculo final: 75% Global Modulos + 25% Global Retos
        $nota_final = ($media_global_modulo * 0.75) + ($media_global_reto * 0.25);
        
        // Estado: Aprobado, Suspenso o Pendiente
        $estado = "Suspenso";
        if ($contador_total_notas_modulos == 0 || $contador_modulos_con_reto == 0) {
            $estado = "Pendiente";
        } else {
            if ($nota_final >= 5.00) {
                $estado = "Aprobado";
            }
        }
        
        $datos_finales[] = array(
            'nombre' => $nombre_est,
            'media_modulo' => round($media_global_modulo, 2),
            'media_reto' => round($media_global_reto, 2),
            'nota_final' => round($nota_final, 2),
            'estado' => $estado
        );
    }
}
?>

<div class="encabezado-pagina">
    <h1>Resultados Finales por Estudiante</h1>
    <p class="subtitulo">Promedio global de todos los módulos del ciclo seleccionado</p>
</div>

<?php 
$exito = ""; if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }
$error = ""; if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }
unset($_SESSION['exito'], $_SESSION['error']);
?>
<?php if ($exito != "") { ?> <div class="mensaje-exito"><?php echo $exito; ?></div> <?php } ?>
<?php if ($error != "") { ?> <div class="mensaje-error"><?php echo $error; ?></div> <?php } ?>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro separacion-grande">
        <form method="GET" action="resultadosFinales.php" class="flexible-rellenar disposicion-flexible alinear-centro">
            <div class="campo-formulario flexible-rellenar">
                <label>Seleccione un Ciclo formativo para ver el resumen:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($todos_los_ciclos as $cic) { ?>
                        <option value="<?php echo $cic['idCiclo']; ?>" <?php if($id_ciclo_elegido == $cic['idCiclo']) { echo "selected"; } ?>>
                            <?php echo $cic['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <?php if ($id_ciclo_elegido != 0 && !empty($datos_finales)) { ?>
            <form action="/pfc/controladores/admin/academico/enviarNotasMasivo.php" method="POST" onsubmit="return confirm('¿Está seguro de enviar las notas por email a todos los estudiantes de este ciclo?')">
                <input type="hidden" name="idCiclo" value="<?php echo $id_ciclo_elegido; ?>">
                <button type="submit" class="boton-secundario" style="background-color: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i> Enviar Resultados por Email a Todos
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
                        <th>Media Global Módulos (75%)</th>
                        <th>Media Global Retos (25%)</th>
                        <th>Nota Final Ciclo</th>
                        <th>Estado Final</th>
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
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div class="margen-arriba tarjeta-gris-suave">
            <p><i class="fas fa-info-circle"></i> <strong>Cálculo Global:</strong> Se promedian todas las calificaciones de todos los módulos del ciclo (75%) y todos los retos asociados (25%).</p>
            <p><i class="fas fa-info-circle"></i> <strong>Estados:</strong> <span class="texto-verde">Aprobado (>= 5.0)</span>, <span class="texto-rojo">Suspenso (< 5.0)</span>, <span class="texto-gris">Pendiente (Sin notas)</span>.</p>
        </div>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
