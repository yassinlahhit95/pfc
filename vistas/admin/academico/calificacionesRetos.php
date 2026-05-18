<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id_ciclo_elegido = $_GET['idCiclo'] ?? '';
$id_modulo_elegido = $_GET['idModulo'] ?? '';
$id_reto_elegido = $_GET['idReto'] ?? '';

$todos_los_ciclos = listarTodosLosCiclos();
$modulos_filtrados = !empty($id_ciclo_elegido) ? listarModulosPorCiclo($id_ciclo_elegido) : [];
$retos_filtrados = !empty($id_modulo_elegido) ? listarRetosFiltrados($id_modulo_elegido) : [];
$estudiantes_lista = !empty($id_reto_elegido) ? listarEstudiantesPorCiclo($id_ciclo_elegido) : [];

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "AULAPRO | NOTAS DE RETOS";
$seccion = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CALIFICACIONES POR RETO</h1>
</div>

<div class="panel">
    <form method="GET" action="calificacionesRetos.php" class="d-flex alinear-centro sep-g envoltura-flexible">
        <div class="campo relleno">
            <label for="selectCicloReto">1. Seleccione Ciclo:</label>
            <select name="idCiclo" id="selectCicloReto" onchange="this.form.submit()">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($todos_los_ciclos as $cicItem) { ?>
                    <option value="<?= $cicItem['idCiclo'] ?>" <?= ($id_ciclo_elegido == $cicItem['idCiclo']) ? 'selected' : '' ?>>
                        [<?= $cicItem['nombreNivel'] ?>] <?= $cicItem['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label>2. Seleccione Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?= empty($id_ciclo_elegido) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($modulos_filtrados as $modItem) { ?>
                    <option value="<?= $modItem['idModulo'] ?>" <?= ($id_modulo_elegido == $modItem['idModulo']) ? 'selected' : '' ?>>
                        <?= $modItem['nombreModulo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label>3. Seleccione Reto:</label>
            <select name="idReto" onchange="this.form.submit()" <?= empty($id_modulo_elegido) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($retos_filtrados as $retoItem) { ?>
                    <option value="<?= $retoItem['idReto'] ?>" <?= ($id_reto_elegido == $retoItem['idReto']) ? 'selected' : '' ?>>
                        <?= $retoItem['nombreReto'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<?php if (!empty($id_reto_elegido)) { ?>
    <div class="panel margen-arriba">
        <form action="../../../controladores/admin/academico/calificarRetos.php" method="POST">
            <input type="hidden" name="idReto" value="<?= $id_reto_elegido ?>">
            <input type="hidden" name="idCiclo" value="<?= $id_ciclo_elegido ?>">
            <input type="hidden" name="idModulo" value="<?= $id_modulo_elegido ?>">

            <div class="tcont">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Nota Reto (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estudiantes_lista)) { ?>
                            <tr>
                                <td colspan="2" class="vacio">No hay estudiantes en este ciclo</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($estudiantes_lista as $estudianteItem) {
                                $idEstudianteFila = $estudianteItem['idEstudiante'];
                                $notaRetoActual = obtenerCalificacionReto($idEstudianteFila, $id_reto_elegido);
                            ?>
                                <tr>
                                    <td>
                                        <?= strtoupper($estudianteItem['nombreEstudiante']) ?>
                                        <input type="hidden" name="estudiantes[]" value="<?= $idEstudianteFila ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="notas[]" value="<?= $notaRetoActual ?>" class="ancho-ajustable-nota">
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($estudiantes_lista)) { ?>
                <div class="margen-arriba">
                    <button type="submit" name="guardarNotasReto" class="boton-primario">
                        <i class="fas fa-save"></i> GUARDAR NOTAS DEL RETO
                    </button>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
