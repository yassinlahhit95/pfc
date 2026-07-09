<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/profesores.php";

$listaDeTodosLosProfesores = listarProfesores();

$titulo_pagina = "AULAPRO | PROFESORES DEL CENTRO";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>PROFESORES DEL CENTRO</h1>
    <div class="acciones-pagina" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <a href="../../../controladores/admin/profesores/exportarCSV.php" class="boton-secundario">
            <i class="fas fa-file-export"></i> EXPORTAR CSV
        </a>
        <button type="button" class="boton-secundario" onclick="document.getElementById('modal-import-prof').style.display='flex'">
            <i class="fas fa-file-import"></i> IMPORTAR CSV
        </button>
        <a href="agregarProfesores.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO PROFESOR
        </a>
    </div>
</div>

<!-- Import CSV Modal -->
<div id="modal-import-prof" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:var(--bg-1,#fff);border-radius:14px;padding:32px;width:480px;max-width:95vw;border:1px solid var(--border);">
        <h3 style="margin:0 0 8px;"><i class="fas fa-file-import"></i> Importar Profesores desde CSV</h3>
        <p style="font-size:.85rem;color:var(--text-2);margin-bottom:20px;">
            El CSV debe tener cabecera con estas columnas:<br>
            <code>nombreProfesor,emailProfesor,dniProfesor,telefonoProfesor,direccionProfesor,ciudadProfesor,codigoPostalProfesor,fechaNacimientoProfesor,fechaAltaProfesor,observacionesProfesor</code>
        </p>
        <form action="../../../controladores/admin/profesores/importarCSV.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <div class="campo">
                <label>Archivo CSV</label>
                <input type="file" name="archivo_csv" accept=".csv,text/csv" required style="width:100%;">
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="boton-primario" style="flex:1;"><i class="fas fa-upload"></i> Importar</button>
                <button type="button" class="boton-secundario" onclick="document.getElementById('modal-import-prof').style.display='none'">Cancelar</button>
            </div>
        </form>
    </div>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaProfesores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>ROL</th>
                    <th>CORREO ELECTRONICO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeTodosLosProfesores)) { ?>
                    <tr>
                        <td colspan="5" class="vacio">No hay profesores registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeTodosLosProfesores as $profesorIndividual) { ?>
                    <tr>
                        <td><?= Security::escapeHtml($profesorIndividual['idProfesor']) ?></td>
                        <td><b><?= mb_strtoupper(Security::escapeHtml($profesorIndividual['nombreProfesor']), 'UTF-8') ?></b></td>
                        <td>
                            <?php if (!empty($profesorIndividual['esTutor'])): ?>
                                <span class="texto-estado verde" title="Tutor de: <?= Security::escapeHtml($profesorIndividual['nombreCicloTutor'] ?? '') ?>">
                                    <i class="fas fa-star" style="font-size:.7rem;"></i> Tutor
                                </span>
                            <?php else: ?>
                                <span class="texto-estado gris">Profesor</span>
                            <?php endif; ?>
                        </td>
                        <td><?= Security::escapeHtml($profesorIndividual['emailProfesor']) ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="../../../vistas/admin/profesores/verDetallesProfesores.php?idProfesor=<?= Security::escapeHtml($profesorIndividual['idProfesor']) ?>"><i class="fas fa-search"></i> Ver detalles</a>
                                    <a class="recurso-menu-item" href="../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=<?= Security::escapeHtml($profesorIndividual['idProfesor']) ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$profesorIndividual['idProfesor'] ?>"
                                       data-tipo="Profesor"
                                       data-nombre="<?= Security::escapeHtml($profesorIndividual['nombreProfesor']) ?>"
                                       data-url="/controladores/admin/profesores/borrar.php"
                                       data-campo="idProfesor"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaProfesores', 15);
</script>

