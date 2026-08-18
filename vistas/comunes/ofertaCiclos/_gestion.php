<?php
// ══════════════════════════════════════════════════════════════════════
// Cuerpo compartido de vistas/{admin,secretaria}/ofertaCiclos/gestion.php
// El wrapper de cada rol ya resolvió el Guard, el nav y debe definir
// $ciclos y $rolBase ('admin' | 'secretaria') antes de incluir este archivo.
// ══════════════════════════════════════════════════════════════════════
?>

<div class="cabecera">
    <h1>Catálogo de Ciclos</h1>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="/vistas/ciclos.php" target="_blank" rel="noopener" class="boton-secundario">
            <i class="fas fa-arrow-up-right-from-square"></i> VER CATÁLOGO PÚBLICO
        </a>
        <a href="agregar.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO CICLO
        </a>
    </div>
</div>

<div class="panel margen-abajo">
    <div class="formulario">
        <div class="campo ancho-total">
            <label for="filtro-ciclos">BUSCAR</label>
            <input type="text" id="filtro-ciclos" placeholder="Buscar por título o etiqueta..."
                   autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other">
        </div>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Ciclos del catálogo</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tabla-ciclos">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Etiqueta</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ciclos)) { ?>
                    <tr>
                        <td colspan="6" class="vacio">Todavía no hay ciclos en el catálogo. Crea el primero con «Nuevo ciclo».</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($ciclos as $ciclo) { ?>
                        <tr>
                            <td>
                                <b><?= Security::escapeHtml($ciclo['titulo']) ?></b>
                                <?php if ((int)$ciclo['destacado'] === 1) { ?>
                                    <i class="fas fa-star" style="color:var(--accent);margin-left:6px;" title="Destacado"></i>
                                <?php } ?>
                            </td>
                            <td><?= $ciclo['etiqueta'] !== '' ? Security::escapeHtml($ciclo['etiqueta']) : '—' ?></td>
                            <td><?= $ciclo['precio'] !== '' ? Security::escapeHtml($ciclo['precio']) : '—' ?></td>
                            <td>
                                <?php if ((int)$ciclo['publicado'] === 1) { ?>
                                    <span class="texto-estado verde">Publicado</span>
                                <?php } else { ?>
                                    <span class="texto-estado gris">Borrador</span>
                                <?php } ?>
                            </td>
                            <td><?= (int)$ciclo['orden'] ?></td>
                            <td>
                                <div class="recurso-menu-wrap">
                                    <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                    <div class="recurso-menu">
                                        <?php if ((int)$ciclo['publicado'] === 1) { ?>
                                        <a class="recurso-menu-item" href="/vistas/ciclos.php?ciclo=<?= Security::escapeHtml($ciclo['slug']) ?>" target="_blank" rel="noopener"><i class="fas fa-eye"></i> Ver ficha</a>
                                        <?php } ?>
                                        <a class="recurso-menu-item" href="modificar.php?idLandingCiclo=<?= (int)$ciclo['idLandingCiclo'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                        <div class="recurso-menu-sep"></div>
                                        <a class="recurso-menu-item peligro" href="#"
                                           data-modal-borrar
                                           data-id="<?= (int)$ciclo['idLandingCiclo'] ?>"
                                           data-tipo="Ciclo del catálogo"
                                           data-nombre="<?= Security::escapeHtml($ciclo['titulo']) ?>"
                                           data-extra="<?= Security::escapeHtml($ciclo['etiqueta']) ?>"
                                           data-url="/controladores/<?= $rolBase ?>/ofertaCiclos/borrar.php"
                                           data-campo="idLandingCiclo"><i class="fas fa-trash"></i> Eliminar</a>
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

<?php require __DIR__ . '/../footer.php'; ?>
<script>
iniciarPaginacion('tabla-ciclos', 15);
document.getElementById('filtro-ciclos').addEventListener('input', function () {
    filtrarTabla('filtro-ciclos', 'tabla-ciclos');
});
</script>
