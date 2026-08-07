<?php
// ══════════════════════════════════════════════════════════════════════
// Cuerpo compartido de vistas/{admin,secretaria}/planificacion/planificacion.php
// El wrapper de cada rol ya resolvió el Guard y el nav, y debe definir
// $tareas y $rolBase ('admin' | 'secretaria') antes de incluir este archivo.
// ══════════════════════════════════════════════════════════════════════
$pendientes = array_filter($tareas, fn($t) => !(int)$t['completada']);
$completadas = array_filter($tareas, fn($t) => (int)$t['completada']);
?>

<div class="cabecera">
    <h1>PLANIFICACIÓN DEL CENTRO</h1>
    <p class="subtitulo-encabezado">Cuaderno compartido entre dirección y secretaría — lo que el centro tiene pendiente hacer.</p>
</div>

<style>
.plan-item { display:flex; align-items:center; gap:14px; padding:13px 18px; border-bottom:1px solid var(--border); transition:background .12s; }
.plan-item:last-child { border-bottom:none; }
.plan-item:hover { background:var(--surface-2); }
.plan-item-texto { flex:1; font-size:14px; color:var(--text); line-height:1.4; }
.plan-item.completada .plan-item-texto { text-decoration:line-through; color:var(--mut); }
.plan-item-meta { font-size:11.5px; color:var(--mut); white-space:nowrap; }

.plan-add-form {
    display:flex; align-items:center; gap:10px;
    padding:6px 8px 6px 18px; background:var(--surface-2); border:1.5px solid var(--border);
    border-radius:14px; transition:border-color .15s, box-shadow .15s;
}
.plan-add-form:focus-within {
    border-color:var(--accent);
    box-shadow:0 0 0 3px color-mix(in srgb, var(--accent) 16%, transparent);
}
.plan-add-form i.fa-lightbulb { color:var(--mut); font-size:.85rem; }
.plan-add-form input[type="text"] {
    flex:1; border:none; background:transparent; outline:none;
    font-size:14.5px; color:var(--text); padding:12px 0;
}
.plan-add-form input[type="text"]::placeholder { color:var(--mut); }
.plan-add-form .boton-primario { border-radius:9px; padding:10px 18px; }

/* Checkbox circular — mismo patrón que el widget del dashboard */
.plan-check { position:relative; display:inline-flex; flex-shrink:0; width:21px; height:21px; cursor:pointer; }
.plan-check input { position:absolute; opacity:0; width:100%; height:100%; margin:0; cursor:pointer; }
.plan-checkmark {
    position:absolute; inset:0; border-radius:50%;
    border:1.5px solid var(--border-2, var(--border)); background:var(--surface);
    transition:background .15s, border-color .15s;
}
.plan-checkmark::after {
    content:''; position:absolute; left:7px; top:3px; width:5px; height:10px;
    border:solid #fff; border-width:0 2px 2px 0; transform:rotate(45deg);
    opacity:0; transition:opacity .1s;
}
.plan-check input:checked + .plan-checkmark { background:var(--accent); border-color:var(--accent); }
.plan-check input:checked + .plan-checkmark::after { opacity:1; }
</style>

<div class="panel margen-abajo">
    <form method="POST" action="../../../controladores/<?= $rolBase ?>/planificacion/insertar.php" class="plan-add-form">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <i class="fas fa-lightbulb"></i>
        <input type="text" name="texto" maxlength="500" required placeholder="Qué hay que hacer…"
               autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
               data-lpignore="true" data-1p-ignore="true" data-form-type="other">
        <button type="submit" class="boton-primario"><i class="fas fa-plus"></i> Añadir</button>
    </form>
</div>

<div class="panel margen-abajo">
    <div class="titulo-tarjeta">
        <h3>Pendientes (<?= count($pendientes) ?>)</h3>
    </div>
    <?php if (empty($pendientes)): ?>
        <div class="vacio" style="padding:20px;">Nada pendiente — buen trabajo.</div>
    <?php else: ?>
        <?php foreach ($pendientes as $t): ?>
            <?php $textoCorto = mb_strlen($t['texto']) > 60 ? mb_substr($t['texto'], 0, 60) . '…' : $t['texto']; ?>
            <div class="plan-item">
                <form method="POST" action="../../../controladores/<?= $rolBase ?>/planificacion/toggle.php">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="id" value="<?= (int)$t['idPlanTarea'] ?>">
                    <input type="hidden" name="completada" value="1">
                    <label class="plan-check" title="Marcar como completada">
                        <input type="checkbox" onchange="this.form.submit()">
                        <span class="plan-checkmark"></span>
                    </label>
                </form>
                <span class="plan-item-texto"><?= Security::escapeHtml($t['texto']) ?></span>
                <a href="#" data-modal-borrar
                   data-id="<?= (int)$t['idPlanTarea'] ?>"
                   data-tipo="Tarea"
                   data-nombre="<?= Security::escapeHtml($textoCorto) ?>"
                   data-url="/controladores/<?= $rolBase ?>/planificacion/borrar.php"
                   data-campo="id"
                   data-redirect="planificacion.php"
                   title="Eliminar" style="color:var(--rojo);">
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($completadas)): ?>
<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Historial — Completadas (<?= count($completadas) ?>)</h3>
    </div>
    <?php foreach ($completadas as $t): ?>
        <?php $textoCorto = mb_strlen($t['texto']) > 60 ? mb_substr($t['texto'], 0, 60) . '…' : $t['texto']; ?>
        <div class="plan-item completada">
            <form method="POST" action="../../../controladores/<?= $rolBase ?>/planificacion/toggle.php">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="id" value="<?= (int)$t['idPlanTarea'] ?>">
                <input type="hidden" name="completada" value="0">
                <label class="plan-check" title="Marcar como pendiente">
                    <input type="checkbox" checked onchange="this.form.submit()">
                    <span class="plan-checkmark"></span>
                </label>
            </form>
            <span class="plan-item-texto"><?= Security::escapeHtml($t['texto']) ?></span>
            <span class="plan-item-meta">
                <?php if ($t['fechaCompletada']): ?>
                    Completada el <?= date('d/m/Y', strtotime($t['fechaCompletada'])) ?><?= !empty($t['completadaPorNombre']) ? ' por ' . Security::escapeHtml($t['completadaPorNombre']) : '' ?>
                <?php endif; ?>
            </span>
            <a href="#" data-modal-borrar
               data-id="<?= (int)$t['idPlanTarea'] ?>"
               data-tipo="Tarea"
               data-nombre="<?= Security::escapeHtml($textoCorto) ?>"
               data-url="/controladores/<?= $rolBase ?>/planificacion/borrar.php"
               data-campo="id"
               data-redirect="planificacion.php"
               title="Eliminar" style="color:var(--rojo);">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../footer.php'; ?>
