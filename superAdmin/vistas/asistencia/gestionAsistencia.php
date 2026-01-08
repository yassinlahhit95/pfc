<?php
$titulo_pagina = "Gestionar Asistencia - Super Admin";
include_once "../comunes/nav.php";
?>
 
 
 
 
 
    <!-- Contenido Principal -->
      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <h1>Gestionar Asistencia</h1>
          <div class="acciones-pagina">
            <button class="boton-secundario" onclick="window.history.back()">
              <i class="fas fa-arrow-left"></i> Volver
            </button>
          </div>
        </div>

        <!-- Seleccionar Curso -->
        <p style="margin-bottom: 20px; color: #718096">
          Selecciona un curso para gestionar la asistencia.
        </p>

        <!-- Grid de Cursos -->
        <div class="cuadricula-clases">
          <div class="tarjeta-clase" onclick="mostrarAsistencia('DAW', this)">
            <div class="icono-clase"><i class="fas fa-laptop-code"></i></div>
            <div class="titulo-clase">DAW</div>
            <div class="subtitulo-clase">Desarrollo de Aplicaciones Web</div>
          </div>
          <div class="tarjeta-clase" onclick="mostrarAsistencia('ASIR', this)">
            <div class="icono-clase"><i class="fas fa-server"></i></div>
            <div class="titulo-clase">ASIR</div>
            <div class="subtitulo-clase">Admin. Sistemas Informaticos</div>
          </div>
          <div class="tarjeta-clase" onclick="mostrarAsistencia('GA', this)">
            <div class="icono-clase"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="titulo-clase">GA</div>
            <div class="subtitulo-clase">Gestion Administrativa</div>
          </div>
          <div class="tarjeta-clase" onclick="mostrarAsistencia('SMR', this)">
            <div class="icono-clase"><i class="fas fa-microchip"></i></div>
            <div class="titulo-clase">SMR</div>
            <div class="subtitulo-clase">Sistemas Microinformaticos</div>
          </div>
          <div class="tarjeta-clase" onclick="mostrarAsistencia('AYF', this)">
            <div class="icono-clase"><i class="fas fa-chart-line"></i></div>
            <div class="titulo-clase">AYF</div>
            <div class="subtitulo-clase">Administracion y Finanzas</div>
          </div>
        </div>

        <!-- Lista de Estudiantes -->
        <!-- Seccion de Asistencia (oculta inicialmente) -->
        <div id="seccionAsistencia" class="seccion-archivos">
          <div class="encabezado-tarjeta">
            <h3 id="tituloCursoSeleccionado">Asistencia - DAW</h3>
            <div style="display: flex; gap: 15px; align-items: center;">
              <input type="date" class="control-formulario" value="2025-12-08" style="width: auto;" />
              <button class="boton-primario"><i class="fas fa-save"></i> Guardar</button>
            </div>
          </div>

          <!-- Tabla de Estudiantes -->
          <div class="contenedor-tabla">
          <div class="encabezado-tarjeta">
            <h3>Listado de Alumnos</h3>
            <div class="acciones-pagina">
              <button class="boton-primario">
                <i class="fas fa-save"></i> Guardar Asistencia
              </button>
            </div>
          </div>

          <table class="tabla-datos">
            <thead>
              <tr>
                <th>Estudiante</th>
                <th>Estado</th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody>
              <!-- Fila Ejemplo 1 -->
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 10px">
                    <div
                      style="
                        width: 35px;
                        height: 35px;
                        border-radius: 50%;
                        background-color: #e2e8f0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #718096;
                      "
                    >
                      <i class="fas fa-user"></i>
                    </div>
                    <span style="font-weight: 500; color: #2d3748"
                      >Ana García López</span
                    >
                  </div>
                </td>
                <td>
                  <div style="display: flex; gap: 15px">
                    <label
                      style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                      "
                    >
                      <input type="radio" name="asistencia_1" checked />
                      Presente
                    </label>
                    <label
                      style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                        color: #e53e3e;
                      "
                    >
                      <input type="radio" name="asistencia_1" /> Ausente
                    </label>
                    <label
                      style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                        color: #d69e2e;
                      "
                    >
                      <input type="radio" name="asistencia_1" /> Retraso
                    </label>
                  </div>
                </td>
                <td>
                  <input
                    type="text"
                    placeholder="Añadir comentario..."
                    class="control-formulario"
                  />
                </td>
              </tr>
              <!-- Fila Ejemplo 2 -->
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 10px">
                    <div
                      style="
                        width: 35px;
                        height: 35px;
                        border-radius: 50%;
                        background-color: #e2e8f0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #718096;
                      "
                    >
                      <i class="fas fa-user"></i>
                    </div>
                    <span style="font-weight: 500; color: #2d3748"
                      >Carlos Ruiz Mateo</span
                    >
                  </div>
                </td>
                <td>
                  <div style="display: flex; gap: 15px">
                    <label
                      style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                      "
                    >
                      <input type="radio" name="asistencia_2" checked />
                      Presente
                    </label>
                    <label
                      style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                        color: #e53e3e;
                      "
                    >
                      <input type="radio" name="asistencia_2" /> Ausente
                    </label>
                    <label
                      style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                        color: #d69e2e;
                      "
                    >
                      <input type="radio" name="asistencia_2" /> Retraso
                    </label>
                  </div>
                </td>
                <td>
                  <input
                    type="text"
                    placeholder="Añadir comentario..."
                    class="control-formulario"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
          </div> <!-- cierra contenedor-tabla -->
        </div> <!-- cierra seccionAsistencia -->
      </main>
    </div>

    <script src="../../js/menu.js"></script>
    <script>
      function mostrarAsistencia(nombreCurso, elementoTarjeta) {
        document.querySelectorAll(".tarjeta-clase").forEach(c => c.classList.remove("activa"));
        elementoTarjeta.classList.add("activa");
        const seccion = document.getElementById("seccionAsistencia");
        const titulo = document.getElementById("tituloCursoSeleccionado");
        titulo.textContent = "Asistencia - " + nombreCurso;
        seccion.classList.remove("visible");
        void seccion.offsetWidth;
        seccion.classList.add("visible");
      }
    </script>
    <script src="../../js/main.js"></script>
  </body>
</html>

