<?php
session_start();

// Validar que sea profesor
if (empty($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfLogueado = $_SESSION['idProfesor'];

// Coger filtros de la URL
$idCicloSeleccionado = 0;
if (!empty($_GET['idCiclo'])) {
    $idCicloSeleccionado = intval($_GET['idCiclo']);
}

$idModuloSeleccionado = 0;
if (!empty($_GET['idModulo'])) {
    $idModuloSeleccionado = intval($_GET['idModulo']);
}

// Listas para los selects
$listaMisCiclos = obtenerCiclosDeProfesor($idProfLogueado);
$listaModulosFiltrados = array();

if (!empty($idCicloSeleccionado)) {
    $todosLosModulosDelCiclo = obtenerModulosPorCiclo($idCicloSeleccionado);
    $misModulosAsignados = obtenerIdsModulosDeProfesor($idProfLogueado);
    
    foreach ($todosLosModulosDelCiclo as $moduloItem) {
        $idM = $moduloItem['idModulo'];
        if (in_array($idM, $misModulosAsignados)) {
            $listaModulosFiltrados[] = $moduloItem;
        }
    }
}

// Sacar alumnos si ya eligio modulo
$listaAlumnos = array();
if (!empty($idModuloSeleccionado)) {
    $listaAlumnos = listarCalificacionesPorModulo($idModuloSeleccionado);
}

// Mensajes de la sesion
$error_msg = "";
if (!empty($_SESSION['error'])) { $error_msg = $_SESSION['error']; }

$exito_msg = "";
if (!empty($_SESSION['exito'])) { $exito_msg = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "Calificaciones - Portal Profesores";
$seccionActual = 'calificaciones';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Módulo</h1>
    <p class="subtitulo">Aqui puedes poner las notas de todos tus alumnos a la vez</p>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="agregar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>1. Selecciona el Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Elige un ciclo --</option>
                <?php foreach ($listaMisCiclos as $c) { ?>
                    <option value="<?php echo $c['idCiclo']; ?>" <?php if($idCicloSeleccionado == $c['idCiclo']) { echo "selected"; } ?>>
                        <?php echo $c['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>2. Selecciona el Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?php if(empty($idCicloSeleccionado)) { echo "disabled"; } ?>>
                <option value="">-- Elige un modulo --</option>
                <?php foreach ($listaModulosFiltrados as $m) { ?>
                    <option value="<?php echo $m['idModulo']; ?>" <?php if($idModuloSeleccionado == $m['idModulo']) { echo "selected"; } ?>>
                        <?php echo $m['nombreModulo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if (!empty($exito_msg)) { ?>
    <div class="mensaje-exito"><?php echo $exito_msg; ?></div>
<?php } ?>
<?php if (!empty($error_msg)) { ?>
    <div class="mensaje-error"><?php echo $error_msg; ?></div>
<?php } ?>

<?php if (!empty($idModuloSeleccionado)) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="/pfc/controladores/profesores/calificaciones/calificarModulos_prof.php" method="POST">
            <input type="hidden" name="idModulo" value="<?php echo $idModuloSeleccionado; ?>">
            <input type="hidden" name="idCiclo" value="<?php echo $idCicloSeleccionado; ?>">
            
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>1ª Ev</th>
                            <th>1ª Final</th>
                            <th>2ª Ev</th>
                            <th>2ª Final</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaAlumnos)) { ?>
                            <tr><td colspan="6" class="sin-datos">No hay alumnos en este ciclo todavia.</td></tr>
                        <?php } else { ?>
                            <?php foreach ($listaAlumnos as $alu) { 
                                $idA = $alu['idEstudiante'];
                                $notas = obtenerNotasModulo($idA, $idModuloSeleccionado);
                                
                                $v1 = ""; $v1f = ""; $v2 = ""; $v2f = ""; $vobs = "";

                                if (isset($notas['nota_1ev'])) { $v1 = $notas['nota_1ev']; }
                                if (isset($notas['nota_1final'])) { $v1f = $notas['nota_1final']; }
                                if (isset($notas['nota_2ev'])) { $v2 = $notas['nota_2ev']; }
                                if (isset($notas['nota_2final'])) { $v2f = $notas['nota_2final']; }
                                if (isset($notas['observaciones'])) { $vobs = $notas['observaciones']; }
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo $alu['nombreEstudiante']; ?></strong>
                                    <input type="hidden" name="estudiantes[]" value="<?php echo $idA; ?>">
                                </td>
                                <td>
                                    <input type="text" name="notas_1ev[]" value="<?php echo $v1; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_1final[]" value="<?php echo $v1f; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_2ev[]" value="<?php echo $v2; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_2final[]" value="<?php echo $v2f; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="observaciones[]" value="<?php echo $vobs; ?>" class="ancho-total">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($listaAlumnos)) { ?>
                <div class="margen-arriba disposicion-flexible alinear-centro">
                    <button type="submit" name="guardarNotas" class="boton-primario">
                        <i class="fas fa-save"></i> Guardar todas las Notas
                    </button>
                    <label class="etiqueta-notificacion">
                        <input type="checkbox" name="notificarEstudiantes" value="1"> 
                        <i class="fas fa-envelope"></i> Enviar aviso por email
                    </label>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>

