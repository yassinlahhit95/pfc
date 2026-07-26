<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$todasLasTareas = [];

// Get all tasks and check entregas efficiently
$db = obtenerConexion();
$sql = "
    SELECT t.*, m.nombreModulo, p.nombreProfesor, ent.idEntrega, ent.nota, k.estado AS kanban_estado
    FROM aula_tareas t
    JOIN modulos m ON t.idModulo = m.idModulo
    JOIN profesores p ON t.idProfesor = p.idProfesor
    LEFT JOIN aula_entregas ent ON t.idTarea = ent.idTarea AND ent.idEstudiante = ?
    LEFT JOIN aula_kanban_estado k ON t.idTarea = k.idTarea AND k.idEstudiante = ?
    WHERE m.idCiclo = ? AND t.publicado = 1
    ORDER BY t.fechaCreacion DESC
";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "iii", $idEstudiante, $idEstudiante, $idCiclo);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$tareas = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $tareas[] = $row;
    }
}

$colDisponibles = [];
$colProgreso = [];
$colEntregadas = [];
$colCalificadas = [];

foreach ($tareas as $tarea) {
    if ($tarea['idEntrega']) {
        if ($tarea['nota'] !== null) {
            $colCalificadas[] = $tarea;
        } else {
            $colEntregadas[] = $tarea;
        }
    } else {
        if ($tarea['kanban_estado'] === 'progress') {
            $colProgreso[] = $tarea;
        } else {
            $colDisponibles[] = $tarea;
        }
    }
}

$tituloDelPagina = 'AULAPRO | TAREAS';
$seccionActual = 'aula_tareas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<style>
.kanban-board {
    display: flex;
    gap: 20px;
    align-items: flex-start;
    overflow-x: auto;
    padding-bottom: 20px;
}
.kanban-col {
    background: var(--surface-2);
    border-radius: 12px;
    padding: 16px;
    width: 300px;
    min-width: 300px;
    min-height: 200px;
    max-height: 60vh;
    display: flex;
    flex-direction: column;
    gap: 12px;
    overflow-y: auto;
}
.kanban-col h3 {
    margin: 0 0 12px 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--text);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
/* .kanban-pendientes-grid reúsa .kanban-col para el fondo/scroll pero
   necesita ganar display/width — va después a propósito (misma
   especificidad, la última declaración gana). */
.kanban-pendientes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    width: auto;
    min-width: 0;
    max-width: none;
}
.kanban-pendientes-vacio {
    grid-column: 1 / -1;
}
.kanban-card {
    background:var(--surface);
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    cursor: grab;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid var(--border);
}
.kanban-card:active {
    cursor: grabbing;
}
.kanban-card.dragging {
    opacity: 0.5;
    transform: scale(0.95);
}
.kanban-card h4 {
    margin: 0 0 8px 0;
    font-size: 1rem;
    color: var(--text);
}
.kanban-card .mod {
    font-size: 0.75rem;
    color: var(--azul);
    background: var(--azul-suave);
    padding: 2px 8px;
    border-radius: 999px;
    display: inline-block;
    margin-bottom: 8px;
}
.kanban-card p {
    font-size: 0.85rem;
    color: var(--dim);
    margin: 0 0 12px 0;
    line-height: 1.4;
}
.kanban-card .btn {
    display: block;
    text-align: center;
    background: var(--surface-2);
    color: var(--dim);
    border: 1px solid var(--border);
    padding: 6px;
    border-radius: 6px;
    font-size: 0.85rem;
    text-decoration: none;
    transition: all 0.2s;
}
.kanban-card .btn:hover {
    background: var(--border);
    color: var(--text);
}
.kanban-col.drag-over {
    background: var(--border);
    border: 2px dashed var(--mut);
}
</style>

<div class="cabecera" style="margin-bottom:24px;">
    <h1><i class="fas fa-tasks"></i> Tablero de tareas</h1>
    <p class="subtitulo-encabezado">Arrastra las tareas de "Pendientes" a "En Progreso" para organizarte mejor.</p>
</div>

<div class="panel" style="margin-bottom:20px;">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-list-check" style="color:var(--accent);"></i> Pendientes</h3>
        <span class="badge badge-azul" id="count-todo"><?= count($colDisponibles) ?></span>
    </div>
    <div class="kanban-col kanban-pendientes-grid" id="col-todo" data-status="todo">
        <?php if (empty($colDisponibles)): ?>
            <p class="texto-suave kanban-pendientes-vacio">No tienes tareas pendientes. ¡Al día!</p>
        <?php endif; ?>
        <?php foreach($colDisponibles as $tarea): ?>
            <div class="kanban-card" draggable="true" data-id="<?= $tarea['idTarea'] ?>">
                <span class="mod"><?= Security::escapeHtml($tarea['nombreModulo']) ?></span>
                <h4><?= Security::escapeHtml(substr($tarea['titulo'], 0, 40)) ?></h4>
                <p><?= Security::escapeHtml(substr(strip_tags($tarea['descripcion']), 0, 80)) ?>...</p>
                <a href="tarea_detalle.php?id=<?= $tarea['idTarea'] ?>" class="btn">Ver detalles</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="kanban-board" data-csrf="<?= Security::generateCSRFToken() ?>">
    <!-- COLUMNA: EN PROGRESO (Gestionada en BD) -->
    <div class="kanban-col" id="col-progress" data-status="progress">
        <h3>En Progreso <span class="badge badge-ambar" id="count-progress"><?= count($colProgreso) ?></span></h3>
        <?php foreach($colProgreso as $tarea): ?>
            <div class="kanban-card" draggable="true" data-id="<?= $tarea['idTarea'] ?>">
                <span class="mod"><?= Security::escapeHtml($tarea['nombreModulo']) ?></span>
                <h4><?= Security::escapeHtml(substr($tarea['titulo'], 0, 40)) ?></h4>
                <p><?= Security::escapeHtml(substr(strip_tags($tarea['descripcion']), 0, 80)) ?>...</p>
                <a href="tarea_detalle.php?id=<?= $tarea['idTarea'] ?>" class="btn">Ver detalles</a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- COLUMNA: ENTREGADAS -->
    <div class="kanban-col" id="col-submitted" data-status="submitted">
        <h3>Entregadas <span class="badge badge-indigo"><?= count($colEntregadas) ?></span></h3>
        <?php foreach($colEntregadas as $tarea): ?>
            <div class="kanban-card" style="cursor:default;">
                <span class="mod" style="background:color-mix(in oklab, var(--accent) 12%, var(--surface)); color:var(--accent);"><?= Security::escapeHtml($tarea['nombreModulo']) ?></span>
                <h4><?= Security::escapeHtml(substr($tarea['titulo'], 0, 40)) ?></h4>
                <p style="color:var(--verde); font-weight:600; font-size:0.8rem;"><i class="fas fa-check-circle"></i> Esperando corrección</p>
                <a href="tarea_detalle.php?id=<?= $tarea['idTarea'] ?>" class="btn">Revisar entrega</a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- COLUMNA: CALIFICADAS -->
    <div class="kanban-col" id="col-graded" data-status="graded">
        <h3>Calificadas <span class="badge badge-verde"><?= count($colCalificadas) ?></span></h3>
        <?php foreach($colCalificadas as $tarea): ?>
            <div class="kanban-card" style="cursor:default; opacity:0.9;">
                <span class="mod" style="background:var(--verde-suave); color:var(--verde-ink);"><?= Security::escapeHtml($tarea['nombreModulo']) ?></span>
                <h4><?= Security::escapeHtml(substr($tarea['titulo'], 0, 40)) ?></h4>
                <p style="color:var(--text); font-weight:700; font-size:1.1rem; margin-bottom:8px;">Nota: <?= number_format($tarea['nota'], 1) ?></p>
                <a href="tarea_detalle.php?id=<?= $tarea['idTarea'] ?>" class="btn">Ver feedback</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.kanban-card[draggable="true"]');
    const cols = document.querySelectorAll('.kanban-col[data-status="todo"], .kanban-col[data-status="progress"]');
    
    const colTodo = document.getElementById('col-todo');
    const colProgress = document.getElementById('col-progress');
    const countTodo = document.getElementById('count-todo');
    const countProgress = document.getElementById('count-progress');

    cards.forEach(card => {
        card.addEventListener('dragstart', () => {
            card.classList.add('dragging');
        });
        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
        });
    });

    cols.forEach(col => {
        col.addEventListener('dragover', (e) => {
            e.preventDefault();
            col.classList.add('drag-over');
        });
        col.addEventListener('dragleave', () => {
            col.classList.remove('drag-over');
        });
        col.addEventListener('drop', (e) => {
            e.preventDefault();
            col.classList.remove('drag-over');
            const dragged = document.querySelector('.dragging');
            if (dragged) {
                // If it moved to a different column
                const oldCol = dragged.closest('.kanban-col');
                if (oldCol !== col) {
                    col.appendChild(dragged);
                    const vacio = col.querySelector('.kanban-pendientes-vacio');
                    if (vacio) vacio.remove();
                    guardarEstado(dragged.dataset.id, col.dataset.status);
                    actualizarContadores();
                }
            }
        });
    });

    function guardarEstado(cardId, newStatus) {
        const data = new URLSearchParams();
        data.append('idTarea', cardId);
        data.append('estado', newStatus);
        data.append('csrf_token', document.querySelector('.kanban-board').dataset.csrf);

        fetch('../../../controladores/estudiantes/aula/ajax_kanban_estado.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: data
        }).catch(err => {
            console.error(err);
            if (window.Toast) Toast.show('No se pudo guardar el cambio. Recarga la página.', 'error');
        });
    }

    function actualizarContadores() {
        countTodo.innerText = colTodo.querySelectorAll('.kanban-card').length;
        countProgress.innerText = colProgress.querySelectorAll('.kanban-card').length;
    }
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
