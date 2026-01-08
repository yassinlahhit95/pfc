<?php
$titulo_pagina = "Modificar Curso - Super Admin";
include_once "../comunes/nav.php";
?> 
<main class="contenido-principal">
  <div class="encabezado-pagina">
    <div>
      <h1>Modificar Curso</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Actualiza la información del curso
      </p>
    </div>
    <div class="acciones-pagina">
      <a href="verCursos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <div class="contenedor-formulario">
    <form id="formularioModificar">
      <div class="cuadricula-formulario">
        <div class="grupo-formulario">
          <label>Nombre del Curso <span class="requerido">*</span></label>
          <input type="text" id="nombreCurso" value="3º ESO A" requerido />
        </div>
        <div class="grupo-formulario">
          <label>Nivel <span class="requerido">*</span></label>
          <select id="nivel" requerido>
            <option value="">Seleccionar nivel</option>
            <option value="1eso">1º ESO</option>
            <option value="2eso">2º ESO</option>
            <option value="3eso" selected>3º ESO</option>
            <option value="4eso">4º ESO</option>
          </select>
        </div>
        <div class="grupo-formulario">
          <label>Año Académico <span class="requerido">*</span></label>
          <select id="anoAcademico" requerido>
            <option value="2023-2024" selected>2023-2024</option>
            <option value="2024-2025">2024-2025</option>
          </select>
        </div>
        <div class="grupo-formulario">
          <label>Capacidad Máxima <span class="requerido">*</span></label>
          <input
            type="number"
            id="capacidad"
            value="30"
            min="1"
            requerido />
        </div>
        <div class="grupo-formulario">
          <label>Tutor Asignado</label>
          <select id="tutor">
            <option value="">Sin asignar</option>
            <option value="prof1" selected>Carlos Martínez López</option>
            <option value="prof2">Laura Sánchez García</option>
            <option value="prof3">Miguel Torres Ruiz</option>
          </select>
        </div>
        <div class="grupo-formulario">
          <label>Aula</label>
          <input type="text" id="aula" value="Aula 201" />
        </div>
        <div class="grupo-formulario ancho-completo">
          <label>Descripción</label>
          <textarea id="descripcion" rows="3">
Curso de tercer año de Educación Secundaria Obligatoria, grupo A</textarea>
        </div>
        <div class="grupo-formulario">
          <label>Estado <span class="requerido">*</span></label>
          <select id="estado" requerido>
            <option value="activo" selected>Activo</option>
            <option value="inactivo">Inactivo</option>
            <option value="completo">Completo</option>
          </select>
        </div>
      </div>

      <div class="acciones-formulario">
        <button
          type="button"
          class="boton-cancelar"
          onclick="window.location.href='verCursos.php'">
          Cancelar
        </button>
        <button type="submit" class="boton-enviar">
          <i class="fas fa-save"></i> Guardar Cambios
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