<?php
$titulo_pagina = "Detalles Horario - Super Admin";
include_once "../comunes/nav.php";
?>
    <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Detalles del Horario</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Información completa del horario
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verHorarios.php" class="boton-secundario"
              ><i class="fas fa-arrow-left"></i> Volver</a
            >
            <a href="modificarHorarios.php" class="boton-primario"
              ><i class="fas fa-edit"></i> Editar</a
            >
          </div>
        </div>
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-info-circle"></i> Información del Horario</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>ID</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">#H001</p>
            </div>
            <div class="grupo-formulario">
              <label>Curso</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">3º ESO A</p>
            </div>
            <div class="grupo-formulario">
              <label>Materia</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Matemáticas
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Profesor</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Carlos Martínez López
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Día de la Semana</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">Lunes</p>
            </div>
            <div class="grupo-formulario">
              <label>Hora de Inicio</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">09:00</p>
            </div>
            <div class="grupo-formulario">
              <label>Hora de Fin</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">10:00</p>
            </div>
            <div class="grupo-formulario">
              <label>Aula</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">Aula 201</p>
            </div>
            <div class="grupo-formulario">
              <label>Estado</label>
              <p style="margin: 0; padding: 12px 0">
                <span class="insignia-estado estado-activo">Activo</span>
              </p>
            </div>
          </div>
        </div>
      </main>
    </div>
    <!-- Scripts -->
    <script src="../../js/menu.js"></script>
    <script src="../../js/deleteModal.js"></script>
    <script src="../../js/main.js"></script>

</body>
</html>

