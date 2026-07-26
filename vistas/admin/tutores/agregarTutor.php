<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
$datos   = $_SESSION['datos_tutor'] ?? [];
unset($_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_tutor']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$listaEstudiantes = listarEstudiantes();
$listaCiclos      = listarTodosLosCiclos();

$titulo_pagina = "AULAPRO | AGREGAR FAMILIAR";
$seccion = 'tutores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO FAMILIAR / TUTOR LEGAL</h1>
    <a href="verTutores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/tutores/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <h4 style="margin:0 0 20px;"><i class="fas fa-user"></i> Datos del Familiar / Tutor Legal</h4>

        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nombreTutor') ?>">
                    <label for="nombreTutor">Nombre Completo <span style="color:red;">*</span></label>
                    <input type="text" id="nombreTutor" name="nombreTutor" value="<?= Security::escapeHtml($datos['nombreTutor'] ?? '') ?>">
                    <?= fieldError($errores, 'nombreTutor') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'emailTutor') ?>">
                    <label for="emailTutor">Correo Electrónico <span style="color:red;">*</span></label>
                    <input type="email" id="emailTutor" name="emailTutor" value="<?= Security::escapeHtml($datos['emailTutor'] ?? '') ?>">
                    <?= fieldError($errores, 'emailTutor') ?>
                </div>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'dniTutor') ?>">
                    <label for="dniTutor">DNI / NIE <span style="color:red;">*</span></label>
                    <input type="text" id="dniTutor" name="dniTutor" value="<?= Security::escapeHtml($datos['dniTutor'] ?? '') ?>">
                    <?= fieldError($errores, 'dniTutor') ?>
                </div>

                <div class="campo">
                    <label for="telefonoTutor">Teléfono</label>
                    <input type="text" id="telefonoTutor" name="telefonoTutor" value="<?= Security::escapeHtml($datos['telefonoTutor'] ?? '') ?>">
                </div>
            </div>
        </div>

        <h4 style="margin:25px 0 15px;"><i class="fas fa-link"></i> Vincular con Estudiantes</h4>

        <div class="formulario" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr));margin-bottom:15px;gap:12px;">
            <div class="campo">
                <label for="parentesco">Parentesco</label>
                <select id="parentesco" name="parentesco">
                    <option value="Padre" <?= (($datos['parentesco'] ?? '') === 'Padre') ? 'selected' : '' ?>>Padre</option>
                    <option value="Madre" <?= (($datos['parentesco'] ?? '') === 'Madre') ? 'selected' : '' ?>>Madre</option>
                    <option value="Tutor Legal" <?= (($datos['parentesco'] ?? '') === 'Tutor Legal') ? 'selected' : '' ?>>Tutor Legal</option>
                </select>
            </div>
            <div class="campo">
                <label for="filtroNivel">Filtrar por Nivel</label>
                <select id="filtroNivel" onchange="filtrarEstudiantes()">
                    <option value="">— Todos los niveles —</option>
                    <option value="1">Grado Medio</option>
                    <option value="2">Grado Superior</option>
                </select>
            </div>
            <div class="campo">
                <label for="filtroCiclo">Filtrar por Ciclo</label>
                <select id="filtroCiclo" onchange="filtrarEstudiantes()">
                    <option value="">— Todos los ciclos —</option>
                    <?php foreach ($listaCiclos as $ciclo): ?>
                        <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>"><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="filtroAnio">Año de estudio</label>
                <select id="filtroAnio" onchange="filtrarEstudiantes()">
                    <option value="">— Todos los años —</option>
                    <option value="1º">1º año</option>
                    <option value="2º">2º año</option>
                </select>
            </div>
        </div>

        <div class="campo" style="margin-bottom:10px;">
            <input type="text" id="buscarEstudiante" placeholder="Buscar por nombre…" oninput="filtrarEstudiantes()"
                   autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other"
                   style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:var(--bg-1);color:var(--text-1);">
        </div>

        <div id="lista-estudiantes" style="max-height:260px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:12px;">
            <?php if (empty($listaEstudiantes)): ?>
                <p class="texto-suave" style="text-align:center;padding:20px;">No hay estudiantes registrados.</p>
            <?php else: ?>
                <?php foreach ($listaEstudiantes as $estudiante): ?>
                    <label class="check-item est-item"
                           data-nombre="<?= strtolower(Security::escapeHtml($estudiante['nombreEstudiante'])) ?>"
                           data-nivel="<?= Security::escapeHtml($estudiante['idNivel']) ?>"
                           data-ciclo="<?= Security::escapeHtml($estudiante['idCiclo']) ?>"
                           data-anio="<?= Security::escapeHtml($estudiante['anioEstudio'] ?? '') ?>"
                           style="display:flex;">
                        <input type="checkbox" name="estudiantes[]" value="<?= Security::escapeHtml($estudiante['idEstudiante']) ?>"
                            <?= in_array($estudiante['idEstudiante'], (array)($datos['estudiantes'] ?? [])) ? 'checked' : '' ?>>
                        <span style="display:flex;align-items:center;gap:10px;flex:1;">
                            <b><?= Security::escapeHtml($estudiante['nombreEstudiante']) ?></b>
                            <span class="texto-estado <?= $estudiante['curso'] === 'Grado Superior' ? 'verde' : 'azul' ?>" style="font-size:.72rem;">
                                <?= $estudiante['curso'] === 'Grado Superior' ? 'G. Superior' : 'G. Medio' ?>
                            </span>
                            <?php if (!empty($estudiante['anioEstudio'])): ?>
                                <span class="texto-estado gris" style="font-size:.72rem;"><?= Security::escapeHtml($estudiante['anioEstudio']) ?></span>
                            <?php endif; ?>
                            <small style="color:var(--text-2);"><?= Security::escapeHtml($estudiante['nombreCiclo']) ?></small>
                        </span>
                    </label>
                <?php endforeach; ?>
                <p id="sin-resultados" style="display:none;text-align:center;padding:16px;color:var(--text-2);">Sin resultados para los filtros aplicados.</p>
            <?php endif; ?>
        </div>

        <div class="acciones" style="margin-top:25px;">
            <input type="submit" name="guardarTutor" class="boton-primario" value="REGISTRAR FAMILIAR">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
function filtrarEstudiantes() {
    var texto = document.getElementById('buscarEstudiante').value.toLowerCase().trim();
    var nivel = document.getElementById('filtroNivel').value;
    var ciclo = document.getElementById('filtroCiclo').value;
    var anio  = document.getElementById('filtroAnio').value;
    var items = document.querySelectorAll('.est-item');
    var visibles = 0;

    items.forEach(function(el) {
        var pasaNombre = !texto || el.dataset.nombre.indexOf(texto) !== -1;
        var pasaNivel  = !nivel  || el.dataset.nivel === nivel;
        var pasaCiclo  = !ciclo  || el.dataset.ciclo === ciclo;
        var pasaAnio   = !anio   || el.dataset.anio === anio;
        var mostrar = pasaNombre && pasaNivel && pasaCiclo && pasaAnio;
        el.style.display = mostrar ? 'flex' : 'none';
        if (mostrar) visibles++;
    });

    document.getElementById('sin-resultados').style.display = visibles === 0 ? 'block' : 'none';
}
</script>

<?php include '../comunes/footer.php'; ?>
