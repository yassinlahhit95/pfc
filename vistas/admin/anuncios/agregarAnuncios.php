<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "PUBLICAR NUEVO ANUNCIO - ADMIN";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";

$error = $_SESSION['error'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_anuncio'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_anuncio']);
?>

<div class="contenedor-formulario-mediano">
    <div class="encabezado-pagina">
        <a href="gestionAnuncios.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> VOLVER
        </a>
        <h1>NUEVO ANUNCIO</h1>
    </div>

    <?php if ($error) { ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php } ?>

    <div class="tarjeta-blanca">
        <form method="POST" action="../../../controladores/admin/anuncios/insertar.php">
            <div class="formulario-cuadricula">
                <div class="campo-formulario">
                    <label>TÍTULO DEL ANUNCIO *</label>
                    <input type="text" name="tituloAnuncio" value="<?= $datos['tituloAnuncio'] ?? '' ?>" placeholder="Ej: Mantenimiento de la plataforma">
                    <?php if (isset($lista_de_errores['tituloAnuncio'])) { ?>
                        <strong class="error-campo"><?= $lista_de_errores['tituloAnuncio'] ?></strong>
                    <?php } ?>
                </div>

                <div class="campo-formulario">
                    <label>DIRIGIDO A *</label>
                    <select name="dirigidoA">
                        <option value="todos" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'todos') ? 'selected' : '' ?>>Todos los usuarios</option>
                        <option value="estudiantes" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'estudiantes') ? 'selected' : '' ?>>Solo Estudiantes</option>
                        <option value="profesores" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'profesores') ? 'selected' : '' ?>>Solo Profesores</option>
                    </select>
                </div>

                <div class="campo-formulario campo-ancho-total">
                    <label>CONTENIDO DEL ANUNCIO *</label>
                    <textarea name="contenidoAnuncio" rows="6" placeholder="Escriba aquí el mensaje..."><?= $datos['contenidoAnuncio'] ?? '' ?></textarea>
                    <?php if (isset($lista_de_errores['contenidoAnuncio'])) { ?>
                        <strong class="error-campo"><?= $lista_de_errores['contenidoAnuncio'] ?></strong>
                    <?php } ?>
                </div>
            </div>

            <div class="botones-formulario mt-20">
                <button type="submit" name="guardarAnuncio" class="boton-primario">
                    <i class="fas fa-paper-plane"></i> PUBLICAR ANUNCIO
                </button>
                <button type="button" class="boton-secundario" onclick="window.location.href = 'gestionAnuncios.php';">
                    <i class="fas fa-times"></i> CANCELAR
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

