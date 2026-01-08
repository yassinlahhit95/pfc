<?php
$titulo_pagina = "Modificar Profesor - Super Admin";
include_once "../comunes/nav.php";
?>

    <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Modificar Profesor</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Actualiza la información del profesor
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verProfesores.php" class="boton-secundario">
              <i class="fas fa-arrow-left"></i> Volver
            </a>
          </div>
        </div>

        <div class="contenedor-formulario">
          <form id="formularioModificar">
            <div class="cuadricula-formulario">
              <div class="grupo-formulario">
                <label>Nombre <span class="requerido">*</span></label>
                <input type="text" id="nombre" value="Carlos" requerido />
              </div>
              <div class="grupo-formulario">
                <label>Apellidos <span class="requerido">*</span></label>
                <input
                  type="text"
                  id="apellidos"
                  value="Martínez López"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Email <span class="requerido">*</span></label>
                <input
                  type="email"
                  id="email"
                  value="carlos.martinez@email.com"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Teléfono</label>
                <input type="tel" id="telefono" value="654321987" />
              </div>
              <div class="grupo-formulario">
                <label>Especialidad <span class="requerido">*</span></label>
                <select id="especialidad" requerido>
                  <option value="">Seleccionar especialidad</option>
                  <option value="matematicas" selected>Matemáticas</option>
                  <option value="lengua">Lengua y Literatura</option>
                  <option value="ingles">Inglés</option>
                  <option value="ciencias">Ciencias</option>
                  <option value="historia">Historia</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Fecha de Ingreso <span class="requerido">*</span></label>
                <input
                  type="date"
                  id="fechaIngreso"
                  value="2020-09-01"
                  requerido
                />
              </div>
              <div class="grupo-formulario ancho-completo">
                <label>Dirección</label>
                <input type="text" id="direccion" value="Avenida Central 456" />
              </div>
              <div class="grupo-formulario">
                <label>Estado <span class="requerido">*</span></label>
                <select id="estado" requerido>
                  <option value="activo" selected>Activo</option>
                  <option value="inactivo">Inactivo</option>
                  <option value="vacaciones">De Vacaciones</option>
                </select>
              </div>
            </div>

            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verProfesores.php'"
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