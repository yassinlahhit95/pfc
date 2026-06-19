<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

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
                            <td><b><?= Security::escapeHtml($anuncio['tituloAnuncio']) ?></b></td>
                            <td><span><?= Security::escapeHtml(substr($anuncio['contenidoAnuncio'], 0, 100)) ?>...</span></td>
                            <td><?= date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])) ?></td>
                            <td>
                                <div class="recurso-menu-wrap">
                                    <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                    <div class="recurso-menu">
                                        <a class="recurso-menu-item" href="detallesAnuncio.php?idAnuncio=<?= $anuncio['idAnuncio'] ?>"><i class="fas fa-eye"></i> Ver detalles</a>
                                        <a class="recurso-menu-item" href="modificarAnuncios.php?idAnuncio=<?= $anuncio['idAnuncio'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                        <div class="recurso-menu-sep"></div>
                                        <a class="recurso-menu-item peligro" href="borrarAnuncio.php?id=<?= $anuncio['idAnuncio'] ?>" onclick="return confirm('¿Eliminar este anuncio?')"><i class="fas fa-trash"></i> Eliminar</a>
                                    </div>
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