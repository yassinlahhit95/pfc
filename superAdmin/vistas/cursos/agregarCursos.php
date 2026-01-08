<?php
$titulo_pagina = "Agregar Curso - Super Admin";
include_once "../comunes/nav.php";
?>
<main class="contenido-principal">
  <!-- Page Header -->
  <div class="encabezado-pagina">
    <div>
      <h1>Agregar Curso</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Complete el formulario para registrar un nuevo curso
      </p>
    </div>
    <div class="acciones-pagina">
      <a href="verCursos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
  </div>

  <!-- Form Container -->
  <div class="contenedor-formulario">
    <form>
      <div class="cuadricula-formulario">
        <!-- Nombre del Curso -->
        <div class="grupo-formulario">
          <label>Nombre del Curso <span class="requerido">*</span></label>
          <input type="text" placeholder="Ej: 1º ESO A" requerido />
        </div>

        <!-- Nivel -->
        <div class="grupo-formulario">
          <label>Nivel <span class="requerido">*</span></label>
          <select requerido>
            <option value="">Seleccione un nivel</option>
            <option value="grado básico">GRADO BÁSICO</option>
            <option value="grado medio">GRADO MEDIO</option>
            <option value="grado superior">GRADO SUPERIOR</option>
          </select>
        </div>

        <!-- Tutor -->
        <div class="grupo-formulario">
          <label>Tutor/a</label>
          <select>
            <option value="">Seleccione un tutor/a</option>
            <option value="1">Juan Pérez</option>
            <option value="2">María Gómez</option>
            <option value="3">Luis Rodríguez</option>
          </select>
        </div>

        <!-- Aula -->
        <div class="grupo-formulario">
          <label>Aula</label>
          <select>
            <option value="">Seleccione un aula</option>
            <option value="A101">A101</option>
            <option value="B202">B202</option>
            <option value="C303">C303</option>
          </select>
        </div>

        <!-- Año Académico -->
        <div class="grupo-formulario">
          <label>Año Académico <span class="requerido">*</span></label>
          <input type="text" placeholder="Ej: 2023-2024" requerido />
        </div>

        <!-- Descripción -->
        <div class="grupo-formulario ancho-completo">
          <label>Descripción</label>
          <textarea
            placeholder="Descripción adicional del curso..."></textarea>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="acciones-formulario">
        <button
          type="button"
          class="boton-cancelar"
          onclick="window.location.href='verCursos.php'">
          Cancelar
        </button>
        <button type="submit" class="boton-enviar">
          <i class="fas fa-save"></i>
          Guardar Curso
        </button>
      </div>
    </form>
  </div>
</main>
</div>
<!-- Scripts -->
<script src="../../js/menu.js"></script>
<script src="../../js/main.js"></script>

</body>

</html>