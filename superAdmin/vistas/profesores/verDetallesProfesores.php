<?php
$titulo_pagina = "Detalles Profesor - Super Admin";
include_once "../comunes/nav.php";
?>
      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Detalles del Profesor</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Información completa del profesor
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verProfesores.php" class="boton-secundario">
              <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="modificarProfesores.php" class="boton-primario">
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
              <p style="margin: 0; padding: 12px 0; color: #2d3748">#P001</p>
            </div>
            <div class="grupo-formulario">
              <label>Nombre Completo</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Carlos Martínez López
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Email</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                carlos.martinez@email.com
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Teléfono</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                654321987
              </p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Dirección</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Avenida Central 456, Madrid
              </p>
            </div>
          </div>
        </div>

        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-briefcase"></i> Información Profesional</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>Especialidad</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Matemáticas
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Fecha de Ingreso</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                01/09/2020
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Estado</label>
              <p style="margin: 0; padding: 12px 0">
                <span class="insignia-estado estado-activo">Activo</span>
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Años de Experiencia</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">3 años</p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Cursos Asignados</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                3º ESO A, 3º ESO B, 4º ESO A
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

