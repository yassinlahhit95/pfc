<?php
$titulo_pagina = "Ingresar Notas - Super Admin";
include_once "../comunes/nav.php";
?>

      <main class="contenido-principal">
        <!-- Page Header -->
        <div class="encabezado-pagina">
          <div>
            <h1>Ingresar Notas</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Registre las calificaciones de los estudiantes
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verNotas.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
          </div>
        </div>

        <!-- Form Container -->
        <div class="contenedor-formulario">
          <form>
            <div class="cuadricula-formulario">
              <!-- Estudiante -->
              <div class="grupo-formulario">
                <label>Estudiante <span class="requerido">*</span></label>
                <input
                  type="text"
                  placeholder="Buscar estudiante..."
                  requerido
                />
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

              <!-- Tipo de Evaluaci�n -->
              <div class="grupo-formulario">
                <label
                  >Tipo de Evaluaci�n <span class="requerido">*</span></label
                >
                <select requerido>
                  <option value="">Seleccione tipo</option>
                  <option value="examen">Examen</option>
                  <option value="trabajo">Trabajo</option>
                  <option value="participacion">Participaci�n</option>
                </select>
              </div>

              <!-- Nota -->
              <div class="grupo-formulario">
                <label>Nota <span class="requerido">*</span></label>
                <input
                  type="number"
                  step="0.1"
                  min="0"
                  max="10"
                  placeholder="0.0 - 10.0"
                  requerido
                />
              </div>

              <!-- Fecha -->
              <div class="grupo-formulario">
                <label>Fecha <span class="requerido">*</span></label>
                <input type="date" requerido />
              </div>

              <!-- Observaciones -->
              <div class="grupo-formulario ancho-completo">
                <label>Observaciones</label>
                <textarea
                  placeholder="Comentarios sobre la evaluaci�n..."
                ></textarea>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verNotas.php'"
              >
                Cancelar
              </button>
              <button type="submit" class="boton-enviar">
                <i class="fas fa-save"></i>
                Guardar Nota
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

