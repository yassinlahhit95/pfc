<?php
// ══════════════════════════════════════════════════════════════════════
// Widget compacto de Planificación para los dashboards de director/secretaría —
// pendientes recientes + añadir rápido, vía AJAX (nunca recarga la página desde
// el dashboard). El caller ya resolvió el Guard y debe definir $rolBase
// ('admin' | 'secretaria') y $planPendientes (listarPlanificacionPendientes())
// antes de incluir este archivo.
// ══════════════════════════════════════════════════════════════════════
?>
<div class="dash-panel" id="plan-widget">
    <div class="dash-panel-head">
        <h3><i class="fas fa-list-check" style="color:var(--accent);margin-right:6px;"></i>Planificación</h3>
        <a href="../planificacion/planificacion.php">Ver todo</a>
    </div>
    <div class="dash-panel-body">
        <form id="plan-widget-add" class="plan-widget-add">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <i class="fas fa-plus plan-widget-add-ico"></i>
            <input type="text" name="texto" id="plan-widget-input" maxlength="500" required
                   placeholder="Añadir una tarea…" class="plan-widget-add-input"
                   autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other">
            <button type="submit" class="plan-widget-add-btn" aria-label="Añadir tarea"><i class="fas fa-arrow-right"></i></button>
        </form>
        <div id="plan-widget-lista">
            <?php if (empty($planPendientes)): ?>
                <p class="empty-state" id="plan-widget-vacio">Nada pendiente — buen trabajo.</p>
            <?php else: ?>
                <?php foreach ($planPendientes as $t): ?>
                <div class="plan-widget-item" data-id="<?= (int)$t['idPlanTarea'] ?>">
                    <label class="plan-widget-check">
                        <input type="checkbox" data-plan-toggle="<?= (int)$t['idPlanTarea'] ?>">
                        <span class="plan-widget-checkmark"></span>
                    </label>
                    <span class="plan-widget-texto"><?= Security::escapeHtml($t['texto']) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* ── Añadir tarea ── */
.plan-widget-add {
    display: flex; align-items: center; gap: 8px;
    padding: 4px 6px 4px 14px; margin-bottom: 14px;
    background: var(--surface-2); border: 1.5px solid var(--border);
    border-radius: 12px; transition: border-color .15s, box-shadow .15s;
}
.plan-widget-add:focus-within {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 16%, transparent);
}
.plan-widget-add-ico { color: var(--mut); font-size: .78rem; flex-shrink: 0; }
.plan-widget-add-input {
    flex: 1; border: none; background: transparent; outline: none;
    font-size: .87rem; color: var(--text); padding: 9px 0;
}
.plan-widget-add-input::placeholder { color: var(--mut); }
.plan-widget-add-btn {
    display: flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; flex-shrink: 0; border: none; border-radius: 9px;
    background: var(--accent); color: var(--accent-ink, #fff);
    font-size: .8rem; cursor: pointer; transition: opacity .15s, transform .1s;
}
.plan-widget-add-btn:hover { opacity: .9; }
.plan-widget-add-btn:active { transform: scale(.93); }
.plan-widget-add-btn:disabled { opacity: .5; cursor: default; }

/* ── Lista de pendientes ── */
.plan-widget-item {
    display: flex; align-items: center; gap: 12px;
    padding: 9px 4px; border-bottom: 1px solid var(--border);
    font-size: .87rem; transition: background .12s;
}
.plan-widget-item:last-child { border-bottom: none; }
.plan-widget-item:hover { background: var(--surface-2); border-radius: 8px; }
.plan-widget-texto { color: var(--text); line-height: 1.35; }

/* Custom checkbox — círculo que se rellena, en vez del control nativo del navegador */
.plan-widget-check { position: relative; display: inline-flex; flex-shrink: 0; width: 19px; height: 19px; cursor: pointer; }
.plan-widget-check input { position: absolute; opacity: 0; width: 100%; height: 100%; margin: 0; cursor: pointer; }
.plan-widget-checkmark {
    position: absolute; inset: 0; border-radius: 50%;
    border: 1.5px solid var(--border-2, var(--border)); background: var(--surface);
    transition: background .15s, border-color .15s;
}
.plan-widget-checkmark::after {
    content: ''; position: absolute; left: 6px; top: 2px; width: 4px; height: 9px;
    border: solid #fff; border-width: 0 2px 2px 0; transform: rotate(45deg);
    opacity: 0; transition: opacity .1s;
}
.plan-widget-check input:checked + .plan-widget-checkmark { background: var(--accent); border-color: var(--accent); }
.plan-widget-check input:checked + .plan-widget-checkmark::after { opacity: 1; }
.plan-widget-check input:disabled + .plan-widget-checkmark { opacity: .6; cursor: default; }
</style>

<script src="<?= AssetMin::url(__DIR__, '../../../public/js/features/planificacion-widget.js') ?>" data-rol-base="<?= Security::escapeHtml($rolBase) ?>"></script>
