<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$ciclos = listarTodosLosCiclos();

$titulo_pagina = "AULAPRO | INFORMES";
$seccion = 'informes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>INFORMES</h1>
</div>

<div class="panel margen-abajo">
    <h2 style="margin-bottom:1.2rem;"><i class="fas fa-users"></i> Listado de Estudiantes</h2>
    <p class="texto-suave" style="margin-bottom:1rem;">Genera un PDF con el listado de estudiantes matriculados.</p>
    <form method="GET" action="../../../controladores/admin/informes/generarListado.php"
          target="_blank" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="campo">
            <label for="idCiclo">Filtrar por Ciclo</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">Todos los ciclos</option>
                <?php foreach ($ciclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>">
                    [<?= Security::escapeHtml($c['abreviaturaCiclo'] ?: '') ?>] <?= Security::escapeHtml($c['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="acciones">
            <button type="submit" class="boton-primario">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </button>
        </div>
    </form>
</div>

<div class="panel">
    <h2 style="margin-bottom:1.2rem;"><i class="fas fa-calendar-week"></i> Horario</h2>
    <p class="texto-suave" style="margin-bottom:1rem;">Imprime el cuadro horario de un ciclo en formato PDF.</p>
    <a href="../horario/horario.php" class="boton-secundario">
        <i class="fas fa-arrow-right"></i> Ir al horario
    </a>
</div>

<?php include '../comunes/footer.php'; ?>
