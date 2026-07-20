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
    <div class="formulario">
        <div class="campo">
            <label for="filtroAnuncios">BUSCAR</label>
            <input type="text" id="filtroAnuncios" placeholder="Buscar por título, destinatario o fecha..."
                   autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other">
        </div>
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
                <?php foreach ($anuncios as $anuncio): ?>
                <tr>
                    <td><?= Security::escapeHtml($anuncio['tituloAnuncio'] ?? $anuncio['titulo']) ?></td>
                    <td>
                        <?php
                        $dirigidoA = $anuncio['dirigidoA'] ?? 'todos';
                        $colores = ['todos' => 'gris', 'estudiantes' => 'azul', 'profesores' => 'verde'];
                        $colorEstado = $colores[$dirigidoA] ?? 'gris';
                        ?>
                        <span class="texto-estado <?= $colorEstado ?>"><?= Security::escapeHtml(ucfirst($dirigidoA)) ?></span>
                    </td>
                    <td><?= $anuncio['fechaExpiracion'] ? Security::escapeHtml(date('d/m/Y', strtotime($anuncio['fechaExpiracion']))) : '—' ?></td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button class="recurso-menu-btn"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="modificarAnuncio.php?id=<?= (int)$anuncio['idAnuncio'] ?>">
                                    <i class="fas fa-pen"></i> Editar
                                </a>
                                <div class="recurso-menu-sep"></div>
                                <a class="recurso-menu-item peligro" href="#"
                                   data-modal-borrar
                                   data-id="<?= (int)$anuncio['idAnuncio'] ?>"
                                   data-tipo="Anuncio"
                                   data-nombre="<?= Security::escapeHtml($anuncio['titulo']) ?>"
                                   data-url="/controladores/secretaria/anuncios/borrar.php"
                                   data-campo="idAnuncio">
                                    <i class="fas fa-trash"></i> Eliminar
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaAnuncios', 15);
// Filtrado en vivo: se ejecuta en cada pulsación
document.getElementById('filtroAnuncios').addEventListener('input', function () {
    filtrarTabla('filtroAnuncios', 'tablaAnuncios');
});
</script>
