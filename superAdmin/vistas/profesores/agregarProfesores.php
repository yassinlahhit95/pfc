<?php
$titulo_pagina = "Agregar Profesor - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <!-- Page Header -->
        <div class="encabezado-pagina">
          <div>
            <h1>Agregar Profesor</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Complete el formulario para registrar un nuevo profesor
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verProfesores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
          </div>
        </div>

        <!-- Form Container -->
        <div class="contenedor-formulario">
          <form>
            <div class="cuadricula-formulario">
              <!-- Nombre -->
              <div class="grupo-formulario">
                <label>Nombre completo<span class="requerido">*</span></label>
                <input type="text" placeholder="Ingrese el nombre y apellidos" requerido />
              </div>

              <!-- Apellidos -->
              <div class="grupo-formulario">
                <label>DNI/NIE/PASAPORTE <span class="requerido">*</span></label>
                <input
                  type="text"
                  placeholder="Ingrese los apellidos"
                  requerido
                />
              </div>

              <!-- Email -->
              <div class="grupo-formulario">
                <label>Email <span class="requerido">*</span></label>
                <input type="email" placeholder="ejemplo@email.com" requerido />
              </div>

              <!-- Teléfono -->
              <div class="grupo-formulario">
                <label>Teléfono</label>
                <input type="tel" placeholder="+34 600 000 000" />
              </div>

              <!-- Especialidad -->
              <div class="grupo-formulario">
                <label>Especialidad <span class="requerido">*</span></label>
                <input
                  type="text"
                  placeholder="Matemáticas, Historia..."
                  requerido
                />
              </div>

              <!-- Fecha de Contratación -->
              <div class="grupo-formulario">
                <label
                  >Fecha de Contratación <span class="requerido">*</span></label
                >
                <input type="date" requerido />
              </div>

              <!-- Dirección -->
              <div class="grupo-formulario ancho-completo">
                <label>Dirección</label>
                <input type="text" placeholder="Calle, número, piso..." />
              </div>

              <!-- Observaciones -->
              <div class="grupo-formulario ancho-completo">
                <label>Observaciones</label>
                <textarea
                  placeholder="Notas adicionales sobre el profesor..."
                ></textarea>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verProfesores.php'"
              >
                Cancelar
              </button>
              <button type="submit" class="boton-enviar">
                <i class="fas fa-save"></i>
                Guardar Profesor
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

