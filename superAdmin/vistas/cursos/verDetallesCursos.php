<?php
$titulo_pagina = "Detalles Curso - Super Admin";
include_once "../comunes/nav.php";
?>
<main class="contenido-principal">
  <div class="encabezado-pagina">
    <div>
      <h1>Detalles del Curso</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Información completa del curso
      </p>
    </div>
    <div class="acciones-pagina">
      <a href="verCursos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
      <a href="modificarCursos.php" class="boton-primario"><i class="fas fa-edit"></i> Editar</a>
    </div>
  </div>
  <div class="tarjeta-panel" style="margin-bottom: 20px">
    <div class="encabezado-tarjeta">
      <h3><i class="fas fa-info-circle"></i> Información del Curso</h3>
    </div>
    <div class="cuadricula-formulario">
      <div class="grupo-formulario">
        <label>ID</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">#C001</p>
      </div>
      <div class="grupo-formulario">
        <label>Nombre del Curso</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">3º ESO A</p>
      </div>
      <div class="grupo-formulario">
        <label>Nivel</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">3º ESO</p>
      </div>
      <div class="grupo-formulario">
        <label>Año Académico</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">
          2023-2024
        </p>
      </div>
      <div class="grupo-formulario">
        <label>Capacidad Máxima</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">
          30 estudiantes
        </p>
      </div>
      <div class="grupo-formulario">
        <label>Estudiantes Matriculados</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">
          28 estudiantes
        </p>
      </div>
      <div class="grupo-formulario">
        <label>Tutor Asignado</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">
          Carlos Martínez López
        </p>
      </div>
      <div class="grupo-formulario">
        <label>Aula</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">Aula 201</p>
      </div>
      <div class="grupo-formulario ancho-completo">
        <label>Descripción</label>
        <p style="margin: 0; padding: 12px 0; color: #2d3748">
          Curso de tercer año de Educación Secundaria Obligatoria, grupo A
        </p>
      </div>
      <div class="grupo-formulario">
        <label>Estado</label>
        <p style="margin: 0; padding: 12px 0">
          <span class="insignia-estado estado-activo">Activo</span>
        </p>
      </div>
    </div>
  </div>
</main>
</div>
<!-- Scripts -->
<script src="../../js/menu.js"></script>
<script src="../../js/deleteModal.js"></script>
<script src="../../js/main.js"></script>

</body>

</html>