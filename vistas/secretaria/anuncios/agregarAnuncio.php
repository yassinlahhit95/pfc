<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../include/form_helpers.php";

$datos = $_SESSION['datos_anuncio'] ?? [];
unset($_SESSION['datos_anuncio']);

$titulo_pagina = "AULAPRO | NUEVO ANUNCIO";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO ANUNCIO</h1>
    <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/anuncios/insertar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'titulo') ?>">
                <label for="titulo">Título <span style="color:var(--rojo)">*</span></label>
                <input type="text" name="titulo" id="titulo" maxlength="255"
                       placeholder="Título del anuncio"
                       value="<?= Security::escapeHtml($datos['titulo'] ?? '') ?>">
                <?= fieldError($errores, 'titulo') ?>
            </div>

            <div class="campo">
                <label for="dirigidoA">Dirigido a</label>
                <select name="dirigidoA" id="dirigidoA">
                    <?php foreach (['todos' => 'Todos', 'estudiantes' => 'Estudiantes', 'profesores' => 'Profesores', 'tutores' => 'Familias'] as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= (($datos['dirigidoA'] ?? 'todos') === $val) ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="fechaExpiracion">Fecha de expiración <span class="texto-suave">(opcional)</span></label>
                <input type="date" name="fechaExpiracion" id="fechaExpiracion"
                       value="<?= Security::escapeHtml($datos['fechaExpiracion'] ?? '') ?>">
            </div>
        </div>

        <div class="campo campo-ancho-total<?= fieldClass($errores, 'mensaje') ?>">
            <label for="mensaje">Mensaje <span style="color:var(--rojo)">*</span></label>
            <textarea name="mensaje" id="mensaje" rows="5"
                      placeholder="Contenido del anuncio..."><?= Security::escapeHtml($datos['mensaje'] ?? '') ?></textarea>
            <?= fieldError($errores, 'mensaje') ?>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> PUBLICAR ANUNCIO</button>
            <a href="gestionAnuncios.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
