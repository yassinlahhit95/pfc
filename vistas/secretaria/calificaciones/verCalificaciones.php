<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";

$listaCiclos     = listarTodosLosCiclos();
$idCicloElegido  = (int)($_GET['idCiclo'] ?? 0);
$idModuloElegido = (int)($_GET['idModulo'] ?? 0);

$listaModulos     = $idCicloElegido  ? listarModulosPorCiclo($idCicloElegido)          : [];
$listaEstudiantes = $idModuloElegido ? listarCalificacionesPorModulo($idModuloElegido) : [];

$titulo_pagina = "AULAPRO | CALIFICACIONES";
$seccion = 'calificaciones';
?>
<style media="print">
  .sidebar, .topbar, .cabecera .boton-secundario, .cabecera .boton-primario, form { display: none !important; }
  .main { margin: 0 !important; }
</style>
<?php include_once __DIR__ . "/../comunes/nav.php"; ?>

<div class="cabecera">
    <h1>CALIFICACIONES</h1>
    <?php if ($idModuloElegido): ?>
    <button onclick="window.print()" class="boton-secundario">
        <i class="fas fa-print"></i> Imprimir
    </button>
    <?php endif; ?>
</div>

<div class="dashboard-grid" style="margin-bottom:24px;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));">
    <a href="verCalificaciones.php" class="tile card-soft" style="--tint:#4F46E5; text-decoration:none">
        <span class="tile-sheen"></span>
        <span class="tile-ico">
            <i class="fas fa-book" style="font-size:1.5rem"></i>
        </span>
        <span class="tile-body">
            <span class="tile-title">Módulos</span>
            <span class="tile-desc">Calificaciones ordinarias</span>
        </span>
    </a>
    <a href="verRetos.php" class="tile card-soft" style="--tint:#10B981; text-decoration:none">
        <span class="tile-sheen"></span>
        <span class="tile-ico">
            <i class="fas fa-flag" style="font-size:1.5rem"></i>
        </span>
        <span class="tile-body">
            <span class="tile-title">Retos</span>
            <span class="tile-desc">Evaluación por retos</span>
        </span>
    </a>
    <a href="verTFG.php" class="tile card-soft" style="--tint:#F59E0B; text-decoration:none">
        <span class="tile-sheen"></span>
        <span class="tile-ico">
            <i class="fas fa-graduation-cap" style="font-size:1.5rem"></i>
        </span>
        <span class="tile-body">
            <span class="tile-title">TFG</span>
            <span class="tile-desc">Trabajos de fin de grado</span>
        </span>
    </a>
</div>

<div class="panel margen-abajo">
    <form method="GET" class="formulario">
        <div class="campo">
            <label for="idCiclo">Ciclo</label>
            <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                <option value="">— Selecciona ciclo —</option>
                <?php foreach ($listaCiclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>"
                    <?= ($c['idCiclo'] == $idCicloElegido) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($c['nombreCiclo']) ?> (<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (!empty($listaModulos)): ?>
        <div class="campo">
            <label for="idModulo">Módulo</label>
            <select name="idModulo" id="idModulo" onchange="this.form.submit()">
                <option value="">— Selecciona módulo —</option>
                <?php foreach ($listaModulos as $m): ?>
                <option value="<?= (int)$m['idModulo'] ?>"
                    <?= ($m['idModulo'] == $idModuloElegido) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($m['nombreModulo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <?php if (!$idCicloElegido): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-graduation-cap"></i></div>
        <div class="panel-vacio-titulo">Selecciona un ciclo</div>
        <div class="panel-vacio-desc">Elige un ciclo y módulo para ver las calificaciones.</div>
    </div>
    <?php elseif (!$idModuloElegido): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-book"></i></div>
        <div class="panel-vacio-titulo">Selecciona un módulo</div>
        <div class="panel-vacio-desc">Elige un módulo para ver sus calificaciones.</div>
    </div>
    <?php elseif (empty($listaEstudiantes)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-user-graduate"></i></div>
        <div class="panel-vacio-titulo">Sin calificaciones</div>
        <div class="panel-vacio-desc">No hay calificaciones registradas para este módulo.</div>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaCalificaciones">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>1ª Ev.</th>
                    <th>1ª Final</th>
                    <th>2ª Ev.</th>
                    <th>2ª Final</th>
                    <th>Media</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaEstudiantes as $est):
                    $notas = [
                        $est['calificacion'] ?? null,
                        null, null, null
                    ];
                    $nota1ev    = $est['nota_1ev']    ?? null;
                    $nota1final = $est['nota_1final'] ?? null;
                    $nota2ev    = $est['nota_2ev']    ?? null;
                    $nota2final = $est['nota_2final'] ?? null;
                    $vals = array_filter([$nota1ev, $nota1final, $nota2ev, $nota2final], fn($v) => $v !== null && $v !== '');
                    $media = count($vals) ? round(array_sum($vals) / count($vals), 2) : null;
                    $fmt = fn($v) => ($v !== null && $v !== '') ? number_format((float)$v, 2) : '—';
                ?>
                <tr>
                    <td><?= Security::escapeHtml($est['nombreEstudiante']) ?></td>
                    <td><?= $fmt($nota1ev) ?></td>
                    <td><?= $fmt($nota1final) ?></td>
                    <td><?= $fmt($nota2ev) ?></td>
                    <td><?= $fmt($nota2final) ?></td>
                    <td><strong><?= $media !== null ? number_format($media, 2) : '—' ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<?php if ($idModuloElegido): ?>
<script>iniciarPaginacion('tablaCalificaciones', 20);</script>
<?php endif; ?>
