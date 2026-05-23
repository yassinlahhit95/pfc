<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/anuncios.php";

$todos_los_anuncios = listarTodosLosAnuncios();

$titulo_pagina = "AULAPRO | GESTIÓN DE ANUNCIOS";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>ANUNCIOS DEL SISTEMA</h1>
    <a href="agregarAnuncios.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO ANUNCIO
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
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
                                    <a href="borrarAnuncio.php?id=<?= $anuncio['idAnuncio'] ?>" class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></a>
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