<?php
session_start();
$titulo_pagina = "Gestión de Anuncios";
$seccion = 'anuncios';
include_once "../comunes/nav.php";

require_once "../../modelos/anuncios.php";

$modeloAnuncios = new anuncio();
$listaAnuncios = $modeloAnuncios->listarAnunciosModelo();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_anuncios'] ?? [];
unset($_SESSION['exito'], $_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_anuncios']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Tablón de Anuncios</h1>
        <p class="texto-atenuado">Comunicados del centro educativo</p>
    </div>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $exito; ?></div>
<?php } ?>
<?php if ($error != "") { ?>
    <div class="mensaje-error"><i class="fas fa-times-circle"></i> <?php echo $error; ?></div>
<?php } ?>

<div class="disposicion-flexible separacion-grande">
    <!-- Formulario -->
    <div class="tarjeta-blanca ancho-fijo-300">
        <div class="titulo-tarjeta"><h3>Nuevo Anuncio</h3></div>
        <form method="POST" action="../../controladores/anuncios/insertar.php">
            
            <div class="campo-formulario margen-abajo">
                <label>Título</label>
                <input type="text" name="titulo" placeholder="Ej: Próximos Exámenes" value="<?php echo htmlspecialchars($datos['titulo'] ?? ''); ?>">
                <?php if (isset($errores['titulo'])): ?>
                    <p style="color: red;"><?php echo $errores['titulo']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Mensaje</label>
                <textarea name="mensaje" rows="4"><?php echo htmlspecialchars($datos['mensaje'] ?? ''); ?></textarea>
                <?php if (isset($errores['mensaje'])): ?>
                    <p style="color: red;"><?php echo $errores['mensaje']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario margen-abajo">
                <label>Fecha Expiración</label>
                <input type="date" name="fecha_expiracion" value="<?php echo htmlspecialchars($datos['fecha_expiracion'] ?? date('Y-m-d')); ?>">
                <?php if (isset($errores['fecha_expiracion'])): ?>
                    <p style="color: red;"><?php echo $errores['fecha_expiracion']; ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" name="guardarAnuncio" class="boton-primario ancho-total">Publicar</button>
        </form>
    </div>

    <!-- Lista -->
    <div class="tarjeta-blanca flexible-rellenar">
        <div class="titulo-tarjeta"><h3>Historial</h3></div>
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaAnuncios)) { ?>
                        <tr><td colspan="3" class="sin-datos">No hay anuncios</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaAnuncios as $a) { 
                            $hoy = date('Y-m-d');
                            $esActivo = ($a['fechaExpiracion'] >= $hoy);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($a['titulo']); ?></strong><br>
                                <small class="texto-atenuado">Expira: <?php echo $a['fechaExpiracion']; ?></small>
                            </td>
                            <td>
                                <span class="estado-bolita <?php echo $esActivo ? 'activo-verde' : 'inactivo-rojo'; ?>">
                                    <?php echo $esActivo ? 'Activo' : 'Expirado'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="../../controladores/anuncios/borrar.php?id=<?php echo $a['idAnuncio']; ?>" 
                                   class="boton-icono boton-eliminar" 
                                   onclick="return confirm('¿Borrar este anuncio?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
