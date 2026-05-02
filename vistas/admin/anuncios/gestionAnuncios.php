<?php
session_start();
$titulo_pagina = "Gestión de Anuncios - Admin";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/anuncios.php";

$todos_los_anuncios = listarTodosLosAnuncios();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_anuncio'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_anuncio']);
?>

<div class="encabezado-pagina">
    <h1>Anuncios del Sistema</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Publicar Nuevo Anuncio</h3>
    </div>
    <form method="POST" action="../../../controladores/admin/anuncios/insertar.php">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Título del Anuncio *</label>
                <input type="text" name="tituloAnuncio" value="<?= $datos['tituloAnuncio'] ?? '' ?>" placeholder="Ej: Mantenimiento de la plataforma">
                <?php if (isset($lista_de_errores['tituloAnuncio'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['tituloAnuncio'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Dirigido a *</label>
                <select name="dirigidoA">
                    <option value="todos">Todos los usuarios</option>
                    <option value="estudiantes">Solo Estudiantes</option>
                    <option value="profesores">Solo Profesores</option>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Contenido del Anuncio *</label>
                <textarea name="contenidoAnuncio" rows="4" placeholder="Escriba aquí el mensaje..."><?= $datos['contenidoAnuncio'] ?? '' ?></textarea>
                <?php if (isset($lista_de_errores['contenidoAnuncio'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['contenidoAnuncio'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba disposicion-flexible separacion-media">
            <button type="submit" name="guardarAnuncio" class="boton-primario">
                <i class="fas fa-paper-plane"></i> Publicar Anuncio
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
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
                    <th>Contenido</th>
                    <th>Fecha y Hora</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_anuncios)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay anuncios publicados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_anuncios as $anuncio) { ?>
                    <tr>
                        <td><strong><?= $anuncio['tituloAnuncio'] ?></strong></td>
                        <td><small><?= substr($anuncio['contenidoAnuncio'], 0, 100) ?>...</small></td>
                        <td><?= date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])) ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="detallesAnuncio.php?idAnuncio=<?= $anuncio['idAnuncio'] ?>" class="btn-accion btn-ver" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="modificarAnuncios.php?idAnuncio=<?= $anuncio['idAnuncio'] ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/anuncios/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este anuncio?')">
                                    <input type="hidden" name="idAnuncio" value="<?= $anuncio['idAnuncio'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
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


