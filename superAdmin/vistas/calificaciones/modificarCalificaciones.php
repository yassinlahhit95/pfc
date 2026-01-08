<title>Modificar Calificación - Super Admin</title>

<?php
$titulo_pagina = "Modificar Calificación - Super Admin";
include_once "../comunes/nav.php";
?>

<main class="contenido-principal">
  <div class="encabezado-pagina">
    <div>
      <h1>Modificar Calificación</h1>
      <p style="color: #8f9bba; margin-top: 5px">
        Actualiza la calificacin final del estudiante
      </p>
    </div>
    <div class="acciones-pagina">
      <a href="verCalificaciones.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <div class="contenedor-formulario">
    <form id="formularioModificar">
      <div class="cuadricula-formulario">
        <div class="grupo-formulario">
          <label>Estudiante <span class="requerido">*</span></label>
          <select id="estudiante" requerido>
            <option value="">Seleccionar estudiante</option>
            <option value="est1" selected>Juan PPerez Garcia</option>
            <option value="est2">Maria Lopez Sanchez</option>
            <option value="est3">Carlos Rodriguez Martin</option>
          </select>
        </div>
        <div class="grupo-formulario">
          <label>Materia <span class="requerido">*</span></label>
          <select id="materia" requerido>
            <option value="">Seleccionar materia</option>
            <option value="matematicas" selected>Matemáticas</option>
            <option value="lengua">Lengua y Literatura</option>
            <option value="ingles">Inglés</option>
            <option value="ciencias">Ciencias</option>
          </select>
        </div>
        <div class="grupo-formulario">
          <label>Trimestre 1 <span class="requerido">*</span></label>
          <input
            type="number"
            id="trimestre1"
            value="7.5"
            min="0"
            max="10"
            step="0.1"
            requerido />
        </div>
        <div class="grupo-formulario">
          <label>Trimestre 2 <span class="requerido">*</span></label>
          <input
            type="number"
            id="trimestre2"
            value="8.0"
            min="0"
            max="10"
            step="0.1"
            requerido />
        </div>
        <div class="grupo-formulario">
          <label>Trimestre 3 <span class="requerido">*</span></label>
          <input
            type="number"
            id="trimestre3"
            value="8.5"
            min="0"
            max="10"
            step="0.1"
            requerido />
        </div>
        <div class="grupo-formulario">
          <label>Calificación Final <span class="requerido">*</span></label>
          <input
            type="number"
            id="calificacionFinal"
            value="8.0"
            min="0"
            max="10"
            step="0.1"
            requerido />
        </div>
        <div class="grupo-formulario">
          <label>Año Académico <span class="requerido">*</span></label>
          <select id="anoAcademico" requerido>
            <option value="2023-2024" selected>2023-2024</option>
            <option value="2024-2025">2024-2025</option>
          </select>
        </div>
        <div class="grupo-formulario">
          <label>Resultado <span class="requerido">*</span></label>
          <select id="resultado" requerido>
            <option value="">Seleccionar resultado</option>
            <option value="aprobado" selected>Aprobado</option>
            <option value="suspenso">Suspenso</option>
            <option value="notable">Notable</option>
            <option value="sobresaliente">Sobresaliente</option>
          </select>
        </div>
        <div class="grupo-formulario ancho-completo">
          <label>Observaciones</label>
          <textarea id="observaciones" rows="3">
                Buen rendimiento durante todo el año académico
          </textarea>
        </div>
      </div>

      <div class="acciones-formulario">
        <button
          type="button"
          class="boton-cancelar"
          onclick="window.location.href='verCalificaciones.php'">
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
<script src="../../js/deleteModal.js"></script>
<script src="../../js/main.js"></script>

</body>

</html>