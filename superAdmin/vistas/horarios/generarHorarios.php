<?php
$titulo_pagina = "Generar Horario - Super Admin";
include_once "../comunes/nav.php";
?>

      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Generar Horario</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Crea y edita horarios de clases por semana
            </p>
          </div>
          <div class="acciones-pagina">
            <button class="boton-primario" onclick="guardarHorario()">
              <i class="fas fa-save"></i> Guardar Horario
            </button>
          </div>
        </div>

        <!-- Filters Section -->
        <div style="margin-bottom: 20px; display: flex; align-items: center">
          <label style="margin-right: 10px">Curso / Clase:</label>
          <select
            class="control-formulario"
            style="width: auto; margin-right: 20px"
          >
            <option value="1daw">1º DAW</option>
            <option value="2daw">2º DAW</option>
            <option value="1asir">1º ASIR</option>
            <option value="2asir">2º ASIR</option>
            <option value="1ga">1º GA</option>
            <option value="2ga">2º GA</option>
            <option value="13d">1º 3D y Juegos</option>
            <option value="23d">2º 3D y Juegos</option>
            <option value="1smr">1º SMR</option>
            <option value="2smr">2º SMR</option>
            <option value="1ayf">1º AYF</option>
            <option value="2ayf">2º AYF</option>
          </select>
          <label style="margin-right: 10px">Semana:</label>
          <input type="week" class="control-formulario" style="width: auto" />
        </div>

        <!-- Schedule Grid (Monday - Friday) -->
        <div class="cuadricula-horario">
          <!-- Header Row -->
          <div class="encabezado-horario">Hora</div>
          <div class="encabezado-horario">Lunes</div>
          <div class="encabezado-horario">Martes</div>
          <div class="encabezado-horario">Miércoles</div>
          <div class="encabezado-horario">Jueves</div>
          <div class="encabezado-horario">Viernes</div>

          <!-- 08:00 - 09:00 -->
          <div class="ranura-tiempo celda-horario">08:00 - 09:00</div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Programación</option>
              <option>Bases de Datos</option>
              <option>Sistemas</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Programación</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Sistemas</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Entornos</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>FOL</option>
            </select>
          </div>

          <!-- 09:00 - 10:00 -->
          <div class="ranura-tiempo celda-horario">09:00 - 10:00</div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Bases de Datos</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Programación</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Lenguajes</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Sistemas</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Bases de Datos</option>
            </select>
          </div>

          <!-- 10:00 - 11:00 -->
          <div class="ranura-tiempo celda-horario">10:00 - 11:00</div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Programación</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Bases de Datos</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Sistemas</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Programación</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Lenguajes</option>
            </select>
          </div>

          <!-- 11:00 - 11:30 (Recreo) -->
          <div
            class="celda-horario"
            style="
              grid-column: 1 / -1;
              background-color: #edf2f7;
              text-align: center;
              font-weight: bold;
              color: #4a5568;
              min-height: 40px;
            "
          >
            RECREO (11:00 - 11:30)
          </div>

          <!-- 11:30 - 12:30 -->
          <div class="ranura-tiempo celda-horario">11:30 - 12:30</div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Sistemas</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Entornos</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>FOL</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Bases de Datos</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Programación</option>
            </select>
          </div>

          <!-- 12:30 - 13:30 -->
          <div class="ranura-tiempo celda-horario">12:30 - 13:30</div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Lenguajes</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Sistemas</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Programación</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Entornos</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Sistemas</option>
            </select>
          </div>

          <!-- 13:30 - 14:30 -->
          <div class="ranura-tiempo celda-horario">13:30 - 14:30</div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Lenguajes</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Sistemas</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Programación</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Entornos</option>
            </select>
          </div>
          <div class="celda-horario">
            <select class="selector-clase">
              <option value="">-- Materia --</option>
              <option>Sistemas</option>
            </select>
          </div>
        </div>
      </main>
    </div>

    <!-- Scripts -->
    <script src="../../js/menu.js"></script>
    <script src="../../js/main.js"></script>
    <script>
      function guardarHorario() {
        alert("Horario guardado correctamente (simulación).");
      }
    </script>
  </body>
</html>

