<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_landing');

require_once __DIR__ . "/../../../modelos/landing.php";
require_once __DIR__ . "/../../../include/landing/plantillas.php";
require_once __DIR__ . "/../../../modelos/configuracion.php";

$landingCfg = obtenerLandingConfig();
$plantillas = landing_plantillas();
$cfgCentro = obtenerConfiguracionCentro();
$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Configuración de su Centro — AulaPro</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Schibsted+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --accent: #4f46e5;
            --accent-glow: rgba(79, 70, 229, 0.12);
            --surface: rgba(255, 255, 255, 0.85);
            --bg: var(--surface-2);
            --text: var(--text);
            --text-muted: var(--dim);
            --border: rgba(0, 0, 0, 0.08);
            --radius: 24px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, var(--surface-2) 0%, var(--border) 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* ── Three.js Canvas Background ── */
        #three-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            pointer-events: none;
        }

        /* ── Onboarding Wrapper ── */
        .onboarding-flow {
            width: 100%;
            max-width: 840px;
            padding: 40px 24px;
            position: relative;
            z-index: 10;
        }

        .ob-container {
            width: 100%;
            position: relative;
        }

        /* Progress indicator */
        .ob-progress-wrapper {
            position: relative;
            height: 6px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 3px;
            margin-bottom: 40px;
        }
        .ob-progress-bar {
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 25%;
            background: linear-gradient(90deg, var(--accent), #6366f1);
            border-radius: 3px;
            transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .ob-steps-indicator {
            position: absolute;
            top: 50%; left: 0; right: 0;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
        }
        .ob-step-dot {
            width: 14px; height: 14px;
            border-radius: 50%;
            background: var(--border-2);
            border: 3px solid var(--surface-2);
            transition: all 0.4s ease;
        }
        .ob-step-dot.active {
            background: var(--accent);
            border-color: #fff;
            box-shadow: 0 0 16px var(--accent-glow);
            transform: scale(1.2);
        }

        /* ── Card Styles with transitions ── */
        .ob-card {
            background: var(--surface);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: var(--radius);
            padding: 60px 48px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.02);
            display: none;
            flex-direction: column;
            align-items: center;
            opacity: 0;
            transform: scale(0.96) translateY(20px);
            transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .ob-card.active {
            display: flex;
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        /* Icons & Media */
        .ob-hero-icon {
            width: 84px; height: 84px;
            background: linear-gradient(135deg, #fbbf24, var(--naranja));
            color: #fff;
            border-radius: 22px;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px;
            margin-bottom: 32px;
            box-shadow: 0 12px 36px rgba(245, 158, 11, 0.25);
            animation: obFloat 4s ease-in-out infinite;
        }
        @keyframes obFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .ob-success-badge {
            font-size: 80px;
            color: var(--verde);
            margin-bottom: 28px;
            filter: drop-shadow(0 4px 12px rgba(16, 185, 129, 0.2));
            animation: obScale 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }
        @keyframes obScale {
            from { transform: scale(0.6); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Typography */
        .ob-title {
            font-size: clamp(24px, 4vw, 36px);
            font-weight: 800;
            color: var(--text);
            margin-bottom: 16px;
            letter-spacing: -.02em;
        }
        .ob-desc {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 620px;
            margin-bottom: 36px;
        }

        /* Templates Grid */
        .ob-template-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            width: 100%;
            margin-bottom: 36px;
        }
        .ob-template-card {
            background:var(--surface);
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 18px;
            overflow: hidden;
            cursor: pointer;
            text-align: left;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .ob-template-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        }
        .ob-template-card.selected {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px var(--accent);
        }
        .ob-template-thumb {
            position: relative;
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: var(--surface-2);
            border-bottom: 1px solid rgba(0,0,0,0.04);
        }
        .ob-template-thumb img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .ob-template-card:hover .ob-template-thumb img { transform: scale(1.05); }
        .ob-template-glow {
            position: absolute;
            inset: 0; opacity: 0.08;
            mix-blend-mode: multiply;
        }
        .ob-template-info { padding: 20px; }
        .ob-template-info h3 {
            font-size: 16px; margin-bottom: 6px;
            color: var(--text);
            display: flex; align-items: center; gap: 8px;
        }
        .ob-color-dot { width: 10px; height: 10px; border-radius: 50%; }
        .ob-template-info p { font-size: 13px; color: var(--text-muted); margin: 0; line-height: 1.5; }
        .ob-template-selected-check {
            position: absolute; top: 12px; right: 12px;
            font-size: 24px; color: var(--accent);
            background:var(--surface); border-radius: 50%;
            line-height: 1; display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .ob-template-card.selected .ob-template-selected-check { display: block; }

        /* Form styling */
        .ob-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            width: 100%;
            text-align: left;
            margin-bottom: 36px;
        }
        .ob-input-group { display: flex; flex-direction: column; gap: 8px; }
        .ob-input-group.full { grid-column: span 2; }
        .ob-input-group label { font-size: 13.5px; font-weight: 600; color: var(--dim); }
        .ob-input-group input {
            background:var(--surface);
            border: 1px solid var(--border-2);
            border-radius: 12px;
            padding: 13px 16px;
            color: var(--text); font-size: 14.5px;
            transition: all 0.25s ease;
        }
        .ob-input-group input:focus {
            outline: none; border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }
        .ob-input-group input.error {
            border-color: var(--rojo);
            background-color: var(--rojo-suave);
        }
        .ob-error-text {
            color: var(--rojo);
            font-size: 13px;
            font-weight: 600;
            margin-top: -2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Buttons & Actions */
        .ob-actions {
            display: flex;
            gap: 16px;
            width: 100%;
            justify-content: center;
            margin-top: 12px;
        }
        .ob-btn-primary, .ob-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 28px;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 700;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: none;
        }
        .ob-btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
        }
        .ob-btn-primary:hover:not(:disabled) {
            transform: translateY(-1.5px);
            background: #4338ca;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }
        .ob-btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .ob-btn-secondary {
            background: var(--surface-2);
            color: var(--dim);
            border: 1px solid var(--border);
        }
        .ob-btn-secondary:hover {
            background: var(--border);
            color: var(--text);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ob-template-grid { grid-template-columns: 1fr; }
            .ob-form-grid { grid-template-columns: 1fr; }
            .ob-input-group.full { grid-column: span 1; }
            .ob-card { padding: 40px 24px; }
        }
    </style>
</head>
<body>

<!-- Three.js Canvas Background -->
<canvas id="three-canvas"></canvas>

<div class="onboarding-flow" id="onboarding-wizard">
    <div class="ob-container">
        
        <!-- Progress Bar Indicator -->
        <div class="ob-progress-wrapper">
            <div class="ob-progress-bar" id="ob-progress-bar"></div>
            <div class="ob-steps-indicator">
                <span class="ob-step-dot active" data-step="1"></span>
                <span class="ob-step-dot" data-step="2"></span>
                <span class="ob-step-dot" data-step="3"></span>
                <span class="ob-step-dot" data-step="4"></span>
            </div>
        </div>

        <!-- ── PASO 1: BIENVENIDA DE LUJO ── -->
        <div class="ob-card active" data-step="1" id="step-1">
            <div class="ob-hero-icon">
                <i class="fas fa-crown"></i>
            </div>
            <h1 class="ob-title">¡Le damos la bienvenida a AulaPro!</h1>
            <p class="ob-desc">
                Le agradecemos profundamente haber depositado su confianza en nuestro producto para la gestión de su institución educativa. 
                Hemos preparado este asistente de lujo para configurar la imagen pública y plantilla de su centro en menos de dos minutos.
            </p>
            <div class="ob-actions">
                <button type="button" class="ob-btn-primary" onclick="nextStep()">
                    Comenzar Experiencia <i class="fas fa-chevron-right" style="margin-left:8px;"></i>
                </button>
            </div>
        </div>

        <!-- ── PASO 2: SELECCIÓN DE PLANTILLA PREMIUM ── -->
        <div class="ob-card" data-step="2" id="step-2">
            <h2 class="ob-title">Seleccione su plantilla de partida</h2>
            <p class="ob-desc">
                Elija la identidad visual que mejor represente a su institución. Cada plantilla ha sido diseñada por expertos de experiencia de usuario.
            </p>
            
            <div class="ob-template-grid">
                <?php foreach ($plantillas as $slug => $p): ?>
                <div class="ob-template-card" data-slug="<?= Security::escapeHtml($slug) ?>" onclick="selectTemplate(this)">
                    <div class="ob-template-thumb">
                        <img src="/public/imagenes/landing/<?= Security::escapeHtml($p['thumbnail']) ?>" alt="">
                        <div class="ob-template-glow" style="background: <?= Security::escapeHtml($p['colorAcento']) ?>;"></div>
                    </div>
                    <div class="ob-template-info">
                        <h3>
                            <span class="ob-color-dot" style="background:<?= Security::escapeHtml($p['colorAcento']) ?>;"></span>
                            <?= Security::escapeHtml($p['nombre']) ?>
                        </h3>
                        <p><?= Security::escapeHtml($p['descripcion']) ?></p>
                    </div>
                    <div class="ob-template-selected-check">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="ob-actions">
                <button type="button" class="ob-btn-secondary" onclick="prevStep()">
                    <i class="fas fa-chevron-left" style="margin-right:8px;"></i> Atrás
                </button>
                <button type="button" class="ob-btn-primary" id="btn-next-template" disabled onclick="nextStep()">
                    Continuar <i class="fas fa-chevron-right" style="margin-left:8px;"></i>
                </button>
            </div>
        </div>

        <!-- ── PASO 3: CONFIGURACIÓN E IDENTIDAD DEL CENTRO ── -->
        <div class="ob-card" data-step="3" id="step-3">
            <h2 class="ob-title">Identidad del Centro</h2>
            <p class="ob-desc">Configure los datos de contacto principales que se mostrarán en la web pública, formularios de admisión y pie de página.</p>
            
            <form id="ob-form-identity" class="ob-form-grid" onsubmit="event.preventDefault();">
                <div class="ob-input-group">
                    <label for="nombreCentro">Nombre Oficial del Centro *</label>
                    <input type="text" id="nombreCentro" name="nombreCentro" value="<?= Security::escapeHtml($cfgCentro['nombreCentro'] ?? '') ?>" placeholder="Ej. Instituto Superior Politécnico">
                </div>
                
                <div class="ob-input-group">
                    <label for="codigoCentro">Código de Centro</label>
                    <input type="text" id="codigoCentro" name="codigoCentro" value="<?= Security::escapeHtml($cfgCentro['codigoCentro'] ?? '') ?>" placeholder="Ej. 28001234">
                </div>

                <div class="ob-input-group">
                    <label for="emailCentro">Correo Electrónico *</label>
                    <input type="text" id="emailCentro" name="emailCentro" value="<?= Security::escapeHtml($cfgCentro['emailCentro'] ?? '') ?>" placeholder="Ej. info@centro.com">
                </div>
                
                <div class="ob-input-group">
                    <label for="telefonoCentro">Móvil / Teléfono de Contacto *</label>
                    <input type="text" id="telefonoCentro" name="telefonoCentro" value="<?= Security::escapeHtml($cfgCentro['telefonoCentro'] ?? '') ?>" placeholder="Ej. +34 910 123 456">
                </div>
                
                <div class="ob-input-group">
                    <label for="direccionCentro">Dirección del Centro</label>
                    <input type="text" id="direccionCentro" value="<?= Security::escapeHtml($cfgCentro['direccionCentro'] ?? '') ?>" placeholder="Ej. Calle de la Educación 12">
                </div>

                <div class="ob-input-group">
                    <label for="cpCentro">Código Postal (CP)</label>
                    <input type="text" id="cpCentro" value="<?= Security::escapeHtml($cfgCentro['cpCentro'] ?? '') ?>" placeholder="Ej. 28001">
                </div>

                <div class="ob-input-group">
                    <label for="ciudadCentro">Ciudad</label>
                    <input type="text" id="ciudadCentro" value="<?= Security::escapeHtml($cfgCentro['ciudadCentro'] ?? '') ?>" placeholder="Ej. Madrid">
                </div>

                <div class="ob-input-group">
                    <label for="nombreDirectorFirmante">Nombre del Director Firmante</label>
                    <input type="text" id="nombreDirectorFirmante" value="<?= Security::escapeHtml($cfgCentro['nombreDirectorFirmante'] ?? '') ?>" placeholder="Ej. Dr. Juan Pérez García">
                </div>

                <div class="ob-input-group full">
                    <label for="logoCentroFile">Logo del Centro (Opcional - JPG, PNG o WEBP de Máx. 2MB)</label>
                    <div style="display:flex; align-items:center; gap:16px; margin-top:6px;">
                        <label class="ob-btn-secondary" style="margin-top:0; padding:10px 18px; border-radius:10px; cursor:pointer; font-size:13.5px;">
                            <i class="fas fa-image" style="margin-right:8px; color:var(--accent);"></i>
                            <span id="logo-filename-label">Subir Logo...</span>
                            <input type="file" id="logoCentroFile" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="handleLogoChange(this)">
                        </label>
                        <div id="logo-preview-container" style="display:none; width:48px; height:48px; border-radius:10px; border:1px solid var(--border); overflow:hidden; background:var(--surface); padding:4px;">
                            <img id="logo-preview-img" src="" style="width:100%; height:100%; object-fit:contain;">
                        </div>
                    </div>
                </div>
            </form>

            <div class="ob-actions">
                <button type="button" class="ob-btn-secondary" onclick="prevStep()">
                    <i class="fas fa-chevron-left" style="margin-right:8px;"></i> Atrás
                </button>
                <button type="button" class="ob-btn-primary" onclick="submitOnboardingForm()">
                    Finalizar Configuración <i class="fas fa-wand-magic-sparkles" style="margin-left:8px;"></i>
                </button>
            </div>
        </div>

        <!-- ── PASO 4: AGRADECIMIENTO Y ÉXITO ── -->
        <div class="ob-card" data-step="4" id="step-4">
            <div class="ob-success-badge">
                <i class="fas fa-circle-check"></i>
            </div>
            <h2 class="ob-title">¡Todo listo y configurado!</h2>
            <p class="ob-desc">
                La plantilla de su centro ha sido aplicada correctamente y el portal de preinscripción está listo para recibir admisiones. 
                Le deseamos el mayor de los éxitos en esta nueva etapa académica junto a AulaPro.
            </p>
            <div class="ob-actions">
                <a href="builder.php" class="ob-btn-primary">
                    Explorar el Constructor <i class="fas fa-compass" style="margin-left:8px;"></i>
                </a>
                <a href="../inicio/dashboard.php" class="ob-btn-secondary">
                    Ir al Panel General
                </a>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentStep = 1;
    let selectedTemplate = "";
    const csrfToken = '<?= $csrfToken ?>';

    // ── Three.js Background Implementation ──
    const canvas = document.getElementById('three-canvas');
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });

    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

    // Create moving floating particle constellation
    const count = 100;
    const geometry = new THREE.BufferGeometry();
    const positions = new Float32Array(count * 3);
    const velocities = [];

    for (let i = 0; i < count; i++) {
        positions[i * 3] = (Math.random() - 0.5) * 40;
        positions[i * 3 + 1] = (Math.random() - 0.5) * 40;
        positions[i * 3 + 2] = (Math.random() - 0.5) * 40;
        velocities.push({
            x: (Math.random() - 0.5) * 0.02,
            y: (Math.random() - 0.5) * 0.02,
            z: (Math.random() - 0.5) * 0.02
        });
    }

    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

    // Particle texture/style
    const material = new THREE.PointsMaterial({
        size: 0.35,
        color: 0x4f46e5,
        transparent: true,
        opacity: 0.45,
        blending: THREE.NormalBlending
    });

    const particles = new THREE.Points(geometry, material);
    scene.add(particles);

    camera.position.z = 20;

    // Animation Loop
    function animate() {
        requestAnimationFrame(animate);

        const positionAttr = geometry.attributes.position;
        for (let i = 0; i < count; i++) {
            positionAttr.array[i * 3] += velocities[i].x;
            positionAttr.array[i * 3 + 1] += velocities[i].y;
            positionAttr.array[i * 3 + 2] += velocities[i].z;

            // Bounce particles off borders
            if (Math.abs(positionAttr.array[i * 3]) > 20) velocities[i].x *= -1;
            if (Math.abs(positionAttr.array[i * 3 + 1]) > 20) velocities[i].y *= -1;
            if (Math.abs(positionAttr.array[i * 3 + 2]) > 20) velocities[i].z *= -1;
        }
        positionAttr.needsUpdate = true;
        
        particles.rotation.y += 0.0006;
        particles.rotation.x += 0.0003;

        renderer.render(scene, camera);
    }

    animate();

    // Window Resize Handler
    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });

    // ── Onboarding Progress & Step Functions ──
    function updateProgress() {
        const percentage = ((currentStep - 1) / 3) * 100;
        $('#ob-progress-bar').css('width', Math.max(percentage, 5) + '%');
        
        $('.ob-step-dot').removeClass('active');
        for (let i = 1; i <= currentStep; i++) {
            $(`.ob-step-dot[data-step="${i}"]`).addClass('active');
        }
    }

    function showStep(step) {
        const currentCard = $('.ob-card.active');
        const targetCard = $(`.ob-card[data-step="${step}"]`);
        
        currentCard.css({ opacity: 0, transform: 'scale(0.96) translateY(-20px)' });
        
        setTimeout(() => {
            currentCard.removeClass('active');
            targetCard.addClass('active');
            
            targetCard[0].offsetHeight; // Force layout reflow
            
            targetCard.css({ opacity: 1, transform: 'scale(1) translateY(0)' });
            currentStep = step;
            updateProgress();
        }, 300);
    }

    function nextStep() {
        if (currentStep < 4) {
            showStep(currentStep + 1);
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    }

    function selectTemplate(card) {
        $('.ob-template-card').removeClass('selected');
        $(card).addClass('selected');
        selectedTemplate = $(card).data('slug');
        $('#btn-next-template').prop('disabled', false);
    }

    function handleLogoChange(input) {
        const file = input.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('Atención', 'La imagen no debe superar los 2MB.', 'warning');
                input.value = "";
                return;
            }
            $('#logo-filename-label').text(file.name);
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logo-preview-img').attr('src', e.target.result);
                $('#logo-preview-container').fadeIn();
            }
            reader.readAsDataURL(file);
        }
    }

    function submitOnboardingForm() {
        const form = document.getElementById('ob-form-identity');
        
        // Clear previous errors
        $('.ob-error-text').remove();
        $('.ob-input-group input').removeClass('error');

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('plantilla', selectedTemplate);
        formData.append('nombreCentro', $('#nombreCentro').val());
        formData.append('emailCentro', $('#emailCentro').val());
        formData.append('telefonoCentro', $('#telefonoCentro').val());
        formData.append('ciudadCentro', $('#ciudadCentro').val());
        formData.append('codigoCentro', $('#codigoCentro').val());
        formData.append('direccionCentro', $('#direccionCentro').val());
        formData.append('cpCentro', $('#cpCentro').val());
        formData.append('nombreDirectorFirmante', $('#nombreDirectorFirmante').val());

        const logoFile = $('#logoCentroFile')[0].files[0];
        if (logoFile) {
            formData.append('logoCentro', logoFile);
        }

        const btn = $('#step-3 .ob-btn-primary');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: '../../../controladores/admin/landing/completar_onboarding.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res && res.ok) {
                    nextStep();
                } else {
                    if (res.errores) {
                        for (let campo in res.errores) {
                            let input = $('#' + campo);
                            input.addClass('error');
                            input.after('<span class="ob-error-text"><i class="fas fa-exclamation-circle"></i> ' + res.errores[campo] + '</span>');
                        }
                    } else {
                        Swal.fire('Error', res.msg || 'No se pudo guardar la configuración.', 'error');
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalText);
                Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
            }
        });
    }
</script>
</body>
</html>
