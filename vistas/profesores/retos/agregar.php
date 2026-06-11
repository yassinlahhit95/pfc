<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_reto'] ?? [];

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$misModulos = listarModulosDeProfesor($idProfesor);
$modulosElegidos = $datos['modulos'] ?? [];
$mapaModulosElegidos = [];
foreach ($modulosElegidos as $idM) { $mapaModulosElegidos[$idM] = true; }

$tituloDelPagina = "AULAPRO | NUEVO RETO";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO RETO</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/profesores/retos/insertar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="campo">
            <label for="nombreReto">Nombre del Reto</label>
            <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($datos['nombreReto'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="horasReto">Horas Totales</label>
            <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($datos['horasReto'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="fechaInicio">Fecha Inicio</label>
            <input type="date" name="fechaInicio" id="fechaInicio" value="<?= Security::escapeHtml($datos['fechaInicio'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="fechaFin">Fecha Fin</label>
            <input type="date" name="fechaFin" id="fechaFin" value="<?= Security::escapeHtml($datos['fechaFin'] ?? '') ?>">
        </div>

        <div class="campo">
            <label>Asociar a Módulos</label>
            <p class="texto-suave" style="margin-bottom: 10px;">Seleccione los modulos en los que se evaluare este reto.</p>
            <div class="checks scroll-v200">
                <?php if (empty($misModulos)) { ?>
                    <p class="texto-rojo">No tiene modulos asignados. No puede crear retos.</p>
                <?php } else { ?>
                    <?php foreach ($misModulos as $mod) { ?>
                        <label class="check-item" for="mod_<?= Security::escapeHtml($mod['idModulo'] ) ?>">
                            <input type="checkbox" name="modulos[]" id="mod_<?= Security::escapeHtml($mod['idModulo'] ) ?>" value="<?= Security::escapeHtml($mod['idModulo'] ) ?>" 
                                <?= Security::escapeHtml(isset($mapaModulosElegidos[$mod['idModulo']]) ? 'checked' : '') ?>>
                            <span><?= Security::escapeHtml($mod['nombreModulo'] ) ?> (<?= Security::escapeHtml($mod['abreviaturaCiclo'] ) ?>)</span>
                        </label>
                    <?php } ?>
                <?php } ?>
            </div>
            
        </div>

        <div class="acciones" style="margin-top: 20px;">
            <input type="submit" name="insertarReto" class="boton-primario" value="REGISTRAR RETO">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


