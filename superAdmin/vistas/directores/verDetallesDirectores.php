<?php
$titulo_pagina = "Detalles Director - Super Admin";
include_once "../comunes/nav.php";
?>

    <main class="contenido-principal">
        <div class="encabezado-pagina">
          <div>
            <h1>Detalles del Director</h1>
            <p style="color: #8f9bba; margin-top: 5px">
              Información completa del director
            </p>
          </div>
          <div class="acciones-pagina">
            <a href="verDirectores.php" class="boton-secundario"
              ><i class="fas fa-arrow-left"></i> Volver</a
            >
            <a href="modificarDirectores.php" class="boton-primario"
              ><i class="fas fa-edit"></i> Editar</a
            >
          </div>
        </div>
        <div class="tarjeta-panel" style="margin-bottom: 20px">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-user"></i> Información Personal</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>ID</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">#D001</p>
            </div>
            <div class="grupo-formulario">
              <label>Nombre Completo</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Ana García Fernández
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Email</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                ana.garcia@email.com
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Teléfono</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                698765432
              </p>
            </div>
            <div class="grupo-formulario ancho-completo">
              <label>Dirección</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Plaza Mayor 789, Madrid
              </p>
            </div>
          </div>
        </div>
        <div class="tarjeta-panel">
          <div class="encabezado-tarjeta">
            <h3><i class="fas fa-briefcase"></i> Información Profesional</h3>
          </div>
          <div class="cuadricula-formulario">
            <div class="grupo-formulario">
              <label>Departamento</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                Académico
              </p>
            </div>
            <div class="grupo-formulario">
              <label>Fecha de Ingreso</label>
              <p style="margin: 0; padding: 12px 0; color: #2d3748">
                15/01/2018
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
              <p style="margin: 0; padding: 12px 0; color: #2d3748">6 años</p>
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

