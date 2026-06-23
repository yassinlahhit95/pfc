<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/anuncios.php";

$anuncios = listarTodosLosAnuncios();

$titulo_pagina = "AULAPRO | ANUNCIOS";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>ANUNCIOS</h1>
    <a href="agregarAnuncio.php" class="boton-primario"><i class="fas fa-plus"></i> NUEVO ANUNCIO</a>
</div>

<div class="panel margen-abajo">
    <div class="filtros">
        <input type="text" id="filtroAnuncios" class="filtro-input" placeholder="Buscar anuncio...">
    </div>
</div>

<div class="panel">
    <?php if (empty($anuncios)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-bullhorn"></i></div>
        <div class="panel-vacio-titulo">No hay anuncios</div>
        <div class="panel-vacio-desc">Crea el primer anuncio para que aparezca aquí.</div>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAnuncios">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Dirigido a</th>
                    <th>Fecha expiración</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($anuncios as $an): ?>
                <tr>
                    <td><?= Security::escapeHtml($an['tituloAnuncio'] ?? $an['titulo']) ?></td>
                    <td>
                        <?php
                        $dir = $an['dirigidoA'] ?? 'todos';
                        $colores = ['todos' => 'gris', 'estudiantes' => 'azul', 'profesores' => 'verde'];
                        $col = $colores[$dir] ?? 'gris';
                        ?>
                        <span class="texto-estado <?= $col ?>"><?= Security::escapeHtml(ucfirst($dir)) ?></span>
                    </td>
                    <td><?= $an['fechaExpiracion'] ? Security::escapeHtml(date('d/m/Y', strtotime($an['fechaExpiracion']))) : '—' ?></td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="modificarAnuncio.php?id=<?= (int)$an['idAnuncio'] ?>">
                                    <i class="fas fa-pen"></i> Editar
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
filtrarTabla('filtroAnuncios', 'tablaAnuncios');
iniciarPaginacion('tablaAnuncios', 15);
</script>
