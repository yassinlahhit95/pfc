<?php
$titulo_pagina = "Ver Materias - Super Admin";
include_once "../comunes/nav.php";
?>


      <!-- Contenido Principal -->
      <main class="contenido-principal">
        <div class="encabezado-pagina">
          <h1>Repositorio de Materias</h1>
          <div class="acciones-pagina">
            <!-- <button class="boton-primario"><i class="fas fa-plus"></i> Nueva Materia</button> -->
          </div>
        </div>

        <p style="margin-bottom: 20px; color: #718096">
          Selecciona una clase para ver los archivos subidos.
        </p>

        <!-- Grid of Classes -->
        <div class="cuadricula-clases">
          <div
            class="tarjeta-clase"
            onclick="mostrarArchivos('Desarrollo Apps Web', this)"
          >
            <div class="icono-clase">
              <i class="fas fa-laptop-code"></i>
            </div>
            <div class="titulo-clase">DAW</div>
            <div class="subtitulo-clase">Desarrollo de Aplicaciones Web</div>
          </div>
          <div
            class="tarjeta-clase"
            onclick="mostrarArchivos('Admin. Sistemas en Red', this)"
          >
            <div class="icono-clase"><i class="fas fa-server"></i></div>
            <div class="titulo-clase">ASIR</div>
            <div class="subtitulo-clase">
              Admin. Sistemas Informáticos en Red
            </div>
          </div>
          <div
            class="tarjeta-clase"
            onclick="mostrarArchivos('Gestión Administrativa', this)"
          >
            <div class="icono-clase">
              <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="titulo-clase">GA</div>
            <div class="subtitulo-clase">Gestión Administrativa</div>
          </div>
          <div
            class="tarjeta-clase"
            onclick="mostrarArchivos('Animaciones 3D y Juegos', this)"
          >
            <div class="icono-clase"><i class="fas fa-gamepad"></i></div>
            <div class="titulo-clase">3D Y JUEGOS</div>
            <div class="subtitulo-clase">Didáctica y Animación 3D</div>
          </div>
          <div
            class="tarjeta-clase"
            onclick="mostrarArchivos('Sist. Microinformáticos', this)"
          >
            <div class="icono-clase"><i class="fas fa-microchip"></i></div>
            <div class="titulo-clase">SMR</div>
            <div class="subtitulo-clase">
              Sistemas Microinformáticos y Redes
            </div>
          </div>
          <div
            class="tarjeta-clase"
            onclick="mostrarArchivos('Admin. y Finanzas', this)"
          >
            <div class="icono-clase"><i class="fas fa-chart-line"></i></div>
            <div class="titulo-clase">AYF</div>
            <div class="subtitulo-clase">Administración y Finanzas</div>
          </div>
        </div>

        <!-- Files Section (Dynamic) -->
        <div id="seccionArchivos" class="seccion-archivos">
          <div class="encabezado-tarjeta">
            <h3 id="tituloClaseSeleccionada">Archivos - DAW</h3>
            <div class="caja-busqueda">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="Buscar archivo..." />
            </div>
          </div>

          <div class="lista-archivos">
            <!-- File 1 -->
            <div class="elemento-archivo">
              <div class="icono-archivo pdf">
                <i class="fas fa-file-pdf"></i>
              </div>
              <div class="info-archivo">
                <span class="nombre-archivo"
                  >Temario_Matematicas_Tema1.pdf</span
                >
                <div class="meta-archivo">
                  <span style="margin-right: 15px"
                    ><i class="fas fa-user-tie"></i> Prof. García</span
                  >
                  <span><i class="fas fa-calendar"></i> 05/12/2025</span>
                </div>
              </div>
              <button class="boton-eliminar-simple" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>

            <!-- File 2 -->
            <div class="elemento-archivo">
              <div class="icono-archivo word">
                <i class="fas fa-file-word"></i>
              </div>
              <div class="info-archivo">
                <span class="nombre-archivo"
                  >Ejercicios_Lengua_Semana3.docx</span
                >
                <div class="meta-archivo">
                  <span style="margin-right: 15px"
                    ><i class="fas fa-user-tie"></i> Prof. Martínez</span
                  >
                  <span><i class="fas fa-calendar"></i> 03/12/2025</span>
                </div>
              </div>
              <button class="boton-eliminar-simple" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>

            <!-- File 3 -->
            <div class="elemento-archivo">
              <div class="icono-archivo pdf">
                <i class="fas fa-file-pdf"></i>
              </div>
              <div class="info-archivo">
                <span class="nombre-archivo">Historia_Resumen.pdf</span>
                <div class="meta-archivo">
                  <span style="margin-right: 15px"
                    ><i class="fas fa-user-tie"></i> Prof. Lopez</span
                  >
                  <span><i class="fas fa-calendar"></i> 01/12/2025</span>
                </div>
              </div>
              <button class="boton-eliminar-simple" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Scripts -->
    <script src="../../js/menu.js"></script>
    <script src="../../js/main.js"></script>
    <script>
      function mostrarArchivos(nombreClase, elementoTarjeta) {
        // Resaltar tarjeta
        document
          .querySelectorAll(".tarjeta-clase")
          .forEach((c) => c.classList.remove("activa"));
        elementoTarjeta.classList.add("activa");

        // Mostrar secci�n
        const seccion = document.getElementById("seccionArchivos");
        const titulo = document.getElementById("tituloClaseSeleccionada");

        titulo.textContent = "Archivos - " + nombreClase;

        // Reinicio de animaci�n simple
        seccion.classList.remove("visible");
        void seccion.offsetWidth; // forzar reflow
        seccion.classList.add("visible");
      }
    </script>
  </body>
</html>

