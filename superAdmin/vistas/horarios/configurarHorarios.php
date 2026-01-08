<?php
$titulo_pagina = "Configurar Horario - Super Admin";
include_once "../comunes/nav.php";
?>


      <main class="contenido-principal">
        <!-- Page Header -->
        <div class="encabezado-pagina">
          <div>
            <h1>Configurar Horario</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Asigne materias y profesores a los horarios
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verHorarios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
          </div>
        </div>

        <!-- Form Container -->
        <div class="contenedor-formulario">
          <form>
            <div class="cuadricula-formulario">
              <!-- Curso -->
              <div class="grupo-formulario">
                <label>Curso <span class="requerido">*</span></label>
                <select requerido>
                  <option value="">Seleccione un curso</option>
                  <option value="1eso-a">1� ESO A</option>
                  <option value="1eso-b">1� ESO B</option>
                  <!-- M�s opciones -->
                </select>
              </div>

              <!-- D�a -->
              <div class="grupo-formulario">
                <label>D�a de la Semana <span class="requerido">*</span></label>
                <select requerido>
                  <option value="">Seleccione un d�a</option>
                  <option value="lunes">Lunes</option>
                  <option value="martes">Martes</option>
                  <option value="miercoles">Mi�rcoles</option>
                  <option value="jueves">Jueves</option>
                  <option value="viernes">Viernes</option>
                </select>
              </div>

              <!-- Hora Inicio -->
              <div class="grupo-formulario">
                <label>Hora Inicio <span class="requerido">*</span></label>
                <input type="time" requerido />
              </div>

              <!-- Hora Fin -->
              <div class="grupo-formulario">
                <label>Hora Fin <span class="requerido">*</span></label>
                <input type="time" requerido />
              </div>

              <!-- Materia -->
              <div class="grupo-formulario">
                <label>Materia <span class="requerido">*</span></label>
                <select requerido>
                  <option value="">Seleccione una materia</option>
                  <option value="matematicas">Matem�ticas</option>
                  <option value="historia">Historia</option>
                  <!-- M�s opciones -->
                </select>
              </div>

              <!-- Profesor -->
              <div class="grupo-formulario">
                <label>Profesor <span class="requerido">*</span></label>
                <select requerido>
                  <option value="">Seleccione un profesor</option>
                  <option value="p1">Laura Mart�nez</option>
                  <option value="p2">Roberto S�nchez</option>
                  <!-- M�s opciones -->
                </select>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verHorarios.php'"
              >
                Cancelar
              </button>
              <button type="submit" class="boton-enviar">
                <i class="fas fa-save"></i>
                Guardar Horario
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

