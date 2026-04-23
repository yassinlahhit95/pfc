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
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Anuncios</h1>
        <p class="subtitulo-encabezado">Publique noticias y avisos para toda la comunidad</p>
    </div>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <div class="titulo-tarjeta"><h3>Nuevo Anuncio</h3></div>
    <form method="POST" action="/pfc/controladores/admin/anuncios/insertar.php">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Título del Aviso *</label>
                <input type="text" name="titulo" placeholder="Mantenimiento del Aula 2">
            </div>
            <div class="campo-formulario">
                <label>Válido hasta *</label>
                <input type="date" name="fecha_expiracion" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
            </div>
            <div class="campo-formulario">
                <label>Dirigido a:</label>
                <select name="dirigidoA">
                    <option value="todos">Todos</option>
                    <option value="estudiantes">Solo Estudiantes</option>
                    <option value="profesores">Solo Profesores</option>
                </select>
            </div>
        </div>
        
        <div class="campo-formulario margen-arriba">
            <label>Contenido del Mensaje *</label>
            <textarea name="mensaje" rows="4" placeholder="Escriba aquí el detalle..."></textarea>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarAnuncio" class="boton-primario">
                <i class="fas fa-bullhorn"></i> Publicar Aviso
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta"><h3>Historial de Avisos</h3></div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Dirigido a</th>
                    <th>Expira</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaAnuncios)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay anuncios registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaAnuncios as $anuncio) { ?>
                    <tr>
                        <td><strong><?php echo $anuncio['titulo']; ?></strong></td>
                        <td><small><?php echo substr($anuncio['mensaje'], 0, 80); ?>...</small></td>
                        <td><span class="etiqueta-gris"><?php echo ucfirst($anuncio['dirigidoA']); ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($anuncio['fechaExpiracion'])); ?></td>
                        <td>
                            <form action="/pfc/controladores/admin/anuncios/borrar.php" method="POST" class="d-inline">
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