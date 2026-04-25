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
    // 1. Obtener todos los módulos del ciclo seleccionado
    $lista_modulos = obtenerModulosPorCiclo($id_ciclo_elegido);
    
    foreach ($lista_modulos as $mod_item) {
        $id_mod = $mod_item['idModulo'];
        $nombre_mod = $mod_item['nombreModulo'];
        
        // 2. Obtener los estudiantes vinculados al ciclo (usamos la función de calificaciones que ya filtra por ciclo del módulo)
        $estudiantes_lista = listarCalificacionesPorModulo($id_mod);
        
        // 3. Obtener medias de retos para este módulo
        $medias_retos = listarCalificacionesRetoPorModulo($id_mod);
        
        foreach ($estudiantes_lista as $est) {
            $id_est = $est['idEstudiante'];
            $nombre_est = $est['nombreEstudiante'];
            
            // Obtener notas del módulo (4 slots)
            $notas_mod = obtenerNotasModulo($id_est, $id_mod);
            
            // Calcular media del módulo
            $suma_mod = 0; 
            $cont_mod = 0;
            
            if (isset($notas_mod['nota_1ev'])) {
                if ($notas_mod['nota_1ev'] > 0) {
                    $suma_mod = $suma_mod + $notas_mod['nota_1ev'];
                    $cont_mod = $cont_mod + 1;
                }
            }
            if (isset($notas_mod['nota_1final'])) {
                if ($notas_mod['nota_1final'] > 0) {
                    $suma_mod = $suma_mod + $notas_mod['nota_1final'];
                    $cont_mod = $cont_mod + 1;
                }
            }
            if (isset($notas_mod['nota_2ev'])) {
                if ($notas_mod['nota_2ev'] > 0) {
                    $suma_mod = $suma_mod + $notas_mod['nota_2ev'];
                    $cont_mod = $cont_mod + 1;
                }
            }
            if (isset($notas_mod['nota_2final'])) {
                if ($notas_mod['nota_2final'] > 0) {
                    $suma_mod = $suma_mod + $notas_mod['nota_2final'];
                    $cont_mod = $cont_mod + 1;
                }
            }
            
            $media_modulo = 0;
            if ($cont_mod > 0) {
                $media_modulo = $suma_mod / $cont_mod;
            }
            
            // Media del reto
            $media_reto = 0;
            if (isset($medias_retos[$id_est])) {
                $media_reto = $medias_retos[$id_est];
            }
            
            // Cálculo final: 75% Modulo + 25% Reto
            $nota_final = ($media_modulo * 0.75) + ($media_reto * 0.25);
            
            // Estado: Aprobado o Suspenso
            $estado = "Suspenso";
            if ($nota_final >= 5.00) {
                $estado = "Aprobado";
            }
            
            $datos_finales[] = array(
                'nombre' => $nombre_est,
                'modulo' => $nombre_mod,
                'media_modulo' => round($media_modulo, 2),
                'media_reto' => round($media_reto, 2),
                'nota_final' => round($nota_final, 2),
                'estado' => $estado
            );
        }
    }
}
?>

<div class="encabezado-pagina">
    <h1>Cálculo de Resultados Finales por Ciclo</h1>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="resultadosModulos.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Seleccione un Ciclo formativo:</label>
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
</div>

<?php if ($id_ciclo_elegido != 0) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Módulo</th>
                        <th>Media Módulo (75%)</th>
                        <th>Media Retos (25%)</th>
                        <th>Nota Final</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($datos_finales)) { ?>
                        <tr><td colspan="6" class="sin-datos">No hay datos registrados en este ciclo</td></tr>
                    <?php } else { ?>
                        <?php foreach ($datos_finales as $fila) { 
                            $clase_estado = "texto-rojo";
                            if ($fila['estado'] == "Aprobado") { $clase_estado = "texto-verde"; }
                        ?>
                        <tr>
                            <td><strong><?php echo $fila['nombre']; ?></strong></td>
                            <td><?php echo $fila['modulo']; ?></td>
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
            <p><i class="fas fa-info-circle"></i> <strong>Fórmula:</strong> (Media Módulo * 0.75) + (Media Retos * 0.25). Aprobado si Nota Final >= 5.00.</p>
        </div>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
