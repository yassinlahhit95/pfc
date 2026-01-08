<?php
$titulo_pagina = "Modificar Materia - Super Admin";
include_once "../comunes/nav.php";
?>
<main class="contenido-principal">
  <div class="encabezado-pagina">
    <div>
      <h1>Modificar Materia</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Actualiza la información de la materia
      </p>
    </div>
    <div class="acciones-pagina">
      <a href="verMaterias.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <div class="contenedor-formulario">
    <form id="formularioModificar">
      <div class="cuadricula-formulario">
        <div class="grupo-formulario">
          <label>Nombre de la Materia <span class="requerido">*</span></label>
          <input
            type="text"
            id="nombreMateria"
            value="Matemáticas"
            requerido />
        </div>
        <div class="grupo-formulario">
          <label>Código <span class="requerido">*</span></label>
          <input type="text" id="codigo" value="MAT-301" requerido />
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
          <label>Horas Semanales <span class="requerido">*</span></label>
          <input
            type="number"
            id="horasSemanales"
            value="4"
            min="1"
            max="10"
            requerido />
        </div>
        <div class="grupo-formulario">
          <label>Profesor Asignado</label>
          <select id="profesor">
            <option value="">Sin asignar</option>
            <option value="prof1" selected>Carlos Martínez López</option>
            <option value="prof2">Laura Sánchez García</option>
            <option value="prof3">Miguel Torres Ruiz</option>
          </select>
        </div>
        <div class="grupo-formulario">
          <label>Departamento <span class="requerido">*</span></label>
          <select id="departamento" requerido>
            <option value="">Seleccionar departamento</option>
            <option value="ciencias" selected>Ciencias</option>
            <option value="letras">Letras</option>
            <option value="idiomas">Idiomas</option>
            <option value="artes">Artes</option>
          </select>
        </div>
        <div class="grupo-formulario ancho-completo">
          <label>Descripción</label>
          <textarea id="descripcion" rows="3">
                  Materia de matemáticas para tercer curso de ESO</textarea>
        </div>
        <div class="grupo-formulario">
          <label>Estado <span class="requerido">*</span></label>
          <select id="estado" requerido>
            <option value="activo" selected>Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>
      </div>

      <div class="acciones-formulario">
        <button
          type="button"
          class="boton-cancelar"
          onclick="window.location.href='verMaterias.php'">
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