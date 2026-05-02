<?php
session_start();
$titulo_pagina = "Gestión de Aulas - Super Admin";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/aulas.php";

$todas_las_aulas = listarAulas();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_aulas'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_aulas']);
?>

<div class="encabezado-pagina">
    <h1>Aulas del Centro</h1>
</div>

<?php if ($exito) : ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>

<?php if ($error) : ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Nueva Aula</h3>
    </div>
    <form method="POST" action="../../../controladores/admin/aulas/insertar.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>Nombre del Aula</label>
            <input type="text" name="nombreAula" value="<?= $datos['nombreAula'] ?? '' ?>" placeholder="Aula 101">
            <?php if (isset($lista_de_errores['nombreAula'])) : ?>
                <p class="error-campo"><?= $lista_de_errores['nombreAula'] ?></p>
            <?php endif; ?>
        </div>
        <div class="mt-25">
            <button type="submit" name="guardarAula" class="boton-primario">
                <i class="fas fa-save"></i> Registrar
            </button>
        </div>
    </form>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAulas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Aula</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todas_las_aulas)) : ?>
                    <tr><td colspan="3" class="sin-datos">No hay aulas configuradas</td></tr>
                <?php else : ?>
                    <?php foreach ($todas_las_aulas as $aula) : ?>
                    <tr>
                        <td><?= $aula['idAula'] ?></td>
                        <td><strong><?= $aula['nombreAula'] ?></strong></td>
                        <td>
                            <div class="botones-accion">
                                <a href="modificarAulas.php?idAula=<?= $aula['idAula'] ?>" class="btn-accion btn-editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../../../controladores/admin/aulas/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta aula?')">
                                    <input type="hidden" name="idAula" value="<?= $aula['idAula'] ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<?php include '../comunes/footer.php'; ?>
