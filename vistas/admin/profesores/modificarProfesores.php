<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id_profesor = (int)($_GET['idProfesor'] ?? 0);
$profesor = obtenerProfesorPorId($id_profesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

$listaCiclos = listarTodosLosCiclos();
$todosLosModulos = listarModulos();

$datos_sesion = $_SESSION['datos_profesor'] ?? null;
if ($datos_sesion) {
    $profesor = $datos_sesion + $profesor;
    $ciclos_marcados = $datos_sesion['ciclos'] ?? [];
    $modulos_marcados = $datos_sesion['modulos'] ?? [];
} else {
    $ciclos_marcados = [];
    $ciclosBD = listarCiclosTutorizadosProfesor($id_profesor);
    foreach ($ciclosBD as $cicloItem) {
        $ciclos_marcados[] = $cicloItem['idCiclo'];
    }
    $modulos_marcados = listarIdsModulosDeProfesor($id_profesor);
}

$titulo_pagina = "AULAPRO | MODIFICAR PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MODIFICAR PROFESOR: <?= Security::escapeHtml($profesor['nombreProfesor']) ?></h1>
    </div>
    <a href="verProfesores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="panel">
    <form action="../../../controladores/admin/profesores/actualizar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idProfesor" value="<?= Security::escapeHtml($id_profesor) ?>">
        
        <div class="formulario">
            <div class="campo">
                <label for="nombreProfesor">Nombre Completo</label>
                <input type="text" id="nombreProfesor" name="nombreProfesor" value="<?= Security::escapeHtml($profesor['nombreProfesor']) ?>">
                
            </div>

            <div class="campo">
                <label for="emailProfesor">Email</label>
                <input type="email" id="emailProfesor" name="emailProfesor" value="<?= Security::escapeHtml($profesor['emailProfesor']) ?>">
                
            </div>

            <div class="campo">
                <label for="dniProfesor">DNI</label>
                <input type="text" id="dniProfesor" name="dniProfesor" value="<?= Security::escapeHtml($profesor['dniProfesor']) ?>">
                
            </div>

            <div class="campo">
                <label for="telefonoProfesor">Teléfono</label>
                <input type="text" id="telefonoProfesor" name="telefonoProfesor" value="<?= Security::escapeHtml($profesor['telefonoProfesor']) ?>">
                
            </div>

            <div class="campo ancho-total">
                <label for="direccionProfesor">Dirección</label>
                <input type="text" id="direccionProfesor" name="direccionProfesor" value="<?= Security::escapeHtml($profesor['direccionProfesor']) ?>">
            </div>

            <div class="campo">
                <label for="ciudadProfesor">Ciudad</label>
                <input type="text" id="ciudadProfesor" name="ciudadProfesor" value="<?= Security::escapeHtml($profesor['ciudadProfesor']) ?>">
            </div>

            <div class="campo">
                <label for="codigoPostalProfesor">Código Postal</label>
                <input type="text" id="codigoPostalProfesor" name="codigoPostalProfesor" value="<?= Security::escapeHtml($profesor['codigoPostalProfesor']) ?>">
            </div>

            <div class="campo">
                <label for="fechaNacimientoProfesor">Fecha de Nacimiento</label>
                <input type="date" id="fechaNacimientoProfesor" name="fechaNacimientoProfesor" value="<?= Security::escapeHtml($profesor['fechaNacimientoProfesor']) ?>">
            </div>

            <div class="campo">
                <label for="fechaAltaProfesor">Fecha de Alta (en centro)</label>
                <input type="date" id="fechaAltaProfesor" name="fechaAltaProfesor" value="<?= Security::escapeHtml($profesor['fechaAltaProfesor']) ?>">
            </div>

            <div class="campo ancho-total">
                <label for="observacionesProfesor">Observaciones / Curriculum Vitae (Resumen)</label>
                <textarea id="observacionesProfesor" name="observacionesProfesor" rows="3"><?= Security::escapeHtml($profesor['observacionesProfesor']) ?></textarea>
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 style="margin-bottom: 15px;"><i class="fas fa-layer-group"></i> 1. Seleccionar Ciclos</h4>
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
                <h4 style="margin-bottom: 15px;"><i class="fas fa-book"></i> 2. Seleccionar Módulos</h4>
                <div id="contenedor-modulos-dinamico" class="checks scroll-v400 bg-gris-suave">
                    <p id="msg-seleccionar-ciclo" class="texto-suave" style="text-align: center; padding: 20px;">
                        Seleccione primero uno o varios ciclos para ver sus módulos disponibles.
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
            <input type="submit" name="actualizarProfesor" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script src="../../../public/js/profesores-form.js"></script>

<?php include '../comunes/footer.php'; ?>
