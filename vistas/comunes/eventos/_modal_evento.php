<?php
// Modal de crear/editar evento — usado en vistas/comunes/eventos/_gestionEventos.php
// y en vistas/admin/inicio/dashboard.php. Requiere que $rolBase esté definido.
// El rol se obtiene de la sesión si no está definido.
$rolBase = $rolBase ?? 'admin';
?>

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
