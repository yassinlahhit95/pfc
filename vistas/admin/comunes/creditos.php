<?php
session_start();
$titulo_pagina = "Créditos del Proyecto - TFG";
$seccion = 'creditos';
include_once "nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Información del Proyecto</h1>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Acerca de este Proyecto</h3>
    </div>
    
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Autor / Estudiante</label>
            <p class="texto-negrita" style="font-size: 1.2rem;">Yassin Lahhit</p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Año Académico</label>
            <p class="texto-negrita"><?php echo date('Y'); ?></p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Centro de Estudios</label>
            <p class="texto-negrita">CPS Ibaiondo</p>
            <p class="texto-atenuado">Centro de Formación Profesional</p>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Tipo de Trabajo</label>
            <p class="texto-negrita">Trabajo de Fin de Grado (TFG)</p>
        </div>

        <div class="campo-formulario campo-ancho-total">
            <label class="texto-atenuado texto-pequeno">La Idea del Proyecto</label>
            <div class="tarjeta-gris-suave mt-5">
                <p class="lh-1-4">
                    Este sistema de gestión escolar ha sido desarrollado como proyecto final de grado para centralizar y simplificar 
                    las tareas administrativas y académicas de un centro de formación. 
                </p>
                <p class="lh-1-4 mt-5">
                    La plataforma permite el control de estudiantes, profesorado, gestión de pagos, inventario de recursos y el seguimiento 
                    de retos educativos, proporcionando una interfaz intuitiva y eficiente para el personal administrativo.
                </p>
            </div>
        </div>
    </div>

    <div class="margen-arriba">
        <p class="texto-atenuado" style="text-align: center; font-size: 12px;">
            © <?php echo date('Y'); ?> Yassin Lahhit - Todos los derechos reservados
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>

