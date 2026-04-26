<?php
session_start();

// Validación de sesión simple
if (isset($_SESSION['idAdmin']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "RESULTADOS FINALES - SUPER ADMIN";
$seccion = 'resultados_modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/calificaciones.php";
require_once "../../../modelos/retos.php";
require_once "../../../modelos/ciclos.php";

// Captura del ciclo seleccionado
$idCicloElegidoParaVer = 0;
if (isset($_GET['idCiclo'])) {
    $idCicloElegidoParaVer = (int)$_GET['idCiclo'];
}

$listaDeTodosLosCiclos = listarTodosLosCiclos();
$listaDeDatosFinalesAMostrar = array();

if ($idCicloElegidoParaVer != 0) {
    // 1. Obtener todos los estudiantes del ciclo
    $listaEstudiantesDelCiclo = listarEstudiantesPorCiclo($idCicloElegidoParaVer);
    
    // 2. Obtener todos los módulos del ciclo
    $listaModulosDelCiclo = obtenerModulosPorCiclo($idCicloElegidoParaVer);
    
    foreach ($listaEstudiantesDelCiclo as $estudianteIndividual) {
        $idDeEsteEstudiante = $estudianteIndividual['idEstudiante'];
        $nombreDelEstudiante = strtoupper($estudianteIndividual['nombreEstudiante']);
        
        $sumaNotasAcumuladaModulos = 0;
        $contadorTotalDeNotasIngresadas = 0;
        
        $sumaMediasAcumuladaRetos = 0;
        $contadorModulosQueTienenReto = 0;
        $existeAlMenosUnModuloSuspenso = false;

        foreach ($listaModulosDelCiclo as $moduloIndividual) {
            $idDeEsteModulo = $moduloIndividual['idModulo'];
            
            // --- CÁLCULO MEDIA MÓDULO (Promedio de las notas ingresadas en los 4 slots) ---
            $datosDeNotasDelModulo = obtenerNotasModulo($idDeEsteEstudiante, $idDeEsteModulo);
            
            $camposDeNotaAChequear = array('nota_1ev', 'nota_1final', 'nota_2ev', 'nota_2final');
            $sumaNotasDeEsteModulo = 0;
            $contadorNotasDeEsteModulo = 0;

            foreach ($camposDeNotaAChequear as $nombreDelCampo) {
                if (isset($datosDeNotasDelModulo[$nombreDelCampo])) {
                    $valorDeLaNota = $datosDeNotasDelModulo[$nombreDelCampo];
                    if (is_numeric($valorDeLaNota) && $valorDeLaNota > 0) {
                        $sumaNotasDeEsteModulo = $sumaNotasDeEsteModulo + $valorDeLaNota;
                        $contadorNotasDeEsteModulo = $contadorNotasDeEsteModulo + 1;
                        
                        $sumaNotasAcumuladaModulos = $sumaNotasAcumuladaModulos + $valorDeLaNota;
                        $contadorTotalDeNotasIngresadas = $contadorTotalDeNotasIngresadas + 1;
                    }
                }
            }
            
            // Verificamos si el módulo está suspenso
            if ($contadorNotasDeEsteModulo > 0) {
                $mediaDeEsteModulo = $sumaNotasDeEsteModulo / $contadorNotasDeEsteModulo;
                if ($mediaDeEsteModulo < 5) {
                    $existeAlMenosUnModuloSuspenso = true;
                }
            }
            
            // --- CÁLCULO MEDIA RETOS ---
            $mapaMediasRetosPorModulo = listarCalificacionesRetoPorModulo($idDeEsteModulo);
            if (isset($mapaMediasRetosPorModulo[$idDeEsteEstudiante])) {
                $mediaDeRetosDeEsteModulo = $mapaMediasRetosPorModulo[$idDeEsteEstudiante];
                $sumaMediasAcumuladaRetos = $sumaMediasAcumuladaRetos + $mediaDeRetosDeEsteModulo;
                $contadorModulosQueTienenReto = $contadorModulosQueTienenReto + 1;
            }
        }
        
        // Calculamos promedios finales del estudiante
        $promedioGlobalModulos = 0;
        if ($contadorTotalDeNotasIngresadas > 0) {
            $promedioGlobalModulos = $sumaNotasAcumuladaModulos / $contadorTotalDeNotasIngresadas;
        }
        
        $promedioGlobalRetos = 0;
        if ($contadorModulosQueTienenReto > 0) {
            $promedioGlobalRetos = $sumaMediasAcumuladaRetos / $contadorModulosQueTienenReto;
        }
        
        // Cálculo final: 75% Módulos + 25% Retos
        $notaFinalDelCiclo = ($promedioGlobalModulos * 0.75) + ($promedioGlobalRetos * 0.25);
        $notaFinalRedondeada = round($notaFinalDelCiclo, 2);
        
        // Determinamos el estado final
        $textoEstadoFinal = "SUSPENSO";
        if ($contadorTotalDeNotasIngresadas == 0) {
            $textoEstadoFinal = "PENDIENTE";
        } else {
            // Aprobado si la nota es >= 5 y NO tiene ningún módulo suspenso
            if ($notaFinalRedondeada >= 5.00 && $existeAlMenosUnModuloSuspenso == false) {
                $textoEstadoFinal = "APROBADO";
            }
        }
        
        // Guardamos los datos procesados en el array de visualización
        $datosParaLaTabla = array();
        $datosParaLaTabla['nombreEstudiante'] = $nombreDelEstudiante;
        $datosParaLaTabla['mediaModulos'] = round($promedioGlobalModulos, 2);
        $datosParaLaTabla['mediaRetos'] = round($promedioGlobalRetos, 2);
        $datosParaLaTabla['notaFinal'] = $notaFinalRedondeada;
        $datosParaLaTabla['estado'] = $textoEstadoFinal;
        $datosParaLaTabla['tieneAlerta'] = $existeAlMenosUnModuloSuspenso;
        
        $listaDeDatosFinalesAMostrar[] = $datosParaLaTabla;
    }
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

<?php if ($mensajeExito != "") { ?> <div class="mensaje-exito"><?php echo $mensajeExito; ?></div> <?php } ?>
<?php if ($mensajeError != "") { ?> <div class="mensaje-error"><?php echo $mensajeError; ?></div> <?php } ?>

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

        <?php if ($idCicloElegidoParaVer != 0 && $listaDeDatosFinalesAMostrar) { ?>
            <form action="/pfc/controladores/admin/academico/enviarNotasMasivo.php" method="POST" onsubmit="return confirm('¿Está seguro de enviar las notas por email a todos los estudiantes de este ciclo?')">
                <input type="hidden" name="idCiclo" value="<?php echo $idCicloElegidoParaVer; ?>">
                <button type="submit" class="boton-primario">
                    <i class="fas fa-paper-plane"></i> ENVIAR RESULTADOS POR EMAIL A TODOS
                </button>
            </form>
        <?php } ?>
    </div>
</div>

<?php if ($idCicloElegidoParaVer != 0) { ?>
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
                    <?php if ($listaDeDatosFinalesAMostrar == false || count($listaDeDatosFinalesAMostrar) == 0) { ?>
                        <tr><td colspan="5" class="sin-datos">No hay estudiantes en este ciclo</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaDeDatosFinalesAMostrar as $fila) { 
                            $claseDelColor = "texto-rojo";
                            if ($fila['estado'] == "APROBADO") { $claseDelColor = "texto-verde"; }
                            if ($fila['estado'] == "PENDIENTE") { $claseDelColor = "texto-gris"; }
                        ?>
                        <tr>
                            <td><strong><?php echo $fila['nombreEstudiante']; ?></strong></td>
                            <td><?php echo $fila['mediaModulos']; ?></td>
                            <td><?php echo $fila['mediaRetos']; ?></td>
                            <td class="texto-negrita"><?php echo $fila['notaFinal']; ?></td>
                            <td class="<?php echo $claseDelColor; ?> texto-negrita">
                                <?php echo $fila['estado']; ?>
                                <?php if($fila['tieneAlerta'] == true && $fila['estado'] != "PENDIENTE") { 
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

