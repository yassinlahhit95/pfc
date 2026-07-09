<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_anuncios');

$errores = $_SESSION['errores'] ?? null;
$exito   = $_SESSION['exito']   ?? '';
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_anuncio'] ?? [];

$titulo_pagina = "AULAPRO | PUBLICAR NUEVO ANUNCIO";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="contenedor-formulario-mediano">
    <div class="cabecera">
        <h1>NUEVO ANUNCIO</h1>
        <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
    </div>


    <div class="panel">
        <form method="POST" action="../../../controladores/admin/anuncios/insertar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <div class="campo<?= fieldClass($errores, 'tituloAnuncio') ?>">
                <label for="tituloAnuncio">TÍTULO DEL ANUNCIO</label>
                <input type="text" id="tituloAnuncio" name="tituloAnuncio" value="<?= Security::escapeHtml($datos['tituloAnuncio'] ?? '') ?>" placeholder="Ej: Mantenimiento de la plataforma">
                <?= fieldError($errores, 'tituloAnuncio') ?>
            </div>

            <div class="campo">
                <label for="dirigidoA">DIRIGIDO A </label>
                <select id="dirigidoA" name="dirigidoA">
                    <option value="todos" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'todos') ? 'selected' : '' ?>>Todos los usuarios</option>
                    <option value="estudiantes" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'estudiantes') ? 'selected' : '' ?>>Solo Estudiantes</option>
                    <option value="profesores" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'profesores') ? 'selected' : '' ?>>Solo Profesores</option>
                    <option value="tutores" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'tutores') ? 'selected' : '' ?>>Solo Familias</option>
                </select>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'contenidoAnuncio') ?>">
                <label for="contenidoAnuncio">CONTENIDO DEL ANUNCIO</label>
                <textarea id="contenidoAnuncio" name="contenidoAnuncio" rows="6" placeholder="Escriba aquí el mensaje..."><?= Security::escapeHtml($datos['contenidoAnuncio'] ?? '') ?></textarea>
                <?= fieldError($errores, 'contenidoAnuncio') ?>
            </div>

            <div class="acciones">
                <input type="submit" name="guardarAnuncio" class="boton-primario" value="PUBLICAR ANUNCIO">
                <input type="reset" class="boton-secundario" value="LIMPIAR">
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
