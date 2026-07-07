<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    header("Location: calificacionesTFG.php");
    exit;
}

$calificacion = obtenerCalificacionTFG($idEstudiante);

$titulo_pagina = "AULAPRO | EVALUAR TFG";
$seccion = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUAR TFG</h1>
    <a href="calificacionesTFG.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<style>
    /* Modern UI - Glassmorphism & Animations */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.05);
        --glass-border: rgba(255, 255, 255, 0.1);
        --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    body.tema-oscuro {
        --glass-bg: rgba(30, 30, 35, 0.4);
        --glass-border: rgba(255, 255, 255, 0.05);
        --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }

    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        box-shadow: var(--glass-shadow);
        border-radius: 16px;
        padding: 25px;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    
    .glass-panel:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.15);
    }
    
    .glass-panel::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--accent), #8b5cf6);
        opacity: 0;
        transition: var(--transition);
    }
    
    .glass-panel:hover::before {
        opacity: 1;
    }

    .glass-card {
        background: var(--bg-body);
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 15px;
        border: 1px solid var(--border-1);
        transition: var(--transition);
    }
    
    .glass-card:hover {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.02);
    }
    
    .modern-input {
        background: var(--bg-body) !important;
        border: 2px solid var(--border-1) !important;
        border-radius: 12px !important;
        padding: 15px !important;
        transition: var(--transition) !important;
        font-size: 1.1rem !important;
    }
    
    .modern-input:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
        transform: translateY(-2px);
    }

    .btn-animado {
        position: relative;
        overflow: hidden;
        border-radius: 12px !important;
        transition: var(--transition) !important;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .btn-animado:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 20px -10px var(--accent);
    }
    
    .btn-animado::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        width: 300px; height: 300px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%) scale(0);
        transition: transform 0.5s ease-out;
    }
    
    .btn-animado:active::after {
        transform: translate(-50%, -50%) scale(1);
        transition: 0s;
    }

    .avatar-glow {
        box-shadow: 0 0 20px rgba(var(--accent-rgb), 0.3);
        border: 2px solid var(--bg-body);
    }
</style>

<div class="grid-2col">
    <!-- Panel Izquierdo: Info del alumno -->
    <div class="glass-panel">
        <div style="display:flex; align-items:center; gap:20px; margin-bottom: 25px;">
            <div class="cw-ava cw-ava-alumno avatar-glow" style="width:70px;height:70px;font-size:1.8rem;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(135deg, var(--accent), #8b5cf6);color:#fff;">
                <?= substr(Security::escapeHtml($estudiante['nombreEstudiante']), 0, 2) ?>
            </div>
            <div>
                <h2 style="margin:0;font-size:1.4rem; font-weight:700; background: linear-gradient(90deg, var(--text-color), var(--dim)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
                </h2>
                <div class="texto-suave" style="font-size: 0.95rem; margin-top: 4px; display:flex; align-items:center; gap:5px;">
                    <i class="fas fa-graduation-cap" style="color:var(--accent)"></i> <?= Security::escapeHtml($estudiante['nombreCiclo']) ?>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div style="font-size:0.75rem; color:var(--dim); margin-bottom:10px; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Estado del Proyecto</div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <div style="display:flex; align-items:center; justify-content:space-between; gap:15px; flex-wrap:wrap;">
                    <div>
                        <span class="indicador-estado activo-verde" style="font-size:0.9rem; padding: 6px 12px; border-radius: 20px; background:rgba(16, 185, 129, 0.1); border:1px solid rgba(16, 185, 129, 0.2);">
                            <i class="fas fa-check-circle" style="animation: pulse 2s infinite;"></i> ENTREGADO
                        </span>
                        <?php if (!empty($estudiante['tituloTFG'])): ?>
                            <div class="texto-suave" style="margin-top:10px; font-style:italic; font-size:0.95rem; border-left: 3px solid var(--accent); padding-left:10px;">
                                <i class="fas fa-quote-left" style="opacity:0.5; margin-right:5px;"></i> <?= Security::escapeHtml($estudiante['tituloTFG']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="../../../controladores/comunes/verTFG.php?id=<?= Security::escapeHtml($estudiante['idEstudiante'] ) ?>&modo=descarga" target="_blank" class="boton-secundario btn-animado" style="border-radius: 20px; background:var(--bg-body);">
                        <i class="fas fa-file-pdf" style="color: #ef4444;"></i> Ver PDF
                    </a>
                </div>
            <?php } else { ?>
                <span class="indicador-estado inactivo-rojo" style="font-size:0.9rem; padding: 6px 12px; border-radius: 20px; background:rgba(239, 68, 68, 0.1); border:1px solid rgba(239, 68, 68, 0.2);">
                    <i class="fas fa-times-circle"></i> NO ENTREGADO
                </span>
            <?php } ?>
        </div>

        <?php if (!empty($calificacion)) { ?>
        <div class="glass-card" style="display:flex; align-items:center; justify-content:space-between; background: linear-gradient(135deg, rgba(var(--accent-rgb),0.05), transparent);">
            <div style="font-size:0.85rem; color:var(--dim); text-transform:uppercase; letter-spacing:1px; font-weight:600;">Calificación Actual</div>
            <div class="texto-negrita <?= $calificacion['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>" style="font-size:2rem; text-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <?= $calificacion['nota'] ?>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Panel Derecho: Formulario de Calificación -->
    <div class="glass-panel">
        <h3 style="margin-top:0; margin-bottom:25px; font-size:1.2rem; color:var(--accent); display:flex; align-items:center; gap:10px;">
            <div style="width: 35px; height: 35px; border-radius: 8px; background: rgba(var(--accent-rgb), 0.1); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-edit"></i>
            </div>
            Formulario de Calificación
        </h3>
        <form action="../../../controladores/admin/pfc/calificar.php" method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" value="<?= (int)$idEstudiante ?>">

            <div class="campo" style="margin-bottom: 25px;">
                <label for="nota" style="font-weight:600; font-size: 0.95rem; margin-bottom:8px; display:block;">Nota Definitiva (0 - 10)</label>
                <div style="position:relative;">
                    <input type="number" step="0.01" min="0" max="10" id="nota" name="nota" value="<?= Security::escapeHtml($calificacion['nota'] ?? '') ?>" placeholder="Ej: 7.5" class="modern-input" style="font-size:1.6rem; font-weight:bold; color:var(--accent); width:100%; text-align:center; letter-spacing:1px;">
                    <i class="fas fa-star" style="position:absolute; right:20px; top:22px; color:var(--accent); font-size:1.2rem; opacity:0.5;"></i>
                </div>
                <div class="texto-suave" style="font-size:0.8rem; margin-top:8px; display:flex; align-items:center; gap:5px;"><i class="fas fa-info-circle" style="color:var(--accent)"></i> Dejar en blanco para retirar la calificación.</div>
            </div>

            <div class="campo" style="margin-bottom: 25px;">
                <label for="observaciones" style="font-weight:600; font-size: 0.95rem; margin-bottom:8px; display:block;">Observaciones / Feedback</label>
                <textarea id="observaciones" name="observaciones" rows="4" placeholder="Comentarios constructivos para el alumno..." class="modern-input" style="width:100%; resize:vertical;"><?= Security::escapeHtml($calificacion['observaciones'] ?? '') ?></textarea>
            </div>

            <div class="campo ancho-total" style="margin-bottom: 30px;">
                <label class="notif-bloque glass-card" style="margin:0; cursor:pointer; padding: 15px 20px;">
                    <input type="checkbox" name="notificarEstudiante" value="1" checked style="display:none;">
                    <div class="notif-bloque-icono" style="background: rgba(var(--accent-rgb),0.1); color:var(--accent);"><i class="fas fa-bell"></i></div>
                    <div class="notif-bloque-texto">
                        <div class="notif-bloque-titulo" style="font-weight:600;">Notificar al estudiante</div>
                        <div class="notif-bloque-canales" style="margin-top:5px;">
                            <span class="notif-canal" style="background:rgba(0,0,0,0.05); padding:2px 8px; border-radius:12px; font-size:0.75rem;"><i class="fas fa-envelope"></i> Email</span>
                            <span class="notif-canal" style="background:rgba(0,0,0,0.05); padding:2px 8px; border-radius:12px; font-size:0.75rem;"><i class="fas fa-mobile-alt"></i> Push</span>
                        </div>
                    </div>
                    <div class="notif-bloque-chk" style="width:24px; height:24px; border:2px solid var(--border-1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:transparent; transition:var(--transition);"><i class="fas fa-check" style="font-size:0.7rem;"></i></div>
                </label>
                <style>
                    input:checked ~ .notif-bloque-chk { background: var(--accent); border-color: var(--accent) !important; color: white !important; }
                    input:checked ~ .notif-bloque-icono { background: var(--accent) !important; color: white !important; }
                </style>
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" name="calificarTFG" class="boton-primario btn-animado" style="padding:14px 30px; font-size:1.1rem; width:100%; display:flex; justify-content:center; align-items:center; gap:10px; background: linear-gradient(135deg, var(--accent), #8b5cf6); border:none;">
                    <i class="fas fa-save"></i> Guardar Calificación
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

