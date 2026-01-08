<?php
$titulo_pagina = "Detalles Notas - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Detalles de la Nota</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Información completa de la nota
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verNotas.php" class="boton-secundario"
              ><i class="fas fa-arrow-left"></i> Volver</a
            >
            >
            <a href="modificarNotas.php" class="boton-primario"
              ><i class="fas fa-edit"></i> Editar</a
            >
          </div>
        </div>
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-info-circle"></i> Información de la Nota</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>ID</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">#N001</p>
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
              <label>Tipo de Evaluación</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">Examen</p>
            </div>
            <div class="grupo-formulario">
              <label>Calificación</label>
              <p
                style="
                  margin: 0;
                  padding: 12px 0;
                  color: #2d3748;
                  font-weight: bold;
                  font-size: 18px;
                "
              >
                8.5
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Trimestre</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Primer Trimestre
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Fecha</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                15/01/2024
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Estado</label>
              <p style="margin: 0; padding: 12px 0">
                <span class="insignia-estado estado-activo">Publicado</span>
              </p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Observaciones</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Buen desempeño en el examen
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

