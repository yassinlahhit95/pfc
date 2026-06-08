<?php
session_start();
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/niveles.php';

$ciclos  = listarTodosLosCiclos();
$niveles = listarNiveles();

$titulo_pagina = "AULAPRO | INFORMES Y DOCUMENTOS";
$seccion = 'informes';
include_once __DIR__ . '/../comunes/nav.php';
?>

<link rel="stylesheet" href="../../../public/css/informes.css?v=<?= @filemtime(__DIR__.'/../../../public/css/informes.css') ?>">

<div class="cabecera">
    <div>
        <h1>INFORMES Y DOCUMENTOS</h1>
        <p class="subtitulo">Genera documentos PDF automáticamente desde los datos del sistema</p>
    </div>
    <a href="../configuracion/configuracion.php" class="boton-secundario">
        <i class="fas fa-cog"></i> Configurar Cabecera
    </a>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div><?php endif; ?>

<?php
// JSON for nivel-cascade JS
$ciclosJson = json_encode(array_map(fn($c) => [
    'id'       => (int)$c['idCiclo'],
    'nombre'   => $c['nombreCiclo'],
    'abrev'    => $c['abreviaturaCiclo'],
    'idNivel'  => (int)$c['idNivel'],
], $ciclos), JSON_UNESCAPED_UNICODE);
?>

<div class="informes-grid">

    <!-- BOLETÍN DE NOTAS -->
    <div class="informe-card">
        <div class="informe-icono" style="background:linear-gradient(135deg,#667eea,#764ba2);">
            <i class="fas fa-file-alt"></i>
        </div>
        <h2 class="informe-titulo">Boletín de Notas</h2>
        <p class="informe-desc">Genera un boletín por alumno con todas las calificaciones por módulo (1ª y 2ª evaluación).</p>
        <form method="GET" action="../../../controladores/admin/informes/generarBoletin.php" target="_blank" class="informe-form">
            <select name="_nivel" class="informe-select nivel-select" data-target="ciclo-boletin" onchange="cascadeInforme(this)">
                <option value="">Todos los niveles</option>
                <?php foreach ($niveles as $n): ?>
                    <option value="<?= (int)$n['idNivel'] ?>"><?= Security::escapeHtml($n['nombreNivel']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="idCiclo" id="ciclo-boletin" class="informe-select" required>
                <option value="">— Seleccionar ciclo —</option>
                <?php foreach ($ciclos as $c): ?>
                    <option value="<?= (int)$c['idCiclo'] ?>">[<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>] <?= Security::escapeHtml($c['nombreCiclo']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="informe-btn">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </button>
        </form>
    </div>

    <!-- LISTADO DE ESTUDIANTES -->
    <div class="informe-card">
        <div class="informe-icono" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">
            <i class="fas fa-users"></i>
        </div>
        <h2 class="informe-titulo">Listado de Alumnado</h2>
        <p class="informe-desc">Lista completa de estudiantes de un ciclo con DNI, email y teléfono. Deja en blanco para todos.</p>
        <form method="GET" action="../../../controladores/admin/informes/generarListado.php" target="_blank" class="informe-form">
            <select name="_nivel" class="informe-select nivel-select" data-target="ciclo-listado" onchange="cascadeInforme(this)">
                <option value="">Todos los niveles</option>
                <?php foreach ($niveles as $n): ?>
                    <option value="<?= (int)$n['idNivel'] ?>"><?= Security::escapeHtml($n['nombreNivel']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="idCiclo" id="ciclo-listado" class="informe-select">
                <option value="">— Todos los ciclos —</option>
                <?php foreach ($ciclos as $c): ?>
                    <option value="<?= (int)$c['idCiclo'] ?>">[<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>] <?= Security::escapeHtml($c['nombreCiclo']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="informe-btn">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </button>
        </form>
    </div>

    <!-- HORARIO DEL CICLO -->
    <div class="informe-card">
        <div class="informe-icono" style="background:linear-gradient(135deg,#10b981,#059669);">
            <i class="fas fa-calendar-week"></i>
        </div>
        <h2 class="informe-titulo">Horario del Ciclo</h2>
        <p class="informe-desc">Cuadro horario semanal de un ciclo con módulos, profesores y aulas asignadas.</p>
        <form method="GET" action="../../../controladores/admin/informes/generarHorario.php" target="_blank" class="informe-form">
            <select name="_nivel" class="informe-select nivel-select" data-target="ciclo-horario" onchange="cascadeInforme(this)">
                <option value="">Todos los niveles</option>
                <?php foreach ($niveles as $n): ?>
                    <option value="<?= (int)$n['idNivel'] ?>"><?= Security::escapeHtml($n['nombreNivel']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="idCiclo" id="ciclo-horario" class="informe-select" required>
                <option value="">— Seleccionar ciclo —</option>
                <?php foreach ($ciclos as $c): ?>
                    <option value="<?= (int)$c['idCiclo'] ?>">[<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>] <?= Security::escapeHtml($c['nombreCiclo']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="informe-btn">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </button>
        </form>
    </div>

</div>

<script>
var _ciclosInformes = <?= $ciclosJson ?>;

function cascadeInforme(selectNivel) {
    var idNivel   = parseInt(selectNivel.value) || 0;
    var targetId  = selectNivel.dataset.target;
    var $ciclo    = document.getElementById(targetId);
    var prevVal   = $ciclo.value;
    var firstOpt  = $ciclo.options[0];

    while ($ciclo.options.length > 1) $ciclo.remove(1);

    _ciclosInformes.forEach(function(c) {
        if (!idNivel || c.idNivel === idNivel) {
            var opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = '[' + c.abrev + '] ' + c.nombre;
            $ciclo.appendChild(opt);
        }
    });

    $ciclo.value = prevVal;
    if (!$ciclo.value) $ciclo.value = '';
}
</script>

<?php include '../comunes/footer.php'; ?>
