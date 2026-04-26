<?php
session_start();

// Validación de sesión
if (isset($_SESSION['idProfesor']) == false || $_SESSION['idProfesor'] == "") {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfesorLogueado = $_SESSION['idProfesor'];

// Captura de filtros desde la URL
$idCicloSeleccionado = 0;
if (isset($_GET['idCiclo']) && $_GET['idCiclo'] != "") {
    $idCicloSeleccionado = intval($_GET['idCiclo']);
}

$idModuloSeleccionado = 0;
if (isset($_GET['idModulo']) && $_GET['idModulo'] != "") {
    $idModuloSeleccionado = intval($_GET['idModulo']);
}

// Obtener listas para los selectores de filtros
$listaMisCiclos = obtenerCiclosDeProfesor($idProfesorLogueado);
$listaModulosFiltrados = array();

if ($idCicloSeleccionado != 0) {
    // Obtenemos módulos del ciclo y solo los que imparte este profesor
    $todosLosModulosDelCiclo = obtenerModulosPorCiclo($idCicloSeleccionado);
    $misModulosAsignados = obtenerIdsModulosDeProfesor($idProfesorLogueado);
    
    foreach ($todosLosModulosDelCiclo as $moduloIndividual) {
        $idDeEsteModulo = $moduloIndividual['idModulo'];
        if (in_array($idDeEsteModulo, $misModulosAsignados)) {
            $listaModulosFiltrados[] = $moduloIndividual;
        }
    }
}

// Obtener estudiantes si hay un módulo seleccionado
$listaEstudiantesAMostrar = array();
if ($idModuloSeleccionado != 0) {
    $listaEstudiantesAMostrar = listarCalificacionesPorModulo($idModuloSeleccionado);
}

// Manejo de mensajes de éxito o error
$mensajeDeError = "";
if (isset($_SESSION['error']) && $_SESSION['error'] != "") { 
    $mensajeDeError = $_SESSION['error']; 
}

$mensajeDeExito = "";
if (isset($_SESSION['exito']) && $_SESSION['exito'] != "") { 
    $mensajeDeExito = $_SESSION['exito']; 
}

// Limpiamos la sesión después de capturar los mensajes
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "CALIFICACIONES POR MÓDULO - PORTAL PROFESORES";
$seccionActual = 'calificaciones';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Módulo</h1>
    <p class="subtitulo">Introduzca las notas de todos los alumnos de un módulo a la vez</p>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="agregar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>1. Seleccione un Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Seleccionar Ciclo --</option>
                <?php foreach ($listaMisCiclos as $cicloItem) { ?>
                    <option value="<?php echo $cicloItem['idCiclo']; ?>" <?php if($idCicloSeleccionado == $cicloItem['idCiclo']) { echo "selected"; } ?>>
                        <?php echo strtoupper($cicloItem['nombreCiclo']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>2. Seleccione un Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?php if($idCicloSeleccionado == 0) { echo "disabled"; } ?>>
                <option value="">-- Seleccionar Módulo --</option>
                <?php foreach ($listaModulosFiltrados as $moduloItem) { ?>
                    <option value="<?php echo $moduloItem['idModulo']; ?>" <?php if($idModuloSeleccionado == $moduloItem['idModulo']) { echo "selected"; } ?>>
                        <?php echo strtoupper($moduloItem['nombreModulo']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($mensajeDeExito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensajeDeExito; ?></div>
<?php } ?>
<?php if ($mensajeDeError != "") { ?>
    <div class="mensaje-error"><?php echo $mensajeDeError; ?></div>
<?php } ?>

<?php if ($idModuloSeleccionado != 0) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="/pfc/controladores/profesores/calificaciones/calificarModulos_prof.php" method="POST">
            <input type="hidden" name="idModulo" value="<?php echo $idModuloSeleccionado; ?>">
            <input type="hidden" name="idCiclo" value="<?php echo $idCicloSeleccionado; ?>">
            
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>1ª Ev</th>
                            <th>1ª Final</th>
                            <th>2ª Ev</th>
                            <th>2ª Final</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($listaEstudiantesAMostrar == false || count($listaEstudiantesAMostrar) == 0) { ?>
                            <tr><td colspan="6" class="sin-datos">No hay estudiantes matriculados en este ciclo</td></tr>
                        <?php } else { ?>
                            <?php foreach ($listaEstudiantesAMostrar as $estudianteCalificado) { 
                                $idDelEstudiante = $estudianteCalificado['idEstudiante'];
                                $notasCompletasDelEstudiante = obtenerNotasModulo($idDelEstudiante, $idModuloSeleccionado);
                                
                                $valorNota1Ev = "";
                                $valorNota1Final = "";
                                $valorNota2Ev = "";
                                $valorNota2Final = "";
                                $textoObservaciones = "";

                                if (isset($notasCompletasDelEstudiante['nota_1ev'])) { $valorNota1Ev = $notasCompletasDelEstudiante['nota_1ev']; }
                                if (isset($notasCompletasDelEstudiante['nota_1final'])) { $valorNota1Final = $notasCompletasDelEstudiante['nota_1final']; }
                                if (isset($notasCompletasDelEstudiante['nota_2ev'])) { $valorNota2Ev = $notasCompletasDelEstudiante['nota_2ev']; }
                                if (isset($notasCompletasDelEstudiante['nota_2final'])) { $valorNota2Final = $notasCompletasDelEstudiante['nota_2final']; }
                                if (isset($notasCompletasDelEstudiante['observaciones'])) { $textoObservaciones = $notasCompletasDelEstudiante['observaciones']; }
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo strtoupper($estudianteCalificado['nombreEstudiante']); ?></strong>
                                    <input type="hidden" name="estudiantes[]" value="<?php echo $idDelEstudiante; ?>">
                                </td>
                                <td>
                                    <input type="text" name="notas_1ev[]" value="<?php echo $valorNota1Ev; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_1final[]" value="<?php echo $valorNota1Final; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_2ev[]" value="<?php echo $valorNota2Ev; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_2final[]" value="<?php echo $valorNota2Final; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="observaciones[]" value="<?php echo $textoObservaciones; ?>" class="ancho-total">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($listaEstudiantesAMostrar) { ?>
                <div class="margen-arriba disposicion-flexible alinear-centro">
                    <button type="submit" name="guardarNotas" class="boton-primario">
                        <i class="fas fa-save"></i> GUARDAR TODAS LAS NOTAS
                    </button>
                    <label class="etiqueta-notificacion">
                        <input type="checkbox" name="notificarEstudiantes" value="1"> 
                        <i class="fas fa-envelope"></i> NOTIFICAR POR EMAIL
                    </label>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
