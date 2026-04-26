<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

$idEstudiante = $_SESSION['idEstudiante'];

// Obtener info del estudiante para saber su ciclo
require_once "../../../modelos/estudiantes.php";
$est_info = obtenerEstudiantePorId($idEstudiante);
$idCiclo = $est_info['idCiclo'];

$tituloDelPagina = "Mis Resultados Finales - Portal Estudiantes";
$seccionActual = 'resultados_finales';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
require_once "../../../modelos/calificaciones.php";
require_once "../../../modelos/retos.php";

$lista_modulos = obtenerModulosPorCiclo($idCiclo);
$datos_finales = array();

foreach ($lista_modulos as $mod_item) {
    $id_mod = $mod_item['idModulo'];
    $nombre_mod = $mod_item['nombreModulo'];
    
    // Media Módulo
    $notas_mod = obtenerNotasModulo($idEstudiante, $id_mod);
    $suma_mod = 0; $cont_mod = 0;
    $campos = array('nota_1ev', 'nota_1final', 'nota_2ev', 'nota_2final');
    foreach ($campos as $c) {
        if (isset($notas_mod[$c]) && $notas_mod[$c] > 0) {
            $suma_mod = $suma_mod + $notas_mod[$c];
            $cont_mod = $cont_mod + 1;
        }
    }
    $media_modulo = 0;
    if ($cont_mod > 0) { $media_modulo = $suma_mod / $cont_mod; }
    
    // Media Retos
    $medias_retos = listarCalificacionesRetoPorModulo($id_mod);
    $media_reto = 0;
    if (isset($medias_retos[$idEstudiante])) {
        $media_reto = $medias_retos[$idEstudiante];
    }
    
    // Final
    $nota_final = ($media_modulo * 0.75) + ($media_reto * 0.25);
    
    $estado = "Suspenso";
    if ($cont_mod == 0) {
        $estado = "Pendiente";
    } else {
        if ($nota_final >= 5) {
            $estado = "Aprobado";
        }
    }
    
    $datos_finales[] = array(
        'modulo' => $nombre_mod,
        'media_modulo' => round($media_modulo, 2),
        'media_reto' => round($media_reto, 2),
        'nota_final' => round($nota_final, 2),
        'estado' => $estado
    );
}
?>

<div class="encabezado-pagina">
    <h1>Mis Resultados Finales</h1>
    <p class="subtitulo">Ciclo: <?php echo $est_info['nombreCiclo']; ?></p>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Media Notas (75%)</th>
                    <th>Media Retos (25%)</th>
                    <th>Nota Final</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos_finales)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay módulos registrados en su ciclo.</td></tr>
                <?php } else { ?>
                    <?php foreach ($datos_finales as $fila) { 
                        $clase = "texto-rojo";
                        if ($fila['estado'] == "Aprobado") { $clase = "texto-verde"; }
                        if ($fila['estado'] == "Pendiente") { $clase = "texto-gris"; }
                    ?>
                    <tr>
                        <td class="texto-negrita"><?php echo $fila['modulo']; ?></td>
                        <td><?php echo $fila['media_modulo']; ?></td>
                        <td><?php echo $fila['media_reto']; ?></td>
                        <td class="texto-negrita"><?php echo $fila['nota_final']; ?></td>
                        <td class="<?php echo $clase; ?> texto-negrita"><?php echo $fila['estado']; ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="margen-arriba tarjeta-gris-suave">
    <p><i class="fas fa-info-circle"></i> <strong>Nota:</strong> El cálculo se basa en el 75% de las notas de evaluación y el 25% de la media de los retos del módulo.</p>
    <p><i class="fas fa-info-circle"></i> <strong>Estados:</strong> <span class="texto-verde">Aprobado (>= 5.0)</span>, <span class="texto-rojo">Suspenso (< 5.0)</span>, <span class="texto-gris">Pendiente (Sin notas)</span>.</p>
</div>

<?php include '../comunes/footer.php'; ?>

