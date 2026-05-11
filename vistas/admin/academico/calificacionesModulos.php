<?php
session_start();
$titulo_pagina = "AULAPRO | NOTAS DE MÓDULOS";
$seccion = 'notas_modulos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$idModuloElegido = $_GET['idModulo'] ?? '';

$listaCiclos = listarTodosLosCiclos();
$listaModulos = !empty($idCicloElegido) ? obtenerModulosPorCiclo($idCicloElegido) : [];
$listaEstudiantes = !empty($idModuloElegido) ? listarCalificacionesPorModulo($idModuloElegido) : [];

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>CALIFICACIONES POR MÓDULO</h1>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="calificacionesModulos.php" class="disposicion-flexible alinear-centro separacion-grande envoltura-flexible">
        <div class="campo-formulario flexible-rellenar">
            <label>1. Seleccione un Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Seleccionar Ciclo --</option>
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>2. Seleccione un Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?= empty($idCicloElegido) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar Módulo --</option>
                <?php foreach ($listaModulos as $modulo) { ?>
                    <option value="<?= $modulo['idModulo'] ?>" <?= ($idModuloElegido == $modulo['idModulo']) ? 'selected' : '' ?>>
                        <?= $modulo['nombreModulo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito) { ?><div class="mensaje-exito"><?= $exito ?></div><?php } ?>
<?php if ($error) { ?><div class="mensaje-error"><?= $error ?></div><?php } ?>

<?php if (!empty($idModuloElegido)) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="../../../controladores/admin/academico/calificarModulos.php" method="POST">
            <input type="hidden" name="idModulo" value="<?= $idModuloElegido ?>">
            <input type="hidden" name="idCiclo" value="<?= $idCicloElegido ?>">
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>1ª EV</th>
                            <th>1ª FINAL</th>
                            <th>2ª EV</th>
                            <th>2ª FINAL</th>
                            <th>OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaEstudiantes)) { ?>
                            <tr>
                                <td colspan="6" class="sin-datos">No hay estudiantes matriculados en este ciclo</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($listaEstudiantes as $alumno) {
                                $idEst = $alumno['idEstudiante'];
                                $notas = obtenerNotasModulo($idEst, $idModuloElegido) ?? [];
                            ?>
                                <tr>
                                    <td>
                                        <strong><?= mb_strtoupper($alumno['nombreEstudiante'], 'UTF-8') ?></strong>
                                        <input type="hidden" name="estudiantes[]" value="<?= $idEst ?>">
                                    </td>
                                    <td><input type="text" name="notas_1ev[]" value="<?= $notas['nota_1ev'] ?? '' ?>" class="ancho-ajustable-nota"></td>
                                    <td><input type="text" name="notas_1final[]" value="<?= $notas['nota_1final'] ?? '' ?>" class="ancho-ajustable-nota"></td>
                                    <td><input type="text" name="notas_2ev[]" value="<?= $notas['nota_2ev'] ?? '' ?>" class="ancho-ajustable-nota"></td>
                                    <td><input type="text" name="notas_2final[]" value="<?= $notas['nota_2final'] ?? '' ?>" class="ancho-ajustable-nota"></td>
                                    <td><input type="text" name="observaciones[]" value="<?= $notas['observaciones'] ?? '' ?>" class="ancho-total"></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($listaEstudiantes)) { ?>
                <div class="form-acciones">
                    <button type="submit" name="guardarNotas" class="boton-primario">
                        <i class="fas fa-save"></i> GUARDAR TODAS LAS NOTAS
                    </button>
                    <button type="button" class="boton-secundario" onclick="window.location.href = 'calificacionesModulos.php';">
                        <i class="fas fa-eraser"></i> LIMPIAR
                    </button>
                    <label class="etiqueta-notificacion self-center">
                        <input type="checkbox" name="notificarEstudiantes" value="1">
                        <i class="fas fa-envelope"></i> NOTIFICAR POR EMAIL
                    </label>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
