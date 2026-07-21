<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
if (!$esTutor || !$idCicloTutor) {
    header("Location: lista.php"); exit;
}

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
$ciclo = obtenerCicloPorId($idCicloTutor);

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$tituloDelPagina = "AULAPRO | NUEVO MÓDULO";
$seccionActual   = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO MÓDULO</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="/controladores/profesores/modulos/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="campo ancho-total">
                <label for="cicloModuloDisabled">Ciclo</label>
                <input type="text" id="cicloModuloDisabled" value="<?= Security::escapeHtml($ciclo['nombreCiclo'] ?? '') ?>" disabled>
            </div>
            <div class="campo ancho-total">
                <label for="nombreModulo">Nombre del Módulo</label>
                <input type="text" id="nombreModulo" name="nombreModulo" required placeholder="Ej: Programación">
            </div>
            <div class="campo ancho-total">
                <label for="horasMaximas">Horas Máximas</label>
                <input type="number" id="horasMaximas" name="horasMaximas" min="1" max="2000" required placeholder="Ej: 160">
            </div>
        </div>
        <div class="acciones">
            <input type="submit" name="guardarModulo" class="boton-primario" value="CREAR MÓDULO">
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
