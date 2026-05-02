<?php
session_start();
$titulo_pagina = "Agregar Profesor - Admin";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$listaCiclos = listarTodosLosCiclos();
$todosLosModulos = listarModulos();

// Organizar módulos por ciclo
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

$lista_de_errores = [];
$lista_de_errores = ($_SESSION['errores'] ?? 0);

$datos = [];
$datos = ($_SESSION['datos_profesor'] ?? 0);

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
        <div class="formulario-cuadricula">
            <!-- (Same profile fields as before) -->
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" value="<?php if(isset($datos['nombreProfesor'])) { echo $datos['nombreProfesor']; } ?>">
                <?php if (isset($lista_de_errores['nombreProfesor'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['nombreProfesor'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailProfesor" value="<?php if(isset($datos['emailProfesor'])) { echo $datos['emailProfesor']; } ?>">
                <?php if (isset($lista_de_errores['emailProfesor'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['emailProfesor'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniProfesor" value="<?php if(isset($datos['dniProfesor'])) { echo $datos['dniProfesor']; } ?>">
                <?php if (isset($lista_de_errores['dniProfesor'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['dniProfesor'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoProfesor" value="<?php if(isset($datos['telefonoProfesor'])) { echo $datos['telefonoProfesor']; } ?>">
                <?php if (isset($lista_de_errores['telefonoProfesor'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['telefonoProfesor'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionProfesor" value="<?php if(isset($datos['direccionProfesor'])) { echo $datos['direccionProfesor']; } ?>">
                <?php if (isset($lista_de_errores['direccionProfesor'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['direccionProfesor'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadProfesor" value="<?php if(isset($datos['ciudadProfesor'])) { echo $datos['ciudadProfesor']; } ?>">
                <?php if (isset($lista_de_errores['ciudadProfesor'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['ciudadProfesor'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalProfesor" value="<?php if(isset($datos['codigoPostalProfesor'])) { echo $datos['codigoPostalProfesor']; } ?>">
                <?php if (isset($lista_de_errores['codigoPostalProfesor'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['codigoPostalProfesor'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Nacimiento *</label>
                <input type="date" name="fechaNacimientoProfesor" value="<?php if(isset($datos['fechaNacimientoProfesor'])) { echo $datos['fechaNacimientoProfesor']; } ?>">
                <?php if (isset($lista_de_errores['fechaNacimientoProfesor'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['fechaNacimientoProfesor'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones / Curriculum Vitae (Resumen)</label>
                <textarea name="observacionesProfesor" rows="3"><?php if(isset($datos['observacionesProfesor'])) { echo $datos['observacionesProfesor']; } ?></textarea>
            </div>
        </div>

        <div class="cuadricula-secundaria mt-25">
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

        <div class="margen-arriba">
            <button type="submit" name="guardarProfesor" class="boton-primario">
                <i class="fas fa-save"></i> Registrar Profesor
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkCiclos = document.querySelectorAll('.check-ciclo');
    const gruposModulos = document.querySelectorAll('.grupo-modulos');
    const msgVacio = document.getElementById('msg-seleccionar-ciclo');

    function actualizarModulos() {
        let algunoSeleccionado = false;
        
        checkCiclos.forEach(check => {
            const idCiclo = check.getAttribute('data-id');
            const grupo = document.querySelector(`.grupo-modulos[data-ciclo-id="${idCiclo}"]`);
            
            if (grupo) {
                if (check.checked) {
                    grupo.style.display = 'block';
                    algunoSeleccionado = true;
                } else {
                    grupo.style.display = 'none';
                    // Opcional: desmarcar módulos si el ciclo se deselecciona
                    const inputsModulo = grupo.querySelectorAll('input[type="checkbox"]');
                    inputsModulo.forEach(i => i.checked = false);
                }
            }
        });

        msgVacio.style.display = algunoSeleccionado ? 'none' : 'block';
    }

    checkCiclos.forEach(check => {
        check.addEventListener('change', actualizarModulos);
    });

    // Ejecutar al cargar por si hay datos de sesión
    actualizarModulos();
});
</script>

<?php include '../comunes/footer.php'; ?>



