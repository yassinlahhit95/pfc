<?php
$titulo_pagina = "Agregar Director - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <!-- Page Header -->
        <div class="encabezado-pagina">
          <div>
            <h1>Agregar Director</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Complete el formulario para registrar un nuevo director
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verDirectores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
          </div>
        </div>

        <!-- Form Container -->
        <div class="contenedor-formulario">
          <form>
            <div class="cuadricula-formulario">
              <!-- Nombre -->
              <div class="grupo-formulario">
                <label>Nombre <span class="requerido">*</span></label>
                <input type="text" placeholder="Ingrese el nombre" requerido />
              </div>

              <!-- Apellidos -->
              <div class="grupo-formulario">
                <label>Apellidos <span class="requerido">*</span></label>
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

              <!-- Tel�fono -->
              <div class="grupo-formulario">
                <label>Tel�fono</label>
                <input type="tel" placeholder="+34 600 000 000" />
              </div>

              <!-- Departamento -->
              <div class="grupo-formulario">
                <label>Departamento <span class="requerido">*</span></label>
                <input
                  type="text"
                  placeholder="Ciencias, Humanidades..."
                  requerido
                />
              </div>

              <!-- Fecha de Inicio -->
              <div class="grupo-formulario">
                <label>Fecha de Inicio <span class="requerido">*</span></label>
                <input type="date" requerido />
              </div>

              <!-- Observaciones -->
              <div class="grupo-formulario ancho-completo">
                <label>Observaciones</label>
                <textarea
                  placeholder="Notas adicionales sobre el director..."
                ></textarea>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verDirectores.php'"
              >
                Cancelar
              </button>
              <button type="submit" class="boton-enviar">
                <i class="fas fa-save"></i>
                Guardar Director
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

