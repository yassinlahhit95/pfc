<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfesor = $_SESSION['idProfesor'];

$id_ciclo_elegido = 0;
if (isset($_GET['idCiclo'])) {
    $id_ciclo_elegido = intval($_GET['idCiclo']);
}

$id_modulo_elegido = 0;
if (isset($_GET['idModulo'])) {
    $id_modulo_elegido = intval($_GET['idModulo']);
}

// Filtros: Solo los ciclos y módulos asignados al profesor
$mis_ciclos = obtenerCiclosDeProfesor($idProfesor);
$modulos_filtrados = array();
if ($id_ciclo_elegido != 0) {
    // Solo módulos del ciclo elegido que pertenezcan al profesor
    $todos_modulos_ciclo = obtenerModulosPorCiclo($id_ciclo_elegido);
    $mis_modulos_general = obtenerModulosDeProfesor($idProfesor);
    $mis_ids_modulos = array_column($mis_modulos_general, 'idModulo');
    
    foreach ($todos_modulos_ciclo as $m) {
        if (in_array($m['idModulo'], $mis_ids_modulos)) {
            $modulos_filtrados[] = $m;
        }
    }
}

$estudiantes_calificados = array();
if ($id_modulo_elegido != 0) {
    // Obtener estudiantes del ciclo y sus notas para este módulo
    $estudiantes_calificados = listarCalificacionesPorModulo($id_modulo_elegido);
}

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "Calificaciones por Módulo - Portal Profesores";
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
                <?php foreach ($mis_ciclos as $cic) { ?>
                    <option value="<?php echo $cic['idCiclo']; ?>" <?php if($id_ciclo_elegido == $cic['idCiclo']) { echo "selected"; } ?>>
                        <?php echo $cic['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>2. Seleccione un Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?php if($id_ciclo_elegido == 0) { echo "disabled"; } ?>>
                <option value="">-- Seleccionar Módulo --</option>
                <?php foreach ($modulos_filtrados as $mod) { ?>
                    <option value="<?php echo $mod['idModulo']; ?>" <?php if($id_modulo_elegido == $mod['idModulo']) { echo "selected"; } ?>>
                        <?php echo $mod['nombreModulo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error != "") { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<?php if ($id_modulo_elegido != 0) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="/pfc/controladores/profesores/calificaciones/calificarModulos_prof.php" method="POST">
            <input type="hidden" name="idModulo" value="<?php echo $id_modulo_elegido; ?>">
            <input type="hidden" name="idCiclo" value="<?php echo $id_ciclo_elegido; ?>">
            
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
                        <?php if (empty($estudiantes_calificados)) { ?>
                            <tr><td colspan="6" class="sin-datos">No hay estudiantes matriculados en este ciclo</td></tr>
                        <?php } else { ?>
                            <?php foreach ($estudiantes_calificados as $cal) { 
                                $id_est = $cal['idEstudiante'];
                                $notasCompletas = obtenerNotasModulo($id_est, $id_modulo_elegido);
                                
                                $v_1ev = $notasCompletas['nota_1ev'] ?? "";
                                $v_1f = $notasCompletas['nota_1final'] ?? "";
                                $v_2ev = $notasCompletas['nota_2ev'] ?? "";
                                $v_2f = $notasCompletas['nota_2final'] ?? "";
                                $v_obs = $notasCompletas['observaciones'] ?? "";
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo $cal['nombreEstudiante']; ?></strong>
                                    <input type="hidden" name="estudiantes[]" value="<?php echo $id_est; ?>">
                                </td>
                                <td>
                                    <input type="text" name="notas_1ev[]" value="<?php echo $v_1ev; ?>" style="width: 60px;">
                                </td>
                                <td>
                                    <input type="text" name="notas_1final[]" value="<?php echo $v_1f; ?>" style="width: 60px;">
                                </td>
                                <td>
                                    <input type="text" name="notas_2ev[]" value="<?php echo $v_2ev; ?>" style="width: 60px;">
                                </td>
                                <td>
                                    <input type="text" name="notas_2final[]" value="<?php echo $v_2f; ?>" style="width: 60px;">
                                </td>
                                <td>
                                    <input type="text" name="observaciones[]" value="<?php echo $v_obs; ?>" class="ancho-total">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($estudiantes_calificados)) { ?>
                <div class="margen-arriba disposicion-flexible alinear-centro">
                    <button type="submit" name="guardarNotas" class="boton-primario">
                        <i class="fas fa-save"></i> Guardar Todas las Notas
                    </button>
                    <label style="margin-left: 20px; font-weight: bold; color: #3498db; cursor: pointer;">
                        <input type="checkbox" name="notificarEstudiantes" value="1"> 
                        <i class="fas fa-envelope"></i> Notificar a los estudiantes por email
                    </label>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
