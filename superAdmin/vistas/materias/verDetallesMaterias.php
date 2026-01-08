<?php
$titulo_pagina = "Detalles Materia - Super Admin";
include_once "../comunes/nav.php";
?>

      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Detalles de la Materia</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Información completa de la materia
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verMaterias.php" class="boton-secundario"
              ><i class="fas fa-arrow-left"></i> Volver</a
            >
            <a href="modificarMaterias.php" class="boton-primario"
              ><i class="fas fa-edit"></i> Editar</a
            >
          </div>
        </div>
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3>
              <i class="fas fa-info-circle"></i> Información de la Materia
            </h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>ID</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">#M001</p>
            </div>
            <div class="grupo-formulario">
              <label>Nombre de la Materia</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Matemáticas
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Código</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">MAT-301</p>
            </div>
            <div class="grupo-formulario">
              <label>Nivel</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">3º ESO</p>
            </div>
            <div class="grupo-formulario">
              <label>Horas Semanales</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">4 horas</p>
            </div>
            <div class="grupo-formulario">
              <label>Profesor Asignado</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Carlos Martínez López
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Departamento</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">Ciencias</p>
            </div>
            <div class="grupo-formulario">
              <label>Estado</label>
              <p style="margin: 0; padding: 12px 0">
                <span class="insignia-estado estado-activo">Activo</span>
              </p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Descripción</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Materia de matemáticas para tercer curso de ESO
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

