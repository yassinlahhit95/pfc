<?php
$titulo_pagina = "Detalles Calificación - Super Admin";
include_once "../comunes/nav.php";
?>   


      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Detalles de la Calificación</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Información completa de la calificación final
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verCalificaciones.php" class="boton-secundario"
              ><i class="fas fa-arrow-left"></i> Volver</a
            >
            <a href="modificarCalificaciones.php" class="boton-primario"
              ><i class="fas fa-edit"></i> Editar</a
            >
          </div>
        </div>
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3>
              <i class="fas fa-info-circle"></i> Información de la Calificación
            </h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>ID</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">#CAL001</p>
            </div>
            <div class="grupo-formulario">
              <label>Estudiante</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Juan Pérez García
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Materia</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Matemáticas
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Año Académico</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                2023-2024
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Trimestre 1</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">7.5</p>
            </div>
            <div class="grupo-formulario">
              <label>Trimestre 2</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">8.0</p>
            </div>
            <div class="grupo-formulario">
              <label>Trimestre 3</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">8.5</p>
            </div>
            <div class="grupo-formulario">
              <label>Calificaci�n Final</label>
              <p
                style="
                  margin: 0;
                  padding: 12px 0;
                  color: #2d3748;
                  font-weight: bold;
                  font-size: 20px;
                "
              >
                8.0
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Resultado</label>
              <p style="margin: 0; padding: 12px 0">
                <span class="insignia-estado estado-activo">Aprobado</span>
              </p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Observaciones</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Buen rendimiento durante todo el año académico
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

