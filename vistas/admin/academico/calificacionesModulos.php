<?php
session_start();
$titulo_pagina = "Notas de Módulos - Super Admin";
$seccion = 'notas_modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/calificaciones.php";
require_once "../../../modelos/ciclos.php";

$id_ciclo_elegido = 0;
if (isset($_GET['idCiclo'])) {
    $id_ciclo_elegido = $_GET['idCiclo'];
}

$id_modulo_elegido = 0;
if (isset($_GET['idModulo'])) {
    $id_modulo_elegido = $_GET['idModulo'];
}

$todos_los_ciclos = listarTodosLosCiclos();

$modulos_filtrados = array();
if ($id_ciclo_elegido != 0) {
    $modulos_filtrados = obtenerModulosPorCiclo($id_ciclo_elegido);
}

$estudiantes_calificados = array();
if ($id_modulo_elegido != 0) {
    $estudiantes_calificados = listarCalificacionesPorModulo($id_modulo_elegido);
}

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Módulo</h1>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="calificacionesModulos.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>1. Seleccione un Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Seleccionar Ciclo --</option>
                <?php foreach ($todos_los_ciclos as $cicItem) { ?>
                    <option value="<?php echo $cicItem['idCiclo']; ?>" <?php if($id_ciclo_elegido == $cicItem['idCiclo']) { echo "selected"; } ?>>
                        <?php echo $cicItem['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>2. Seleccione un Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?php if($id_ciclo_elegido == 0) { echo "disabled"; } ?>>
                <option value="">-- Seleccionar Módulo --</option>
                <?php foreach ($modulos_filtrados as $modItem) { ?>
                    <option value="<?php echo $modItem['idModulo']; ?>" <?php if($id_modulo_elegido == $modItem['idModulo']) { echo "selected"; } ?>>
                        <?php echo $modItem['nombreModulo']; ?>
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
        <form action="/pfc/controladores/admin/academico/calificarModulos.php" method="POST">
            <input type="hidden" name="idModulo" value="<?php echo $id_modulo_elegido; ?>">
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
                        <?php if ($estudiantes_calificados == false || count($estudiantes_calificados) == 0) { ?>
                            <tr><td colspan="6" class="sin-datos">No hay estudiantes matriculados en este ciclo</td></tr>
                        <?php } else { ?>
                            <?php foreach ($estudiantes_calificados as $estIndividual) { 
                                $idEstudianteActual = $estIndividual['idEstudiante'];
                                $notasCompletasActuales = obtenerNotasModulo($idEstudianteActual, $id_modulo_elegido);
                                
                                $val_1ev = ""; $val_1f = ""; $val_2ev = ""; $val_2f = ""; $val_obs = "";
                                
                                if (isset($notasCompletasActuales['nota_1ev'])) { $val_1ev = $notasCompletasActuales['nota_1ev']; }
                                if (isset($notasCompletasActuales['nota_1final'])) { $val_1f = $notasCompletasActuales['nota_1final']; }
                                if (isset($notasCompletasActuales['nota_2ev'])) { $val_2ev = $notasCompletasActuales['nota_2ev']; }
                                if (isset($notasCompletasActuales['nota_2final'])) { $val_2f = $notasCompletasActuales['nota_2final']; }
                                if (isset($notasCompletasActuales['observaciones'])) { $val_obs = $notasCompletasActuales['observaciones']; }
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo strtoupper($estIndividual['nombreEstudiante']); ?></strong>
                                    <input type="hidden" name="estudiantes[]" value="<?php echo $idEstudianteActual; ?>">
                                </td>
                                <td>
                                    <input type="text" name="notas_1ev[]" value="<?php echo $val_1ev; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_1final[]" value="<?php echo $val_1f; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_2ev[]" value="<?php echo $val_2ev; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="notas_2final[]" value="<?php echo $val_2f; ?>" class="ancho-ajustable-nota">
                                </td>
                                <td>
                                    <input type="text" name="observaciones[]" value="<?php echo $val_obs; ?>" class="ancho-total">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($estudiantes_calificados) { ?>
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

