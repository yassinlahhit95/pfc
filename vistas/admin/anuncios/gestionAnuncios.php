<?php
session_start();
$titulo_pagina = "Gestión de Anuncios - Super Admin";
$seccion = 'anuncios';
include_once "../comunes/nav.php";

require_once "../../../modelos/anuncios.php";

$todos_los_anuncios = listarTodosLosAnuncios();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = [];
if (isset($_SESSION['datos_anuncio'])) { $datos = $_SESSION['datos_anuncio']; }

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_anuncio']);
?>

<div class="encabezado-pagina">
    <h1>Anuncios del Sistema</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Publicar Nuevo Anuncio</h3>
    </div>
    <form method="POST" action="/pfc/controladores/admin/anuncios/insertar.php">
        <div class="campo-formulario">
            <label>Título del Anuncio *</label>
            <input type="text" name="tituloAnuncio" value="<?php if(isset($datos['tituloAnuncio'])) echo $datos['tituloAnuncio']; ?>" placeholder="Ej: Mantenimiento de la plataforma">
            <?php if (isset($lista_de_errores['tituloAnuncio'])) { ?>
                <p class="error-campo"><?php echo $lista_de_errores['tituloAnuncio']; ?></p>
            <?php } ?>
        </div>

        <div class="campo-formulario margen-arriba">
            <label>Contenido del Anuncio *</label>
            <textarea name="contenidoAnuncio" rows="4" placeholder="Escriba aquí el mensaje..."><?php if(isset($datos['contenidoAnuncio'])) echo $datos['contenidoAnuncio']; ?></textarea>
            <?php if (isset($lista_de_errores['contenidoAnuncio'])) { ?>
                <p class="error-campo"><?php echo $lista_de_errores['contenidoAnuncio']; ?></p>
            <?php } ?>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarAnuncio" class="boton-primario">
                <i class="fas fa-paper-plane"></i> Publicar Anuncio
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca margen-arriba">
    <div class="titulo-tarjeta">
        <h3>Anuncios Recientes</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_anuncios)) { ?>
                    <tr><td colspan="3" class="sin-datos">No hay anuncios publicados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_anuncios as $anuncio) { ?>
                    <tr>
                        <td><strong><?php echo $anuncio['tituloAnuncio']; ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])); ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/anuncios/modificarAnuncios.php?idAnuncio=<?php echo $anuncio['idAnuncio']; ?>" class="boton-icono boton-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/pfc/controladores/admin/anuncios/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este anuncio?')">
                                    <input type="hidden" name="idAnuncio" value="<?php echo $anuncio['idAnuncio']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
