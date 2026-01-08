<?php
$titulo_pagina = "Agregar Materia - Super Admin";
include_once "../comunes/nav.php";
?>

      <main class="contenido-principal">
        <!-- Page Header -->
        <div class="encabezado-pagina">
          <div>
            <h1>Agregar Materia</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Complete el formulario para registrar una nueva materia
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verMaterias.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
          </div>
        </div>

        <!-- Form Container -->
        <div class="contenedor-formulario">
          <form>
            <div class="cuadricula-formulario">
              <!-- Nombre de la Materia -->
              <div class="grupo-formulario">
                <label
                  >Nombre de la Materia <span class="requerido">*</span></label
                >
                <input type="text" placeholder="Ej: Matem�ticas" requerido />
              </div>

              <!-- Código -->
              <div class="grupo-formulario">
                <label>Código <span class="requerido">*</span></label>
                <input type="text" placeholder="Ej: MAT-101" requerido />
              </div>

              <!-- Curso -->
              <div class="grupo-formulario">
                <label>Curso <span class="requerido">*</span></label>
                <select requerido>
                  <option value="">Seleccione un curso</option>
                  <option value="1eso">1º ESO</option>
                  <option value="2eso">2º ESO</option>
                  <option value="3eso">3º ESO</option>
                  <option value="4eso">4º ESO</option>
                </select>
              </div>

              <!-- Profesor -->
              <div class="grupo-formulario">
                <label>Profesor Asignado</label>
                <input type="text" placeholder="Nombre del profesor" />
              </div>

              <!-- Créditos/Horas -->
              <div class="grupo-formulario">
                <label>Horas Semanales</label>
                <input type="number" placeholder="Ej: 4" />
              </div>

              <!-- Descripción -->
              <div class="grupo-formulario ancho-completo">
                <label>Descripción</label>
                <textarea
                  placeholder="Descripción del contenido de la materia..."
                ></textarea>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verMaterias.php'"
              >
                Cancelar
              </button>
              <button type="submit" class="boton-enviar">
                <i class="fas fa-save"></i>
                Guardar Materia
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

