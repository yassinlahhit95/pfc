<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito   = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

$idAula = (int)($_GET['id'] ?? 0);
$aula = obtenerAulaPorId($idAula);
if (!$aula) {
    $_SESSION['errores'] = "Aula no encontrada.";
    header("Location: gestionAulas.php");
    exit;
}
// Reponer datos si vuelve de un error de validacion
$datos = $_SESSION['datos_aula'] ?? $aula;
unset($_SESSION['datos_aula']);

$titulo_pagina = "AULAPRO | EDITAR AULA";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR AULA <?= Security::escapeHtml($aula['codigoAula']) ?></h1>
    <a href="gestionAulas.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml(is_array($errores) ? implode(' ', $errores) : $errores) ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/aulas/actualizar.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idAula" value="<?= Security::escapeHtml($aula['idAula']) ?>">
        <div class="formulario">
            <div class="form-fila">
                <div class="campo">
                    <label for="planta">Planta</label>
                    <select name="planta" id="planta">
                        <?php for ($p = 0; $p <= 5; $p++) { ?>
                            <option value="<?= $p ?>" <?= (($datos['planta'] ?? 1) == $p) ? 'selected' : '' ?>>
                                <?= $p === 0 ? 'Planta Baja (0)' : 'Planta ' . $p ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="campo">
                    <label for="numero">Número de aula (en la planta)</label>
                    <input type="number" name="numero" id="numero" min="1" max="99" value="<?= Security::escapeHtml($datos['numero'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label>Código resultante</label>
                    <input type="text" id="codigoPreview" value="" readonly class="bg-gris-suave">
                </div>
            </div>

            <div class="campo">
                <label for="nombreAula">Nombre / descripción (opcional)</label>
                <input type="text" name="nombreAula" id="nombreAula" value="<?= Security::escapeHtml($datos['nombreAula'] ?? '') ?>" placeholder="Ej: Laboratorio de Redes">
            </div>

            <div class="form-fila">
                <div class="campo">
                    <label for="tipoAula">Tipo</label>
                    <select name="tipoAula" id="tipoAula">
                        <?php
                        $tipos = ['teoria' => 'Teoría', 'laboratorio' => 'Laboratorio', 'taller' => 'Taller', 'otro' => 'Otro'];
                        foreach ($tipos as $val => $lbl) { ?>
                            <option value="<?= $val ?>" <?= (($datos['tipoAula'] ?? 'teoria') == $val) ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="campo">
                    <label for="capacidad">Capacidad (opcional)</label>
                    <input type="number" name="capacidad" id="capacidad" min="1" value="<?= Security::escapeHtml($datos['capacidad'] ?? '') ?>">
                </div>
            </div>

            <label class="campo-checkbox">
                <input type="checkbox" name="activa" value="1" <?= (!empty($datos['activa'])) ? 'checked' : '' ?>>
                Aula activa (disponible para asignar en el horario)
            </label>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarAula" class="boton-primario" value="GUARDAR CAMBIOS">
            <a href="gestionAulas.php" class="boton-secundario">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
(function () {
    function pad(n) { n = String(n); return n.length < 2 ? '0' + n : n; }
    function actualizar() {
        var planta = document.getElementById('planta').value;
        var numero = document.getElementById('numero').value;
        document.getElementById('codigoPreview').value = numero ? ('Aula ' + planta + pad(numero)) : '';
    }
    document.getElementById('planta').addEventListener('change', actualizar);
    document.getElementById('numero').addEventListener('input', actualizar);
    actualizar();
})();
</script>
