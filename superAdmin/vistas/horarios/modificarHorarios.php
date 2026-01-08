<?php
$titulo_pagina = "Modificar Horario - Super Admin";
include_once "../comunes/nav.php";
?>

      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Modificar Horario</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Actualiza la informaci�n del horario
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verHorarios.php" class="boton-secundario">
              <i class="fas fa-arrow-left"></i> Volver
            </a>
          </div>
        </div>

        <div class="contenedor-formulario">
          <form id="formularioModificar">
            <div class="cuadricula-formulario">
              <div class="grupo-formulario">
                <label>Curso <span class="requerido">*</span></label>
                <select id="curso" requerido>
                  <option value="">Seleccionar curso</option>
                  <option value="1eso-a">1� ESO A</option>
                  <option value="2eso-a">2� ESO A</option>
                  <option value="3eso-a" selected>3� ESO A</option>
                  <option value="4eso-a">4� ESO A</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Materia <span class="requerido">*</span></label>
                <select id="materia" requerido>
                  <option value="">Seleccionar materia</option>
                  <option value="matematicas" selected>Matem�ticas</option>
                  <option value="lengua">Lengua y Literatura</option>
                  <option value="ingles">Ingl�s</option>
                  <option value="ciencias">Ciencias</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Profesor <span class="requerido">*</span></label>
                <select id="profesor" requerido>
                  <option value="">Seleccionar profesor</option>
                  <option value="prof1" selected>Carlos Mart�nez L�pez</option>
                  <option value="prof2">Laura S�nchez Garc�a</option>
                  <option value="prof3">Miguel Torres Ruiz</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>D�a de la Semana <span class="requerido">*</span></label>
                <select id="dia" requerido>
                  <option value="">Seleccionar d�a</option>
                  <option value="lunes" selected>Lunes</option>
                  <option value="martes">Martes</option>
                  <option value="miercoles">Mi�rcoles</option>
                  <option value="jueves">Jueves</option>
                  <option value="viernes">Viernes</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Hora de Inicio <span class="requerido">*</span></label>
                <input type="time" id="horaInicio" value="09:00" requerido />
              </div>
              <div class="grupo-formulario">
                <label>Hora de Fin <span class="requerido">*</span></label>
                <input type="time" id="horaFin" value="10:00" requerido />
              </div>
              <div class="grupo-formulario">
                <label>Aula <span class="requerido">*</span></label>
                <input type="text" id="aula" value="Aula 201" requerido />
              </div>
              <div class="grupo-formulario">
                <label>Estado <span class="requerido">*</span></label>
                <select id="estado" requerido>
                  <option value="activo" selected>Activo</option>
                  <option value="inactivo">Inactivo</option>
                  <option value="cancelado">Cancelado</option>
                </select>
              </div>
            </div>

            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verHorarios.php'"
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

