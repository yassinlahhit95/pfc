<?php
session_start();
$titulo_pagina = "Calificaciones por Módulo - Super Admin";
$seccion = 'notas_modulos';
include_once "../comunes/nav.php";

require_once "../../../modelos/ciclos.php";
require_once "../../../modelos/modulos.php";
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/calificaciones.php";

$listaCiclos = listarTodosLosCiclos();

$idCicloElegido = null;
if (isset($_GET['idCiclo'])) {
    $idCicloElegido = $_GET['idCiclo'];
}

$idModuloElegido = null;
if (isset($_GET['idModulo'])) {
    $idModuloElegido = $_GET['idModulo'];
}

// Obtenemos modulos (filtrados por ciclo si hay uno elegido)
$listaModulos = [];
if ($idCicloElegido) {
    $listaModulos = listarModulosPorCiclo($idCicloElegido);
}

// Obtenemos estudiantes (filtrados por ciclo si hay uno elegido)
$listaEstudiantes = [];
if ($idCicloElegido) {
    $listaEstudiantes = listarEstudiantes(); // En un sistema real filtraríamos por ciclo
}

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Módulo</h1>
    <p class="subtitulo-encabezado">Gestione las notas de los alumnos de forma masiva</p>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="GET" action="/pfc/vistas/admin/academico/calificacionesModulos.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Ciclo Formativo</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Seleccionar Ciclo --</option>
                <?php foreach ($listaCiclos as $c) { ?>
                    <option value="<?php echo $c['idCiclo']; ?>" <?php if ($idCicloElegido == $c['idCiclo']) { echo 'selected'; } ?>>
                        <?php echo $c['nombreCiclo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>Módulo / Asignatura</label>
            <select name="idModulo" onchange="this.form.submit()" <?php if (!$idCicloElegido) { echo 'disabled'; } ?>>
                <option value="">-- Seleccionar Módulo --</option>
                <?php foreach ($listaModulos as $m) { ?>
                    <option value="<?php echo $m['idModulo']; ?>" <?php if ($idModuloElegido == $m['idModulo']) { echo 'selected'; } ?>>
                        <?php echo $m['nombreModulo']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($idModuloElegido) { ?>
<div class="tarjeta-blanca mt-25">
    <div class="titulo-tarjeta">
        <h3>Lista de Alumnos</h3>
    </div>
    <form action="/pfc/controladores/admin/academico/calificarModulos.php" method="POST">
        <input type="hidden" name="idModulo" value="<?php echo $idModuloElegido; ?>">
        <input type="hidden" name="idCiclo" value="<?php echo $idCicloElegido; ?>">
        
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th style="text-align: center;">1ª Ev.</th>
                        <th style="text-align: center;">1ª Final</th>
                        <th style="text-align: center;">2ª Ev.</th>
                        <th style="text-align: center;">2ª Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($listaEstudiantes as $est) { 
                        // Solo mostrar estudiantes del ciclo
                        if ($est['idCiclo'] == $idCicloElegido) {
                            $notas = obtenerNotasModulo($est['idEstudiante'], $idModuloElegido);
                            
                            $n1ev = '';
                            if (isset($notas['nota_1ev'])) {
                                $n1ev = $notas['nota_1ev'];
                            }
                            
                            $n1f = '';
                            if (isset($notas['nota_1final'])) {
                                $n1f = $notas['nota_1final'];
                            }
                            
                            $n2ev = '';
                            if (isset($notas['nota_2ev'])) {
                                $n2ev = $notas['nota_2ev'];
                            }
                            
                            $n2f = '';
                            if (isset($notas['nota_2final'])) {
                                $n2f = $notas['nota_2final'];
                            }
                    ?>
                        <tr>
                            <td><strong><?php echo $est['nombreEstudiante']; ?></strong></td>

                            <td style="text-align: center;">
                                <input type="text" name="n1ev[<?php echo $est['idEstudiante']; ?>]"
                                       value="<?php echo $n1ev; ?>"
                                       class="p-5 w-80 br-4 border-ddd" placeholder="0.00">
                            </td>

                            <td style="text-align: center;">
                                <input type="text" name="n1f[<?php echo $est['idEstudiante']; ?>]"
                                       value="<?php echo $n1f; ?>"
                                       class="p-5 w-80 br-4 border-ddd" placeholder="0.00">
                            </td>

                            <td style="text-align: center;">
                                <input type="text" name="n2ev[<?php echo $est['idEstudiante']; ?>]"
                                       value="<?php echo $n2ev; ?>"
                                       class="p-5 w-80 br-4 border-ddd" placeholder="0.00">
                            </td>

                            <td style="text-align: center;">
                                <input type="text" name="n2f[<?php echo $est['idEstudiante']; ?>]"
                                       value="<?php echo $n2f; ?>"
                                       class="p-5 w-80 br-4 border-ddd" placeholder="0.00">
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarNotas" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Todas las Notas
            </button>
        </div>
    </form>
</div>
<?php } else { ?>
    <div class="mensaje-info mt-25">
        <i class="fas fa-info-circle"></i> Seleccione un Ciclo y un Módulo para comenzar a calificar.
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>