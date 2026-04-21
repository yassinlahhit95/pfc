<?php
session_start();
$titulo_pagina = "Gestión de Anuncios - Super Admin";
$seccion = 'anuncios';
include_once "../comunes/nav.php";

require_once "../../../modelos/anuncios.php";
$listaAnuncios = listarTodosLosAnuncios();

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_anuncio'])) {
    $datos = $_SESSION['datos_anuncio'];
}
unset($_SESSION['exito'], $_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_anuncio']);

// Variables simples
$titulo = '';
if (isset($datos['titulo'])) {
    $titulo = $datos['titulo'];
}

$mensaje = '';
if (isset($datos['mensaje'])) {
    $mensaje = $datos['mensaje'];
}

$fechaExp = date('Y-m-d', strtotime('+7 days'));
if (isset($datos['fecha_expiracion'])) {
    $fechaExp = $datos['fecha_expiracion'];
}
?>

<div class="encabezado-pagina">
    <div>
        <h1>Anuncios</h1>
        <p class="subtitulo-encabezado">Publique noticias y avisos para toda la comunidad</p>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><p><?php echo $exito; ?></p></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><p><?php echo $error; ?></p></div>
<?php } ?>

<!-- Formulario de creación arriba, ocupando ancho completo (row) -->
<div class="tarjeta-blanca margen-abajo">
    <div class="titulo-tarjeta"><h3>Nuevo Anuncio</h3></div>
    <form method="POST" action="/pfc/admin/controladores/anuncios/insertar.php">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Título del Aviso *</label>
                <input type="text" name="titulo" value="<?php echo $titulo; ?>" placeholder="Ej: Mantenimiento del Aula 2" required>
            </div>
            <div class="campo-formulario">
                <label>Válido hasta *</label>
                <input type="date" name="fecha_expiracion" value="<?php echo $fechaExp; ?>" required>
            </div>
        </div>
        
        <div class="campo-formulario margen-arriba">
            <label>Contenido del Mensaje *</label>
            <textarea name="mensaje" rows="4" placeholder="Escriba aquí el detalle..." required><?php echo $mensaje; ?></textarea>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarAnuncio" class="boton-primario">
                <i class="fas fa-bullhorn"></i> Publicar Aviso
            </button>
        </div>
    </form>
</div>

<!-- Listado de anuncios abajo -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta"><h3>Historial de Avisos</h3></div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Expira</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaAnuncios)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay anuncios registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaAnuncios as $anuncio) { ?>
                    <tr>
                        <td><strong><?php echo $anuncio['titulo']; ?></strong></td>
                        <td><small><?php echo substr($anuncio['mensaje'], 0, 100); ?>...</small></td>
                        <td><?php echo date('d/m/Y', strtotime($anuncio['fechaExpiracion'])); ?></td>
                        <td>
                            <form action="/pfc/admin/controladores/anuncios/borrar.php" method="POST" class="d-inline">
                                <input type="hidden" name="idAnuncio" value="<?php echo $anuncio['idAnuncio']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>