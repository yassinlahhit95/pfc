<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_aula'] ?? [];
unset($_SESSION['datos_aula']);

$titulo_pagina = "AULAPRO | NUEVA AULA";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVA AULA</h1>
    <a href="gestionAulas.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form method="POST" action="../../../controladores/admin/aulas/insertar.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'planta') ?>">
                    <label for="planta">Planta</label>
                    <select name="planta" id="planta">
                        <?php for ($planta = 0; $planta <= 5; $planta++) { ?>
                            <option value="<?= $planta ?>" <?= (($datos['planta'] ?? '1') == $planta) ? 'selected' : '' ?>>
                                <?= $planta === 0 ? 'Planta Baja (0)' : 'Planta ' . $planta ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?= fieldError($errores, 'planta') ?>
                </div>
                <div class="campo<?= fieldClass($errores, 'numero') ?>">
                    <label for="numero">Número de aula (en la planta)</label>
                    <input type="number" name="numero" id="numero" min="1" max="99" value="<?= Security::escapeHtml($datos['numero'] ?? '') ?>" placeholder="Ej: 1">
                    <?= fieldError($errores, 'numero') ?>
                </div>
                <div class="campo">
                    <label for="codigoPreview">Código resultante</label>
                    <input type="text" id="codigoPreview" value="" readonly class="bg-gris-suave">
                </div>
            </div>

            <div class="campo ancho-total">
                <label for="nombreAula">Nombre / descripción (opcional)</label>
                <input type="text" name="nombreAula" id="nombreAula" value="<?= Security::escapeHtml($datos['nombreAula'] ?? '') ?>" placeholder="Ej: Laboratorio de Redes">
            </div>

            <div class="form-fila">
                <div class="campo">
                    <label for="tipoAula">Tipo</label>
                    <select name="tipoAula" id="tipoAula">
                        <?php
                        $tipos = ['teoria' => 'Teoría', 'laboratorio' => 'Laboratorio', 'taller' => 'Taller', 'otro' => 'Otro'];
                        foreach ($tipos as $valorTipo => $nombreTipo) { ?>
                            <option value="<?= $valorTipo ?>" <?= (($datos['tipoAula'] ?? 'teoria') == $valorTipo) ? 'selected' : '' ?>><?= $nombreTipo ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="campo">
                    <label for="capacidad">Capacidad (opcional)</label>
                    <input type="number" name="capacidad" id="capacidad" min="1" value="<?= Security::escapeHtml($datos['capacidad'] ?? '') ?>" placeholder="Ej: 30">
                </div>
            </div>

            <div class="campo-checkbox-grupo">
                <label class="campo-checkbox">
                    <input type="checkbox" name="activa" value="1" <?= (!isset($datos['activa']) || $datos['activa']) ? 'checked' : '' ?>>
                    Aula activa (disponible para asignar en el horario)
                </label>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarAula" class="boton-primario" value="GUARDAR AULA">
            <a href="gestionAulas.php" class="boton-secundario">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
(function () {
    function pad(numero) { numero = String(numero); return numero.length < 2 ? '0' + numero : numero; }
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
