<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

$tituloDelPagina = "Agregar Reto - Portal Profesores";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Reto</h1>
    <a href="/pfc/profesores/vistas/retos/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/profesores/retos/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" required>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales *</label>
                <input type="text" name="horasReto" required>
            </div>

            <div class="campo-formulario">
                <label>Fecha Inicio *</label>
                <input type="date" name="fechaInicio" required>
            </div>

            <div class="campo-formulario">
                <label>Fecha Fin *</label>
                <input type="date" name="fechaFin" required>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="insertarReto" class="boton-primario">Guardar Reto</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>