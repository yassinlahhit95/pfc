<?php
$titulo_pagina = "Modificar Director - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Modificar Director</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Actualiza la informaci�n del director
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verDirectores.php" class="boton-secundario">
              <i class="fas fa-arrow-left"></i> Volver
            </a>
          </div>
        </div>

        <div class="contenedor-formulario">
          <form id="formularioModificar">
            <div class="cuadricula-formulario">
              <div class="grupo-formulario">
                <label>Nombre <span class="requerido">*</span></label>
                <input type="text" id="nombre" value="Ana" requerido />
              </div>
              <div class="grupo-formulario">
                <label>Apellidos <span class="requerido">*</span></label>
                <input
                  type="text"
                  id="apellidos"
                  value="Garc�a Fern�ndez"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Email <span class="requerido">*</span></label>
                <input
                  type="email"
                  id="email"
                  value="ana.garcia@email.com"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Tel�fono</label>
                <input type="tel" id="telefono" value="698765432" />
              </div>
              <div class="grupo-formulario">
                <label>Departamento <span class="requerido">*</span></label>
                <select id="departamento" requerido>
                  <option value="">Seleccionar departamento</option>
                  <option value="academico" selected>Acad�mico</option>
                  <option value="administrativo">Administrativo</option>
                  <option value="general">General</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Fecha de Ingreso <span class="requerido">*</span></label>
                <input
                  type="date"
                  id="fechaIngreso"
                  value="2018-01-15"
                  requerido
                />
              </div>
              <div class="grupo-formulario ancho-completo">
                <label>Direcci�n</label>
                <input type="text" id="direccion" value="Plaza Mayor 789" />
              </div>
              <div class="grupo-formulario">
                <label>Estado <span class="requerido">*</span></label>
                <select id="estado" requerido>
                  <option value="activo" selected>Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>
            </div>

            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verDirectores.php'"
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

