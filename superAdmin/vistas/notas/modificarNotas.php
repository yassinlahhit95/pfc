<?php
$titulo_pagina = "Modificar Notas - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Modificar Nota</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Actualiza la información de la nota
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verNotas.php" class="boton-secundario">
              <i class="fas fa-arrow-left"></i> Volver
            </a>
          </div>
        </div>

        <div class="contenedor-formulario">
          <form id="formularioModificar">
            <div class="cuadricula-formulario">
              <div class="grupo-formulario">
                <label>Estudiante <span class="requerido">*</span></label>
                <select id="estudiante" requerido>
                  <option value="">Seleccionar estudiante</option>
                  <option value="est1" selected>Juan Pérez García</option>
                  <option value="est2">María López Sánchez</option>
                  <option value="est3">Carlos Rodríguez Martín</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Materia <span class="requerido">*</span></label>
                <select id="materia" requerido>
                  <option value="">Seleccionar materia</option>
                  <option value="matematicas" selected>Matemáticas</option>
                  <option value="lengua">Lengua y Literatura</option>
                  <option value="ingles">Inglés</option>
                  <option value="ciencias">Ciencias</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label
                  >Tipo de Evaluación <span class="requerido">*</span></label
                >
                <select id="tipoEvaluacion" requerido>
                  <option value="">Seleccionar tipo</option>
                  <option value="examen" selected>Examen</option>
                  <option value="trabajo">Trabajo</option>
                  <option value="practica">Práctica</option>
                  <option value="proyecto">Proyecto</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Calificación <span class="requerido">*</span></label>
                <input
                  type="number"
                  id="calificacion"
                  value="8.5"
                  min="0"
                  max="10"
                  step="0.1"
                  requerido
                />
              </div>
              <div class="grupo-formulario">
                <label>Trimestre <span class="requerido">*</span></label>
                <select id="trimestre" requerido>
                  <option value="">Seleccionar trimestre</option>
                  <option value="1" selected>Primer Trimestre</option>
                  <option value="2">Segundo Trimestre</option>
                  <option value="3">Tercer Trimestre</option>
                </select>
              </div>
              <div class="grupo-formulario">
                <label>Fecha <span class="requerido">*</span></label>
                <input type="date" id="fecha" value="2024-01-15" requerido />
              </div>
              <div class="grupo-formulario ancho-completo">
                <label>Observaciones</label>
                <textarea id="observaciones" rows="3">
                    Buen desempeñ o en el examen</textarea
                >
              </div>
              <div class="grupo-formulario">
                <label>Estado <span class="requerido">*</span></label>
                <select id="estado" requerido>
                  <option value="publicado" selected>Publicado</option>
                  <option value="borrador">Borrador</option>
                  <option value="revisado">Revisado</option>
                </select>
              </div>
            </div>

            <div class="acciones-formulario">
              <button
                type="button"
                class="boton-cancelar"
                onclick="window.location.href='verNotas.php'"
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

