<?php
$titulo_pagina = "Modificar Estudiante - Super Admin";
include_once "../comunes/nav.php";
?>

      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Modificar Estudiante</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Actualiza la información del estudiante
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verEstudiantes.php" class="boton-secundario">
              <i class="fas fa-arrow-left"></i> Volver
            </a>
          </div>
        </div>

        <div class="contenedor-formulario">
          <form id="formularioModificar">
            <div class="cuadricula-formulario">
              <div class="grupo-formulario">
                <label>Nombre <span class="requerido">*</span></label>
                <input type="text" id="nombre" value="Juan" requerido />
              </div>
              <div class="grupo-formulario">
                <label>Apellidos <span class="requerido">*</span></label>
                <input
                  type="text"
                  id="apellidos"
                  value="Pérez García"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Email <span class="requerido">*</span></label>
                <input
                  type="email"
                  id="email"
                  value="juan.perez@email.com"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Teléfono</label>
                <input type="tel" id="telefono" value="612345678" />
              </div>
              <div class="grupo-formulario">
                <label
                  >Fecha de Nacimiento <span class="requerido">*</span></label
                >
                <input
                  type="date"
                  id="fechaNacimiento"
                  value="2008-05-15"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Curso <span class="requerido">*</span></label>
                <select id="curso" requerido>
                  <option value="">Seleccionar curso</option>
                  <option value="1eso">1º ESO</option>
                  <option value="2eso">2º ESO</option>
                  <option value="3eso" selected>3º ESO</option>
                  <option value="4eso">4º ESO</option>
                </select>
              </div>
              <div class="grupo-formulario ancho-completo">
                <label>Dirección</label>
                <input type="text" id="direccion" value="Calle Principal 123" />
              </div>
              <div class="grupo-formulario">
                <label>Estado <span class="requerido">*</span></label>
                <select id="estado" requerido>
                  <option value="activo" selected>Activo</option>
                  <option value="inactivo">Inactivo</option>
                  <option value="pendiente">Pendiente</option>
                </select>
              </div>
            </div>

            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verEstudiantes.php'"
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

