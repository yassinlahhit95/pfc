<?php
session_start();
$titulo_pagina = "Gestión de Anuncios";
$seccion = 'anuncios';
include_once "../comunes/nav.php";

require_once "../../modelos/anuncios.php";
$listaAnuncios = listarTodosLosAnuncios();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_anuncio'] ?? [];
unset($_SESSION['exito'], $_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_anuncio']);

// Variables simples
$titulo = $datos['titulo'] ?? '';
$mensaje = $datos['mensaje'] ?? '';
$fechaExp = $datos['fecha_expiracion'] ?? date('Y-m-d', strtotime('+7 days'));
?>

<div class="encabezado-pagina">
    <div>
        <h1>Anuncios</h1>
    </div>
</div>

<?php if ($exito) { ?>
<div class="mensaje-exito">
    <p><?php echo $exito; ?></p>
</div>
<?php } ?>

<?php if ($error) { ?>
<div class="mensaje-error">
    <p><?php echo $error; ?></p>
</div>
<?php } ?>

<!-- Formulario en una fila superior -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Nuevo Anuncio</h3>
    </div>
    <form method="POST" action="controladores/anuncios/insertar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario" style="flex: 2;">
            <label>Título *</label>
            <input type="text" name="titulo" value="<?php echo $titulo; ?>" placeholder="Ej: Inicio de clases">
            <?php if (isset($errores['titulo'])) { ?>
                <p class="error-campo"><?php echo $errores['titulo']; ?></p>
            <?php } ?>
        </div>

        <div class="campo-formulario" style="flex: 3;">
            <label>Mensaje *</label>
            <input type="text" name="mensaje" value="<?php echo $mensaje; ?>" placeholder="Contenido corto...">
            <?php if (isset($errores['mensaje'])) { ?>
                <p class="error-campo"><?php echo $errores['mensaje']; ?></p>
            <?php } ?>
        </div>

        <div class="campo-formulario" style="flex: 1;">
            <label>Vence *</label>
            <input type="date" name="fecha_expiracion" value="<?php echo $fechaExp; ?>">
            <?php if (isset($errores['fecha_expiracion'])) { ?>
                <p class="error-campo"><?php echo $errores['fecha_expiracion']; ?></p>
            <?php } ?>
        </div>

        <div class="mt-25">
            <button type="submit" name="guardarAnuncio" class="boton-primario">
                <i class="fas fa-paper-plane"></i> Publicar
            </button>
        </div>
    </form>
</div>

<!-- Tabla debajo -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Historial de Comunicados</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Vence</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaAnuncios)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay anuncios publicados</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaAnuncios as $anuncio) { ?>
                    <tr>
                        <td><?php echo $anuncio['idAnuncio']; ?></td>
                        <td><strong><?php echo $anuncio['titulo']; ?></strong></td>
                        <td>
                            <div class="texto-pequeno texto-atenuado lh-1-4" style="max-width: 400px;">
                                <?php echo $anuncio['mensaje']; ?>
                            </div>
                        </td>
                        <td>
                            <?php 
                                $vence = strtotime($anuncio['fechaExpiracion']);
                                $hoy = time();
                                $claseVencido = ($vence < $hoy) ? 'inactivo-rojo' : 'activo-verde';
                            ?>
                            <span class="estado-bolita <?php echo $claseVencido; ?>">
                                <?php echo date('d/m/Y', $vence); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="controladores/anuncios/borrar.php" class="d-inline" onsubmit="return confirm('¿Eliminar anuncio?');">
                                <input type="hidden" name="idAnuncio" value="<?php echo $anuncio['idAnuncio']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar" title="Eliminar">
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
