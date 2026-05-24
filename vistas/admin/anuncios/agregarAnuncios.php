<?php
session_start();

$errores = $_SESSION['errores'] ?? null;
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

    <?php if ($errores) { ?>
        <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

    <div class="panel">
        <form method="POST" action="../../../controladores/admin/anuncios/insertar.php" class="formulario">
            <div class="campo">
                <label for="tituloAnuncio">TÍTULO DEL ANUNCIO</label>
                <input type="text" id="tituloAnuncio" name="tituloAnuncio" value="<?= $datos['tituloAnuncio'] ?? '' ?>" placeholder="Ej: Mantenimiento de la plataforma">
                
            </div>

            <div class="campo">
                <label for="dirigidoA">DIRIGIDO A </label>
                <select id="dirigidoA" name="dirigidoA">
                    <option value="todos" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'todos') ? 'selected' : '' ?>>Todos los usuarios</option>
                    <option value="estudiantes" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'estudiantes') ? 'selected' : '' ?>>Solo Estudiantes</option>
                    <option value="profesores" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'profesores') ? 'selected' : '' ?>>Solo Profesores</option>
                </select>
            </div>

            <div class="campo">
                <label for="contenidoAnuncio">CONTENIDO DEL ANUNCIO</label>
                <textarea id="contenidoAnuncio" name="contenidoAnuncio" rows="6" placeholder="Escriba aquí el mensaje..."><?= $datos['contenidoAnuncio'] ?? '' ?></textarea>
                
            </div>

            <div class="acciones">
                <input type="submit" name="guardarAnuncio" class="boton-primario" value="PUBLICAR ANUNCIO">
                <input type="reset" class="boton-secundario" value="LIMPIAR">
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
