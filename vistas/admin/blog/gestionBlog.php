<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_landing');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/blog.php";

$posts = listarTodosLosPosts();

$titulo_pagina = "AULAPRO | BLOG DEL CENTRO";
$seccion = 'blog';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>BLOG DEL CENTRO</h1>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="/vistas/blog.php" target="_blank" rel="noopener" class="boton-secundario">
            <i class="fas fa-arrow-up-right-from-square"></i> VER BLOG PÚBLICO
        </a>
        <a href="agregarPost.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVA ENTRADA
        </a>
    </div>
</div>

<div class="panel margen-abajo">
    <div class="formulario">
        <div class="campo">
            <label for="filtro-blog">BUSCAR</label>
            <input type="text" id="filtro-blog" placeholder="Buscar por título, categoría o autor..." autocomplete="off">
        </div>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Entradas del blog</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tabla-blog">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Autor</th>
                    <th>Estado</th>
                    <th>Publicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)) { ?>
                    <tr>
                        <td colspan="6" class="vacio">Todavía no hay entradas. Crea la primera con «Nueva entrada».</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($posts as $post) { ?>
                        <tr>
                            <td>
                                <b><?= Security::escapeHtml($post['titulo']) ?></b>
                                <?php if ((int)$post['destacado'] === 1) { ?>
                                    <i class="fas fa-star" style="color:var(--accent);margin-left:6px;" title="Destacada"></i>
                                <?php } ?>
                            </td>
                            <td><?= $post['categoria'] !== '' ? Security::escapeHtml($post['categoria']) : '—' ?></td>
                            <td><?= $post['autor'] !== '' ? Security::escapeHtml($post['autor']) : '—' ?></td>
                            <td>
                                <?php if ((int)$post['publicado'] === 1 && strtotime($post['fechaPublicacion']) <= time()) { ?>
                                    <span class="texto-estado verde">Publicada</span>
                                <?php } elseif ((int)$post['publicado'] === 1) { ?>
                                    <span class="texto-estado azul">Programada</span>
                                <?php } else { ?>
                                    <span class="texto-estado gris">Borrador</span>
                                <?php } ?>
                            </td>
                            <td><?= $post['fechaPublicacion'] ? date('d/m/Y H:i', strtotime($post['fechaPublicacion'])) : '—' ?></td>
                            <td>
                                <div class="recurso-menu-wrap">
                                    <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                    <div class="recurso-menu">
                                        <?php if ((int)$post['publicado'] === 1) { ?>
                                        <a class="recurso-menu-item" href="/vistas/blog.php?post=<?= Security::escapeHtml($post['slug']) ?>" target="_blank" rel="noopener"><i class="fas fa-eye"></i> Ver en el blog</a>
                                        <?php } ?>
                                        <a class="recurso-menu-item" href="modificarPost.php?idPost=<?= (int)$post['idPost'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                        <div class="recurso-menu-sep"></div>
                                        <a class="recurso-menu-item peligro" href="#"
                                           data-modal-borrar
                                           data-id="<?= (int)$post['idPost'] ?>"
                                           data-tipo="Entrada del blog"
                                           data-nombre="<?= Security::escapeHtml($post['titulo']) ?>"
                                           data-extra="<?= Security::escapeHtml($post['categoria']) ?>"
                                           data-url="/controladores/admin/blog/borrar.php"
                                           data-campo="idPost"><i class="fas fa-trash"></i> Eliminar</a>
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
<script>
iniciarPaginacion('tabla-blog', 15);
document.getElementById('filtro-blog').addEventListener('input', function () {
    filtrarTabla('filtro-blog', 'tabla-blog');
});
</script>
