<?php
session_start();
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$id_del_estudiante = $_GET['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id_del_estudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

$estudiante = ($_SESSION['datos_estudiante'] ?? 0);

$todos_los_ciclos = listarTodosLosCiclos();

$lista_de_errores = [];
$lista_de_errores = ($_SESSION['errores'] ?? 0);

unset($_SESSION['datos_estudiante'], $_SESSION['errores']);

$titulo_pagina = "Modificar Estudiante - Admin";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Estudiante: <?= $estudiante['nombreEstudiante'] ?></h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $id_del_estudiante ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" value="<?= $estudiante['nombreEstudiante'] ?>">
                <?php if (isset($lista_de_errores['nombreEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['nombreEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailEstudiante" value="<?= $estudiante['emailEstudiante'] ?>">
                <?php if (isset($lista_de_errores['emailEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['emailEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo Formativo *</label>
                <select name="idCiclo">
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?php if ($ciclo['idCiclo'] == $estudiante['idCiclo']) { echo "selected"; } ?>>
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['idCiclo'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniEstudiante" value="<?= $estudiante['dniEstudiante'] ?>">
                <?php if (isset($lista_de_errores['dniEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['dniEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoEstudiante" value="<?= $estudiante['telefonoEstudiante'] ?>">
                <?php if (isset($lista_de_errores['telefonoEstudiante'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['telefonoEstudiante'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" value="<?= $estudiante['fechaNacimientoEstudiante'] ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección</label>
                <input type="text" name="direccionEstudiante" value="<?= $estudiante['direccionEstudiante'] ?>">
            </div>
            
            <div class="campo-formulario">
                <label>Ciudad</label>
                <input type="text" name="ciudadEstudiante" value="<?= $estudiante['ciudadEstudiante'] ?>">
            </div>

            <div class="campo-formulario">
                <label>Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" value="<?= $estudiante['codigoPostalEstudiante'] ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones</label>
                <textarea name="observacionesEstudiante"><?= $estudiante['observacionesEstudiante'] ?></textarea>
            </div>
            
            <input type="hidden" name="fechaAltaEstudiante" value="<?= $estudiante['fechaAltaEstudiante'] ?>">
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarEstudiante" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>



