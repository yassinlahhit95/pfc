<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/grupos.php";

$exito  = $_SESSION['exito']  ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$ciclos = listarTodosLosCiclos();
$grupos = listarTodosLosGrupos();

// Calculate student counts per group
$con = obtenerConexion();
$resCounts = mysqli_query($con, "SELECT idGrupo, COUNT(*) as total FROM estudiantes WHERE eliminado = 0 GROUP BY idGrupo");
$counts = [];
while ($row = mysqli_fetch_assoc($resCounts)) {
    $counts[(int)$row['idGrupo']] = (int)$row['total'];
}

$titulo_pagina = "Gestión de Grupos";
$seccion = 'gestionar_grupos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>GESTIÓN DE GRUPOS / AULAS</h1>
        <p class="subtitulo-encabezado">Crea y organiza las aulas (Grupos A, B, Turno Mañana/Tarde) para cada ciclo formativo.</p>
    </div>
</div>

<?php if ($exito): ?>
    <div class="alerta alerta-exito margen-abajo">
        <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
    </div>
<?php endif; ?>

<?php if ($errores): ?>
    <div class="alerta alerta-error margen-abajo">
        <i class="fas fa-exclamation-circle"></i> <?= Security::escapeHtml($errores) ?>
    </div>
<?php endif; ?>

<div class="columnas" style="grid-template-columns: 1fr 2fr; gap: 20px; align-items: start;">
    <!-- Add Group Card -->
    <div class="panel">
        <h2>Crear nuevo grupo</h2>
        <form action="../../../controladores/admin/academico/guardarGrupo.php" method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="campo">
                <label for="idCiclo">Ciclo Formativo</label>
                <select name="idCiclo" id="idCiclo" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach ($ciclos as $c): ?>
                        <option value="<?= (int)$c['idCiclo'] ?>"><?= Security::escapeHtml($c['nombreCiclo']) ?> (<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="anioEstudio">Año / Curso</label>
                <select name="anioEstudio" id="anioEstudio" required>
                    <option value="1º">1º Año</option>
                    <option value="2º">2º Año</option>
                </select>
            </div>

            <div class="campo">
                <label for="nombreGrupo">Nombre del Grupo / Aula</label>
                <input type="text" name="nombreGrupo" id="nombreGrupo" placeholder="p.ej. Grupo A - Mañana" required>
            </div>

            <div class="acciones" style="margin-top:20px;">
                <button type="submit" class="boton-primario" style="width:100%;"><i class="fas fa-plus"></i> CREAR GRUPO</button>
            </div>
        </form>
    </div>

    <!-- Groups List Card -->
    <div class="panel">
        <h2>Aulas registradas</h2>
        <div class="contenedor-tabla">
            <table class="tabla-datos" id="tablaGrupos">
                <thead>
                    <tr>
                        <th>Ciclo Formativo</th>
                        <th>Año</th>
                        <th>Nombre del Grupo</th>
                        <th style="text-align:center">Estudiantes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grupos)): ?>
                        <tr>
                            <td colspan="5" class="vacio">No hay ningún grupo configurado todavía.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($grupos as $g): ?>
                            <tr>
                                <td>
                                    <strong><?= Security::escapeHtml($g['nombreCiclo']) ?></strong> 
                                    <span class="texto-suave">(<?= Security::escapeHtml($g['abreviaturaCiclo']) ?>)</span>
                                </td>
                                <td><?= Security::escapeHtml($g['anioEstudio']) ?></td>
                                <td><strong><?= Security::escapeHtml($g['nombreGrupo']) ?></strong></td>
                                <td style="text-align:center">
                                    <span class="nav-badge"><?= $counts[(int)$g['idGrupo']] ?? 0 ?> alumnos</span>
                                </td>
                                <td>
                                    <div style="display:flex; gap:8px;">
                                        <!-- Edit Modal Trigger or button -->
                                        <button class="btn-accion" onclick="editarGrupo(<?= (int)$g['idGrupo'] ?>, '<?= Security::escapeHtml($g['nombreGrupo']) ?>', <?= (int)$g['idCiclo'] ?>, '<?= Security::escapeHtml($g['anioEstudio']) ?>')" title="Editar"><i class="fas fa-edit"></i></button>
                                        
                                        <!-- Delete -->
                                        <form action="../../../controladores/admin/academico/eliminarGrupo.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este grupo? Los alumnos de este grupo quedarán sin aula asignada.');" style="margin:0;">
                                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                            <input type="hidden" name="idGrupo" value="<?= (int)$g['idGrupo'] ?>">
                                            <button type="submit" class="btn-accion btn-eliminar" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Simple Edit Modal / Overlay -->
<div id="modalEditarGrupo" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="panel" style="max-width:450px; width:100%; margin:20px; background:white; border-radius:12px; padding:25px;">
        <h2 style="margin-top:0;">Modificar Grupo</h2>
        <form action="../../../controladores/admin/academico/guardarGrupo.php" method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idGrupo" id="edit_idGrupo">

            <div class="campo">
                <label for="edit_idCiclo">Ciclo Formativo</label>
                <select name="idCiclo" id="edit_idCiclo" required>
                    <?php foreach ($ciclos as $c): ?>
                        <option value="<?= (int)$c['idCiclo'] ?>"><?= Security::escapeHtml($c['nombreCiclo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="edit_anioEstudio">Año / Curso</label>
                <select name="anioEstudio" id="edit_anioEstudio" required>
                    <option value="1º">1º Año</option>
                    <option value="2º">2º Año</option>
                </select>
            </div>

            <div class="campo">
                <label for="edit_nombreGrupo">Nombre del Grupo</label>
                <input type="text" name="nombreGrupo" id="edit_nombreGrupo" required>
            </div>

            <div class="acciones" style="display:flex; gap:10px; margin-top:20px;">
                <button type="button" class="boton-secundario" onclick="cerrarModal()" style="flex:1;">Cancelar</button>
                <button type="submit" class="boton-primario" style="flex:1;">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editarGrupo(id, nombre, idCiclo, anio) {
        document.getElementById('edit_idGrupo').value = id;
        document.getElementById('edit_nombreGrupo').value = nombre;
        document.getElementById('edit_idCiclo').value = idCiclo;
        document.getElementById('edit_anioEstudio').value = anio;
        document.getElementById('modalEditarGrupo').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modalEditarGrupo').style.display = 'none';
    }
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
