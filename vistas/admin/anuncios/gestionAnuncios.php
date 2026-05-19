<?php
session_start();
$titulo_pagina = "AULAPRO | GESTIÓN DE ANUNCIOS";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/anuncios.php";

$todos_los_anuncios = listarTodosLosAnuncios();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="cabecera">
    <h1>ANUNCIOS DEL SISTEMA</h1>
    <a href="agregarAnuncios.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO ANUNCIO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="panel">
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
                    <tr>
                        <td colspan="4" class="vacio">No hay anuncios publicados</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_anuncios as $anuncio) { ?>
                        <tr>
                            <td><b><?= $anuncio['tituloAnuncio'] ?></b></td>
                            <td><span><?= substr($anuncio['contenidoAnuncio'], 0, 100) ?>...</span></td>
                            <td><?= date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])) ?></td>
                            <td>
                                <div class="botones-accion">
                                    <a href="detallesAnuncio.php?idAnuncio=<?= $anuncio['idAnuncio'] ?>" class="btn-accion btn-ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="modificarAnuncios.php?idAnuncio=<?= $anuncio['idAnuncio'] ?>" class="btn-accion btn-editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="../../../controladores/admin/anuncios/borrar.php" method="POST" onsubmit="return confirm('Eliminar este anuncio?')">
                                        <input type="hidden" name="idAnuncio" value="<?= $anuncio['idAnuncio'] ?>">
                                        <input type="submit" class="btn-accion btn-eliminar" value="Borrar">
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