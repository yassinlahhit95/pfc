<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_anuncios');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/anuncios.php";

$todosLosAnuncios = listarTodosLosAnuncios();

$titulo_pagina = "AULAPRO | GESTIÓN DE ANUNCIOS";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>ANUNCIOS DEL SISTEMA</h1>
    <button type="button" class="boton-primario" onclick="document.getElementById('modal-nuevo-anuncio').style.display='flex'">
        <i class="fas fa-plus"></i> NUEVO ANUNCIO
    </button>
</div>

<!-- Modal nuevo anuncio -->
<div id="modal-nuevo-anuncio" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:var(--bg-1,var(--surface,#fff));border-radius:14px;padding:32px;width:560px;max-width:95vw;border:1px solid var(--border);box-shadow:var(--shadow-lg);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="margin:0;"><i class="fas fa-bullhorn"></i> Nuevo Anuncio</h3>
            <button type="button" onclick="document.getElementById('modal-nuevo-anuncio').style.display='none'"
                    style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--dim);">&times;</button>
        </div>
        <form method="POST" action="../../../controladores/admin/anuncios/insertar.php" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <div class="campo ancho-total">
                <label for="tituloAnuncioModal">TÍTULO DEL ANUNCIO</label>
                <input type="text" id="tituloAnuncioModal" name="tituloAnuncio" required placeholder="Ej: Mantenimiento de la plataforma">
            </div>
            <div class="campo">
                <label for="dirigidoAModal">DIRIGIDO A</label>
                <select id="dirigidoAModal" name="dirigidoA">
                    <option value="todos">Todos los usuarios</option>
                    <option value="estudiantes">Solo Estudiantes</option>
                    <option value="profesores">Solo Profesores</option>
                    <option value="tutores">Solo Familias</option>
                </select>
            </div>
            <div class="campo ancho-total">
                <label for="contenidoAnuncioModal">CONTENIDO</label>
                <textarea id="contenidoAnuncioModal" name="contenidoAnuncio" rows="5" required placeholder="Escriba aquí el mensaje..."></textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;grid-column:1/-1;">
                <button type="submit" name="guardarAnuncio" class="boton-primario" style="flex:1;">
                    <i class="fas fa-paper-plane"></i> PUBLICAR ANUNCIO
                </button>
                <button type="button" class="boton-secundario" onclick="document.getElementById('modal-nuevo-anuncio').style.display='none'">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
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
                <?php if (empty($todosLosAnuncios)) { ?>
                    <tr>
                        <td colspan="4" class="vacio">No hay anuncios publicados</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($todosLosAnuncios as $anuncio) { ?>
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
                                        <a class="recurso-menu-item peligro" href="#"
                                           data-modal-borrar
                                           data-id="<?= (int)$anuncio['idAnuncio'] ?>"
                                           data-tipo="Anuncio"
                                           data-nombre="<?= Security::escapeHtml($anuncio['tituloAnuncio']) ?>"
                                           data-url="/controladores/admin/anuncios/borrar.php"
                                           data-campo="idAnuncio"><i class="fas fa-trash"></i> Eliminar</a>
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