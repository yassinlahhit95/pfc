<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$anuncio = obtenerAnuncioPorId((int)($_GET['id'] ?? 0));
if (!$anuncio) {
    header("Location: gestionAnuncios.php");
    exit;
}

$datos = $_SESSION['datos_anuncio'] ?? null;
unset($_SESSION['datos_anuncio']);

$v = fn($k) => Security::escapeHtml($datos[$k] ?? $anuncio[$k] ?? '');

$titulo_pagina = "AULAPRO | EDITAR ANUNCIO";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR ANUNCIO</h1>
    <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/anuncios/actualizar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idAnuncio" value="<?= (int)$anuncio['idAnuncio'] ?>">

        <div class="campo<?= fieldClass($errores, 'titulo') ?>">
            <label for="titulo">Título <span style="color:var(--rojo)">*</span></label>
            <input type="text" name="titulo" id="titulo" maxlength="255"
                   value="<?= $v('titulo') ?>">
            <?= fieldError($errores, 'titulo') ?>
        </div>

        <div class="campo">
            <label for="dirigidoA">Dirigido a</label>
            <select name="dirigidoA" id="dirigidoA">
                <?php
                $selDir = $datos['dirigidoA'] ?? $anuncio['dirigidoA'] ?? 'todos';
                foreach (['todos' => 'Todos', 'estudiantes' => 'Estudiantes', 'profesores' => 'Profesores', 'tutores' => 'Familias'] as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= ($selDir === $val) ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="fechaExpiracion">Fecha de expiración</label>
            <input type="date" name="fechaExpiracion" id="fechaExpiracion"
                   value="<?= $v('fechaExpiracion') ?>">
        </div>

        <div class="campo campo-ancho-total<?= fieldClass($errores, 'mensaje') ?>">
            <label for="mensaje">Mensaje <span style="color:var(--rojo)">*</span></label>
            <textarea name="mensaje" id="mensaje" rows="5"><?= $v('mensaje') ?></textarea>
            <?= fieldError($errores, 'mensaje') ?>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> GUARDAR CAMBIOS</button>
            <a href="gestionAnuncios.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
