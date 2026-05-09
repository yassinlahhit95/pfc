<?php
session_start();
$titulo_pagina = "Agregar Profesor - Admin";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$listaCiclos = listarTodosLosCiclos();
$todosLosModulos = listarModulos();

$modulos_por_ciclo = [];
foreach ($todosLosModulos as $m) {
    $idC = $m['idCiclo'];
    if (!isset($modulos_por_ciclo[$idC])) {
        $modulos_por_ciclo[$idC] = [
            'nombre' => $m['nombreCiclo'],
            'modulos' => []
        ];
    }
    $modulos_por_ciclo[$idC]['modulos'][] = $m;
}

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_profesor'] ?? [];

$ciclosElegidos = (isset($datos['ciclos']) && is_array($datos['ciclos'])) ? $datos['ciclos'] : [];
$modulosElegidos = (isset($datos['modulos']) && is_array($datos['modulos'])) ? $datos['modulos'] : [];

unset($_SESSION['errores'], $_SESSION['datos_profesor']);
?>

<div class="encabezado-pagina">
    <h1>Nuevo Profesor</h1>
    <a href="../../../vistas/admin/profesores/verProfesores.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/profesores/insertar.php" method="POST">
        <div class="formulario-cuadricula" style="grid-template-columns: 1fr;">
            <div class="campo-formulario">
                <label for="nombreProfesor">Nombre Completo *</label>
                <input type="text" id="nombreProfesor" name="nombreProfesor" value="<?php if(isset($datos['nombreProfesor'])) { echo $datos['nombreProfesor']; } ?>">
                <?php if (isset($errores['nombreProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="emailProfesor">Email *</label>
                <input type="text" id="emailProfesor" name="emailProfesor" value="<?php if(isset($datos['emailProfesor'])) { echo $datos['emailProfesor']; } ?>">
                <?php if (isset($errores['emailProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['emailProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="dniProfesor">DNI *</label>
                <input type="text" id="dniProfesor" name="dniProfesor" value="<?php if(isset($datos['dniProfesor'])) { echo $datos['dniProfesor']; } ?>">
                <?php if (isset($errores['dniProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['dniProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="telefonoProfesor">Teléfono *</label>
                <input type="text" id="telefonoProfesor" name="telefonoProfesor" value="<?php if(isset($datos['telefonoProfesor'])) { echo $datos['telefonoProfesor']; } ?>">
                <?php if (isset($errores['telefonoProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="direccionProfesor">Dirección *</label>
                <input type="text" id="direccionProfesor" name="direccionProfesor" value="<?php if(isset($datos['direccionProfesor'])) { echo $datos['direccionProfesor']; } ?>">
                <?php if (isset($errores['direccionProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['direccionProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="ciudadProfesor">Ciudad *</label>
                <input type="text" id="ciudadProfesor" name="ciudadProfesor" value="<?php if(isset($datos['ciudadProfesor'])) { echo $datos['ciudadProfesor']; } ?>">
                <?php if (isset($errores['ciudadProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['ciudadProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="codigoPostalProfesor">Código Postal *</label>
                <input type="text" id="codigoPostalProfesor" name="codigoPostalProfesor" value="<?php if(isset($datos['codigoPostalProfesor'])) { echo $datos['codigoPostalProfesor']; } ?>">
                <?php if (isset($errores['codigoPostalProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['codigoPostalProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaNacimientoProfesor">Fecha de Nacimiento *</label>
                <input type="date" id="fechaNacimientoProfesor" name="fechaNacimientoProfesor" value="<?php if(isset($datos['fechaNacimientoProfesor'])) { echo $datos['fechaNacimientoProfesor']; } ?>">
                <?php if (isset($errores['fechaNacimientoProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaNacimientoProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="observacionesProfesor">Observaciones / Curriculum Vitae (Resumen)</label>
                <textarea id="observacionesProfesor" name="observacionesProfesor" rows="3"><?php if(isset($datos['observacionesProfesor'])) { echo $datos['observacionesProfesor']; } ?></textarea>
            </div>
        </div>

        <div class="cuadricula-secundaria mt-25" style="grid-template-columns: 1fr 1fr;">
            <div>
                <h4 class="margen-abajo"><i class="fas fa-layer-group"></i> 1. Seleccionar Ciclos</h4>
                <div class="lista-checkboxes scroll-vertical-200">
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="ciclos[]" value="<?= $ciclo['idCiclo'] ?>" class="check-ciclo" data-id="<?= $ciclo['idCiclo'] ?>"
                                <?php if (in_array($ciclo['idCiclo'], $ciclosElegidos)) { echo 'checked'; } ?>>
                            <span><?= $ciclo['nombreCiclo'] ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 class="margen-abajo"><i class="fas fa-book"></i> 2. Seleccionar Módulos</h4>
                <div id="contenedor-modulos-dinamico" class="lista-checkboxes scroll-vertical-400 bg-gris-suave">
                    <p id="msg-seleccionar-ciclo" class="texto-atenuado text-center p-20">
                        Seleccione primero uno o varios ciclos para ver sus módulos disponibles.
                    </p>
                    <?php foreach ($modulos_por_ciclo as $idCiclo => $grupo) { ?>
                        <div class="grupo-modulos mb-15 d-none" data-ciclo-id="<?= $idCiclo ?>">
                            <p class="texto-negrita color-primario borde-abajo-gris mb-10 pb-3">
                                <?= $grupo['nombre'] ?>
                            </p>
                            <?php foreach ($grupo['modulos'] as $mod) { ?>
                                <label class="item-checkbox pl-10">
                                    <input type="checkbox" name="modulos[]" value="<?= $mod['idModulo'] ?>"
                                        <?php if (in_array($mod['idModulo'], $modulosElegidos)) { echo 'checked'; } ?>>
                                    <span><?= $mod['nombreModulo'] ?></span>
                                </label>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="form-acciones mt-25">
            <button type="submit" name="guardarProfesor" class="boton-primario">
                <i class="fas fa-save"></i> REGISTRAR PROFESOR
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkCiclos = document.querySelectorAll('.check-ciclo');
    const msgVacio = document.getElementById('msg-seleccionar-ciclo');

    function actualizarModulos() {
        let algunoSeleccionado = false;
        
        checkCiclos.forEach(check => {
            const idCiclo = check.getAttribute('data-id');
            const grupo = document.querySelector(`.grupo-modulos[data-ciclo-id="${idCiclo}"]`);
            
            if (grupo) {
                if (check.checked) {
                    grupo.classList.remove('d-none');
                    algunoSeleccionado = true;
                } else {
                    grupo.classList.add('d-none');
                }
            }
        });

        if (msgVacio) {
            if (algunoSeleccionado) {
                msgVacio.classList.add('d-none');
            } else {
                msgVacio.classList.remove('d-none');
            }
        }
    }

    checkCiclos.forEach(check => {
        check.addEventListener('change', actualizarModulos);
    });

    actualizarModulos();
});
</script>

<?php include '../comunes/footer.php'; ?>
