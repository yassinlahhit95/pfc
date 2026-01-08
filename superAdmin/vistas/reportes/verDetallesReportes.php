<?php
$titulo_pagina = "Detalles Reporte - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Detalles del Reporte</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Información completa del reporte
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="generarReportes.php" class="boton-secundario"
              ><i class="fas fa-arrow-left"></i> Volver</a
            >
            <a href="modificarReportes.php" class="boton-primario"
              ><i class="fas fa-edit"></i> Editar</a
            >
          </div>
        </div>
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-info-circle"></i> Información del Reporte</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>ID</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">#R001</p>
            </div>
            <div class="grupo-formulario">
              <label>Título del Reporte</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Reporte Académico Trimestral
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Tipo de Reporte</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Académico
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Periodo</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Primer Trimestre
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Fecha de Generación</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                20/01/2024
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Generado Por</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Administrador
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Curso/Nivel</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">3º ESO</p>
            </div>
            <div class="grupo-formulario">
              <label>Estado</label>
              <p style="margin: 0; padding: 12px 0">
                <span class="insignia-estado estado-activo">Generado</span>
              </p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Descripción</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Reporte académico del primer trimestre del año 2023-2024
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

