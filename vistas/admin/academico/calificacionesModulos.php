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
                <?php foreach ($todos_los_ciclos as $cic) { ?>
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
                        <?php if (empty($estudiantes_calificados)) { ?>
                            <tr><td colspan="6" class="sin-datos">No hay estudiantes matriculados en este ciclo</td></tr>
                        <?php } else { ?>
                            <?php foreach ($estudiantes_calificados as $cal) { 
                                $id_est = $cal['idEstudiante'];
                                $notasCompletas = obtenerNotasModulo($id_est, $id_modulo_elegido);
                                
                                // Inicializar variables con valores por defecto (evitando ??)
                                $v_1ev = ""; $v_1f = ""; $v_2ev = ""; $v_2f = ""; $v_obs = "";
                                
                                if (isset($notasCompletas['nota_1ev'])) { $v_1ev = $notasCompletas['nota_1ev']; }
                                if (isset($notasCompletas['nota_1final'])) { $v_1f = $notasCompletas['nota_1final']; }
                                if (isset($notasCompletas['nota_2ev'])) { $v_2ev = $notasCompletas['nota_2ev']; }
                                if (isset($notasCompletas['nota_2final'])) { $v_2f = $notasCompletas['nota_2final']; }
                                if (isset($notasCompletas['observaciones'])) { $v_obs = $notasCompletas['observaciones']; }
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
                <div class="margen-arriba">
                    <button type="submit" name="guardarNotas" class="boton-primario">
                        <i class="fas fa-save"></i> Guardar Todas las Notas
                    </button>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
