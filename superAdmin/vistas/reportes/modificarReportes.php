<?php
$titulo_pagina = "Modificar Reporte - Super Admin";
include_once "../comunes/nav.php";
?>

      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Modificar Reporte</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Actualiza la información del reporte
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="generarReportes.php" class="boton-secundario">
              <i class="fas fa-arrow-left"></i> Volver
            </a>
          </div>
        </div>

        <div class="contenedor-formulario">
          <form id="formularioModificar">
            <div class="cuadricula-formulario">
              <div class="grupo-formulario">
                <label
                  >Título del Reporte <span class="requerido">*</span></label
                >
                <input
                  type="text"
                  id="titulo"
                  value="Reporte Académico Trimestral"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Tipo de Reporte <span class="requerido">*</span></label>
                <select id="tipoReporte" requerido>
                  <option value="">Seleccionar tipo</option>
                  <option value="academico" selected>Académico</option>
                  <option value="asistencia">Asistencia</option>
                  <option value="disciplinario">Disciplinario</option>
                  <option value="general">General</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Periodo <span class="requerido">*</span></label>
                <select id="periodo" requerido>
                  <option value="">Seleccionar periodo</option>
                  <option value="trimestre1" selected>Primer Trimestre</option>
                  <option value="trimestre2">Segundo Trimestre</option>
                  <option value="trimestre3">Tercer Trimestre</option>
                  <option value="anual">Anual</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label
                  >Fecha de Generación <span class="requerido">*</span></label
                >
                <input
                  type="date"
                  id="fechaGeneracion"
                  value="2024-01-20"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Curso/Nivel</label>
                <select id="curso">
                  <option value="">Todos los cursos</option>
                  <option value="1eso">1º ESO</option>
                  <option value="2eso">2º ESO</option>
                  <option value="3eso" selected>3º ESO</option>
                  <option value="4eso">4º ESO</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Generado Por <span class="requerido">*</span></label>
                <select id="generadoPor" requerido>
                  <option value="">Seleccionar</option>
                  <option value="admin" selected>Administrador</option>
                  <option value="director">Director</option>
                  <option value="profesor">Profesor</option>
                </select>
              </div>
              <div class="grupo-formulario ancho-completo">
                <label>Descripción</label>
                <textarea id="descripcion" rows="3">
                  Reporte académico del primer trimestre del año 2023-2024</textarea
                >
              </div>
              <div class="grupo-formulario">
                <label>Estado <span class="requerido">*</span></label>
                <select id="estado" requerido>
                  <option value="borrador">Borrador</option>
                  <option value="generado" selected>Generado</option>
                  <option value="enviado">Enviado</option>
                  <option value="archivado">Archivado</option>
                </select>
              </div>
            </div>

            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='generarReportes.php'"
              >
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

