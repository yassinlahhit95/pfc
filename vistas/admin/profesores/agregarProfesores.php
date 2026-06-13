<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$listaCiclos = listarTodosLosCiclos();
$todosLosModulos = listarModulos();

$datos = $_SESSION['datos_profesor'] ?? [];

$ciclos_marcados = $datos['ciclos'] ?? [];
$modulos_marcados = $datos['modulos'] ?? [];

$titulo_pagina = "AULAPRO | AGREGAR PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>NUEVO PROFESOR</h1>
    </div>
    <a href="../../../vistas/admin/profesores/verProfesores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/profesores/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="campo">
                <label for="nombreProfesor">Nombre Completo</label>
                <input type="text" id="nombreProfesor" name="nombreProfesor" value="<?= Security::escapeHtml($datos['nombreProfesor'] ?? '') ?>">
                
            </div>

            <div class="campo">
                <label for="emailProfesor">Email</label>
                <input type="text" id="emailProfesor" name="emailProfesor" value="<?= Security::escapeHtml($datos['emailProfesor'] ?? '') ?>">
                
            </div>

            <div class="campo">
                <label for="dniProfesor">DNI</label>
                <input type="text" id="dniProfesor" name="dniProfesor" value="<?= Security::escapeHtml($datos['dniProfesor'] ?? '') ?>">
                
            </div>

            <div class="campo">
                <label for="telefonoProfesor">Teléfono</label>
                <input type="text" id="telefonoProfesor" name="telefonoProfesor" value="<?= Security::escapeHtml($datos['telefonoProfesor'] ?? '') ?>">
                
            </div>

            <div class="campo ancho-total">
                <label for="direccionProfesor">Dirección</label>
                <input type="text" id="direccionProfesor" name="direccionProfesor" value="<?= Security::escapeHtml($datos['direccionProfesor'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="ciudadProfesor">Ciudad</label>
                <input type="text" id="ciudadProfesor" name="ciudadProfesor" value="<?= Security::escapeHtml($datos['ciudadProfesor'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="codigoPostalProfesor">Código Postal</label>
                <input type="text" id="codigoPostalProfesor" name="codigoPostalProfesor" value="<?= Security::escapeHtml($datos['codigoPostalProfesor'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="fechaNacimientoProfesor">Fecha de Nacimiento</label>
                <input type="date" id="fechaNacimientoProfesor" name="fechaNacimientoProfesor" value="<?= Security::escapeHtml($datos['fechaNacimientoProfesor'] ?? '') ?>">
            </div>

            <div class="campo ancho-total">
                <label for="observacionesProfesor">Observaciones</label>
                <textarea id="observacionesProfesor" name="observacionesProfesor" rows="3"><?= Security::escapeHtml($datos['observacionesProfesor'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 style="margin-bottom: 15px;">Seleccionar Ciclos</h4>
                <div class="checks scroll-v200">
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <label class="check-item">
                            <input type="checkbox" name="ciclos[]" value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" class="check-ciclo"
                                <?php if (in_array($ciclo['idCiclo'], $ciclos_marcados)) { echo 'checked'; } ?>>
                            <span><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 style="margin-bottom: 15px;">Seleccionar Módulos</h4>
                <div id="contenedor-modulos-dinamico" class="checks scroll-v400 bg-gris-suave">
                    <p id="msg-seleccionar-ciclo" class="texto-suave" style="text-align: center; padding: 20px;">
                        Seleccione primero un ciclo para ver sus módulos.
                    </p>

                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <div class="grupo-modulos oculto" style="margin-bottom: 15px;" id="grupo-ciclo-<?= Security::escapeHtml($ciclo['idCiclo']) ?>">
                            <p class="texto-negrita color-primario" style="margin-bottom: 5px;">
                                <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                            </p>
                            
                            <?php foreach ($todosLosModulos as $modulo) { ?>
                                <?php if ($modulo['idCiclo'] == $ciclo['idCiclo']) { ?>
                                    <label class="check-item" style="padding-left: 10px;">
                                        <input type="checkbox" name="modulos[]" value="<?= Security::escapeHtml($modulo['idModulo']) ?>"
                                            <?php if (in_array($modulo['idModulo'], $modulos_marcados)) { echo 'checked'; } ?>>
                                        <span><?= Security::escapeHtml($modulo['nombreModulo']) ?></span>
                                    </label>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="acciones" style="margin-top: 25px;">
            <input type="submit" name="guardarProfesor" class="boton-primario" value="REGISTRAR PROFESOR">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script src="../../../public/js/profesores-form.js"></script>

<?php include '../comunes/footer.php'; ?>
