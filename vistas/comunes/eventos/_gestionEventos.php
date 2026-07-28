<?php
// Cuerpo compartido admin/secretaría de la gestión de eventos — ver
// vistas/admin/eventos/gestionEventos.php y vistas/secretaria/eventos/gestionEventos.php
// (wrappers finos que ponen el guard, $rolBase y su propio nav.php antes de
// hacer require de este fichero). Sigue el patrón documentado en CLAUDE.md
// ("Avoiding duplication across roles").
require_once __DIR__ . '/../../../include/AssetMin.php';

$todosLosEventos = listarTodosEventos(['solo_activos' => true]);

$badgesVisibilidad = [
    'publica'       => ['azul',   'Pública'],
    'roles'         => ['naranja','Roles'],
    'personalizado' => ['gris',   'Personalizado'],
    'privada'       => ['rojo',   'Privada'],
];
?>

<link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../../public/css/features/calendario.css') ?>">

<div class="cabecera">
    <h1>CALENDARIO DE ACTIVIDADES</h1>
    <button type="button" class="boton-primario" data-nuevo-evento>
        <i class="fas fa-plus"></i> CREAR EVENTO
    </button>
</div>

<div class="panel margen-abajo">
    <div class="campo">
        <label for="buscarEvento">Buscar evento</label>
        <input type="search" id="buscarEvento" placeholder="Título o ubicación..."
               onkeyup="filtrarTabla('buscarEvento','tablaEventos')"
               autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
               data-lpignore="true" data-1p-ignore="true" data-form-type="other">
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEventos">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Visibilidad</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todosLosEventos)) { ?>
                    <tr><td colspan="6" class="vacio">No hay eventos programados</td></tr>
                <?php } else { foreach ($todosLosEventos as $evento) {
                    $badge = $badgesVisibilidad[$evento['tipo_visibilidad'] ?? 'publica'] ?? ['gris', $evento['tipo_visibilidad'] ?? '']; ?>
                    <tr>
                        <td><b><?= Security::escapeHtml($evento['tituloEvento']) ?></b></td>
                        <td><?= Security::escapeHtml(date('d/m/Y', strtotime($evento['fechaEvento']))) ?></td>
                        <td><?= $evento['horaEvento'] ? Security::escapeHtml(date('H:i', strtotime($evento['horaEvento']))) . 'h' : '—' ?></td>
                        <td><span class="texto-estado <?= $badge[0] ?>"><?= Security::escapeHtml($badge[1]) ?></span></td>
                        <td><?= Security::escapeHtml($evento['ubicacionEvento'] ?? '') ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="#" data-editar-evento data-id="<?= (int)$evento['idEvento'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$evento['idEvento'] ?>"
                                       data-tipo="Evento"
                                       data-nombre="<?= Security::escapeHtml($evento['tituloEvento']) ?>"
                                       data-url="/controladores/<?= Security::escapeHtml($rolBase) ?>/eventos/borrar.php"
                                       data-campo="idEvento"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Crear / Editar Evento -->
<div id="modal-evento" class="modal-backdrop" role="dialog" aria-modal="true" data-rol-base="<?= Security::escapeHtml($rolBase) ?>">
    <div class="modal-caja modal-caja-evento" style="text-align:left;">
        <h3 class="modal-titulo" id="modal-evento-titulo" style="text-align:center;margin-bottom:18px;">Crear Evento</h3>
        <form id="form-evento">
            <input type="hidden" id="ev-csrf" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" id="ev-id" value="">

            <div class="formulario">
                <div class="campo ancho-total">
                    <label for="ev-titulo">Título</label>
                    <input type="text" id="ev-titulo" placeholder="Ej: Reunión de Evaluación">
                    <span class="campo-error" id="ev-titulo-error" style="display:none;"></span>
                </div>

                <div class="campo ancho-total">
                    <label for="ev-descripcion">Descripción</label>
                    <textarea id="ev-descripcion" rows="3" placeholder="Detalles del evento..."></textarea>
                </div>

                <div class="campo">
                    <label for="ev-fecha">Fecha</label>
                    <input type="date" id="ev-fecha">
                    <span class="campo-error" id="ev-fecha-error" style="display:none;"></span>
                </div>

                <div class="campo">
                    <label for="ev-hora">Hora</label>
                    <input type="time" id="ev-hora" value="10:00">
                </div>

                <div class="campo ancho-total">
                    <label for="ev-ubicacion">Ubicación</label>
                    <input type="text" id="ev-ubicacion" placeholder="Ej: Salón de Actos">
                </div>

                <div class="campo ancho-total">
                    <label>Visibilidad</label>
                    <div class="grupo-opciones">
                        <label class="opcion-radio"><input type="radio" name="ev-visibilidad" value="publica" checked> Pública (visible para todos)</label>
                        <label class="opcion-radio"><input type="radio" name="ev-visibilidad" value="roles"> Roles específicos</label>
                        <label class="opcion-radio"><input type="radio" name="ev-visibilidad" value="personalizado"> Usuarios personalizados</label>
                        <label class="opcion-radio"><input type="radio" name="ev-visibilidad" value="privada"> Privada</label>
                    </div>
                </div>

                <div class="campo ancho-total" id="ev-audiencia-roles" style="display:none;">
                    <label>Roles con acceso</label>
                    <div class="grupo-opciones">
                        <label class="opcion-check"><input type="checkbox" name="ev-rol" value="director"> Director</label>
                        <label class="opcion-check"><input type="checkbox" name="ev-rol" value="profesor"> Profesor</label>
                        <label class="opcion-check"><input type="checkbox" name="ev-rol" value="secretaria"> Secretaria</label>
                        <label class="opcion-check"><input type="checkbox" name="ev-rol" value="estudiante"> Estudiante</label>
                        <label class="opcion-check"><input type="checkbox" name="ev-rol" value="tutor"> Tutor</label>
                    </div>
                </div>

                <div class="campo ancho-total" id="ev-audiencia-personalizado" style="display:none;">
                    <label for="ev-personalizado">Usuarios (formato tipo:id, separados por coma)</label>
                    <input type="text" id="ev-personalizado" placeholder="Ej: profesor:5, estudiante:12">
                    <span class="texto-suave" style="font-size:12px;">Tipos válidos: director, profesor, secretaria, estudiante, tutor.</span>
                </div>

                <div class="campo ancho-total">
                    <label>Recordatorios</label>
                    <div class="grupo-opciones">
                        <label class="opcion-check"><input type="checkbox" name="ev-recordatorio" value="24h_antes" checked> 24 horas antes</label>
                        <label class="opcion-check"><input type="checkbox" name="ev-recordatorio" value="1h_antes"> 1 hora antes</label>
                        <label class="opcion-check"><input type="checkbox" name="ev-recordatorio" value="en_inicio"> En la hora de inicio</label>
                    </div>
                </div>
            </div>

            <div class="modal-acciones">
                <button type="button" class="boton-secundario" id="modal-evento-cancelar">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="boton-primario" id="modal-evento-guardar">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
<script src="<?= AssetMin::url(__DIR__, '../../../public/js/features/calendario.js') ?>"></script>
<script>
iniciarPaginacion('tablaEventos', 15);
</script>
