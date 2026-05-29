<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../include/Security.php";

$idProfesor = $_SESSION['idProfesor'];
$modulos = listarModulosDeProfesor($idProfesor);
$csrfToken = Security::generateCSRFToken();

$tituloDelPagina = 'AULAPRO | CREAR TAREA';
$seccionActual = 'aula_crear_tarea';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CREAR NUEVA TAREA</h1>
    <p class="texto-suave">Añade una tarea para tus estudiantes</p>
</div>

<?php if (empty($modulos)) { ?>
    <div class="alerta-error">
        <i class="fas fa-exclamation-triangle"></i>
        <p>No tienes módulos asignados. Contacta con administración.</p>
    </div>
<?php } else { ?>
    <form method="POST" action="../../../controladores/aula/crear_tarea.php" enctype="multipart/form-data" class="formulario-principal">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="grupo-formulario">
            <label for="idModulo">MÓDULO *</label>
            <select id="idModulo" name="idModulo" required>
                <option value="">-- Selecciona un módulo --</option>
                <?php foreach ($modulos as $modulo) { ?>
                    <option value="<?= $modulo['idModulo'] ?>"><?= htmlspecialchars($modulo['nombreModulo']) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="grupo-formulario">
            <label for="titulo">TÍTULO DE LA TAREA *</label>
            <input type="text" id="titulo" name="titulo" required placeholder="Ej: Ejercicio de JavaScript" maxlength="200">
        </div>

        <div class="grupo-formulario">
            <label for="descripcion">DESCRIPCIÓN Y INSTRUCCIONES *</label>
            <textarea id="descripcion" name="descripcion" rows="8" required placeholder="Detalla qué debe hacer el estudiante..."></textarea>
            <span class="texto-pequeño texto-suave">Incluye instrucciones claras, requisitos y criterios de evaluación</span>
        </div>

        <div class="grupo-formulario">
            <label for="archivo">ARCHIVO ADJUNTO (Opcional)</label>
            <input type="file" id="archivo" name="archivo" accept=".pdf,.doc,.docx,.zip,.rar,.txt">
            <span class="texto-pequeño texto-suave">Documentos, plantillas, etc. (Máx: 20MB)</span>
        </div>

        <div class="grupo-formulario">
            <label>
                <input type="checkbox" name="publicar" value="1" checked>
                <span><strong>Publicar inmediatamente</strong> (Los estudiantes podrán ver esta tarea)</span>
            </label>
        </div>

        <div class="grupo-botones">
            <a href="tareas.php" class="boton-secundario">CANCELAR</a>
            <button type="submit" class="boton-primario">
                <i class="fas fa-save"></i> CREAR TAREA
            </button>
        </div>
    </form>

    <div class="info-sistema">
        <h3>Consejos para Crear Tareas</h3>
        <ul>
            <li>Sé claro y específico en las instrucciones</li>
            <li>Incluye ejemplos o plantillas si es necesario</li>
            <li>Especifica el formato de entrega (PDF, ZIP, etc)</li>
            <li>Puedes crear una tarea como borrador y publicarla después</li>
            <li>Los estudiantes recibirán notificación cuando publiques</li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
