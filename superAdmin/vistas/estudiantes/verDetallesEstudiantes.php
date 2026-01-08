<?php
$titulo_pagina = "Detalles Estudiante - Super Admin";
include_once "../comunes/nav.php";
?>

      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Detalles del Estudiante</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Información completa del estudiante
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verEstudiantes.php" class="boton-secundario">
              <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="modificarEstudiantes.php" class="boton-primario">
              <i class="fas fa-edit"></i> Editar
            </a>
          </div>
        </div>

        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-user"></i> Información Personal</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>ID</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">#001</p>
            </div>
            <div class="grupo-formulario">
              <label>Nombre Completo</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Juan Pérez García
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Email</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                juan.perez@email.com
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Teléfono</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                612345678
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Fecha de Nacimiento</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                15/05/2008
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Edad</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">15 años</p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Dirección</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Calle Principal 123, Madrid
              </p>
            </div>
          </div>
        </div>

        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-graduation-cap"></i> Información Académica</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>Curso</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">3º ESO A</p>
            </div>
            <div class="grupo-formulario">
              <label>Fecha de Ingreso</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                01/09/2023
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Estado</label>
              <p style="margin: 0; padding: 12px 0">
                <span class="insignia-estado estado-activo">Activo</span>
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Promedio General</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">8.5</p>
            </div>
          </div>
        </div>

        <div class="tarjeta-panel">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-users"></i> Información de Contacto</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>Tutor/Padre</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                María García
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Teléfono de Contacto</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                687654321
              </p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Email de Contacto</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                maria.garcia@email.com
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

