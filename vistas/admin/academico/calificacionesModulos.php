<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$idModuloElegido = $_GET['idModulo'] ?? '';

$listaCiclos = listarTodosLosCiclos();
$listaModulos = !empty($idCicloElegido) ? listarModulosPorCiclo($idCicloElegido) : [];
$listaEstudiantes = !empty($idModuloElegido) ? listarCalificacionesPorModulo($idModuloElegido) : [];

$titulo_pagina = "AULAPRO | NOTAS DE MÓDULOS";
$seccion = 'notas_modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CALIFICACIONES POR MÓDULO</h1>
</div>

<div class="panel">
    <form method="GET" action="calificacionesModulos.php" class="caja alinear-centro espacio-grande caja-libre">
        <div class="campo relleno">
            <label for="selectCicloMod">Seleccione un Ciclo:</label>
            <select name="idCiclo" id="selectCicloMod" onchange="this.form.submit()">
                <option value="">-Seleccionar Ciclo-</option>
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        [<?= Security::escapeHtml($ciclo['nombreNivel']) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label>2. Seleccione un Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?= empty($idCicloElegido) ? 'disabled' : '' ?>>
                <option value="">-Seleccionar Modulo-</option>
                <?php foreach ($listaModulos as $modulo) { ?>
                    <option value="<?= Security::escapeHtml($modulo['idModulo']) ?>" <?= ($idModuloElegido == $modulo['idModulo']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($modulo['nombreModulo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito) { ?><div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div><?php } ?>
<?php if ($errores) { ?><div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div><?php } ?>

<?php if (!empty($idModuloElegido)) { ?>
    <div class="panel margen-arriba">
        <form action="../../../controladores/admin/academico/calificarModulos.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModuloElegido) ?>">
            <input type="hidden" name="idCiclo" value="<?= Security::escapeHtml($idCicloElegido) ?>">
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>1º EV</th>
                            <th>1º FINAL</th>
                            <th>2º EV</th>
                            <th>2º FINAL</th>
                            <th>OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaEstudiantes)) { ?>
                            <tr>
                                <td colspan="6" class="vacio">No hay estudiantes matriculados en este ciclo</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($listaEstudiantes as $alumno) {
                                $idEstudiante = $alumno['idEstudiante'];
                                $notas = obtenerNotasModulo($idEstudiante, $idModuloElegido) ?? [];
                            ?>
                                <tr>
                                    <td>
                                        <?= mb_strtoupper(Security::escapeHtml($alumno['nombreEstudiante']), 'UTF-8') ?>
                                        <input type="hidden" name="estudiantes[]" value="<?= Security::escapeHtml($idEstudiante) ?>">
                                    </td>
                                    <td><input type="text" name="notas_1ev[]" value="<?= Security::escapeHtml($notas['nota_1ev'] ?? '') ?>" class="ancho-ajustable-nota"></td>
                                    <td><input type="text" name="notas_1final[]" value="<?= Security::escapeHtml($notas['nota_1final'] ?? '') ?>" class="ancho-ajustable-nota"></td>
                                    <td><input type="text" name="notas_2ev[]" value="<?= Security::escapeHtml($notas['nota_2ev'] ?? '') ?>" class="ancho-ajustable-nota"></td>
                                    <td><input type="text" name="notas_2final[]" value="<?= Security::escapeHtml($notas['nota_2final'] ?? '') ?>" class="ancho-ajustable-nota"></td>
                                    <td><input type="text" name="observaciones[]" value="<?= Security::escapeHtml($notas['observaciones'] ?? '') ?>" class="ancho-total"></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($listaEstudiantes)) { ?>
                <div class="acciones">
                    <input type="submit" name="guardarNotas" class="boton-primario" value="GUARDAR TODAS LAS NOTAS">
</div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
