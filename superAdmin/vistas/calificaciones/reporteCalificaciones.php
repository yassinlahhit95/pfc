<?php
$titulo_pagina = "Reporte Calificaciones - Super Admin";
include_once "../comunes/nav.php";
?>
<div class="contenedor-admin">

  <main class="contenido-principal">
    <!-- Page Header -->
    <div class="encabezado-pagina">
      <div>
        <h1>Reporte de Calificaciones</h1>
        <p style="color: #8f9bba; margin-top: 5px">
          Genere un reporte detallado de calificaciones
        </p>
      </div>
      <div class="acciones-pagina">
        <a href="verCalificaciones.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
      </div>
    </div>

    <!-- Form Container -->
    <div class="contenedor-formulario">
      <form>
        <div class="cuadricula-formulario">
          <!-- Estudiante -->
          <div class="grupo-formulario">
            <label>Estudiante</label>
            <input
              type="text"
              placeholder="Buscar estudiante (opcional)..." />
          </div>

          <!-- Curso -->
          <div class="grupo-formulario">
            <label>Curso <span class="requerido">*</span></label>
            <select requerido>
              <option value="">Seleccione un curso</option>
              <option value="1eso-a">1º ESO A</option>
              <option value="1eso-b">1º ESO B</option>
              <!-- Más opciones -->
            </select>
          </div>

          <!-- Periodo -->
          <div class="grupo-formulario">
            <label>Periodo Académico <span class="requerido">*</span></label>
            <select requerido>
              <option value="">Seleccione periodo</option>
              <option value="trimestre1">1º Trimestre</option>
              <option value="trimestre2">2º Trimestre</option>
              <option value="trimestre3">3º Trimestre</option>
              <option value="final">Final</option>
            </select>
          </div>

          <!-- Tipo de Reporte -->
          <div class="grupo-formulario">
            <label>Tipo de Reporte <span class="requerido">*</span></label>
            <select requerido>
              <option value="boletin">Boletín de Notas</option>
              <option value="resumen">Resumen de Rendimiento</option>
            </select>
          </div>

          <!-- Formato -->
          <div class="grupo-formulario">
            <label>Formato de Salida</label>
            <select>
              <option value="pdf">PDF</option>
              <option value="excel">Excel</option>
            </select>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="acciones-formulario">
          <button
            type="button"
            class="boton-cancelar"
            onclick="window.location.href='verCalificaciones.php'">
            Cancelar
          </button>
          <button type="submit" class="boton-enviar">
            <i class="fas fa-file-download"></i>
            Generar Reporte
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