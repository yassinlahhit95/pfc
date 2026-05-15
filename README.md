# AulaPro - Sistema de Gestión Académica Integral (TFG)

**AulaPro** es una plataforma web desarrollada como Trabajo de Fin de Grado (TFG) para el ciclo superior de **Desarrollo de Aplicaciones Web (DAW)** en el CPS Ibaiondo.

El objetivo principal es digitalizar y centralizar la gestión administrativa y académica de un centro de Formación Profesional, sustituyendo el uso de hojas de cálculo dispersas por una solución robusta, escalable y accesible desde cualquier dispositivo.

## 🚀 Propuesta de Valor
AulaPro no es solo un gestor de notas; es una herramienta de comunicación y administración que conecta a los tres pilares del centro:
* **Administración (Directores):** Control total sobre la estructura académica, usuarios, inventario y finanzas.
* **Cuerpo Docente (Profesores):** Herramientas ágiles para la evaluación continua, seguimiento de retos y comunicación con alumnos.
* **Alumnado (Estudiantes):** Un portal personal para consultar expedientes, gestionar su TFG y estar al día con las comunicaciones del centro.

## ✨ Características Principales
- **Arquitectura Multirrol:** Tres portales independientes con permisos granulares.
- **Evaluación Basada en Retos (ABP):** Cálculo automático de notas finales ponderando módulos (75%) y retos transversales (25%).
- **Gestión Documental Automática:** Generación de boletines, certificados y documentación en PDF (FPDF).
- **Comunicación en Tiempo Real:** Notificaciones push al móvil (Firebase FCM) y envío masivo de calificaciones por email (Brevo API).
- **Control de Inventario y Préstamos:** Gestión de dispositivos (portátiles/tablets) con trazabilidad de préstamos a alumnos.
- **Gestión de TFG:** Flujo completo de entrega, revisión y calificación de proyectos finales.
- **Control Financiero:** Seguimiento de pagos de matrícula y mensualidades.

## 🛠️ Stack Tecnológico
* **Backend:** PHP 8.2 (Arquitectura MVC personalizada sin frameworks externos para demostrar dominio de la lógica base).
* **Base de Datos:** MySQL (Diseño relacional optimizado con 20 tablas).
* **Frontend:** HTML5, CSS3 (Diseño Responsive nativo), JavaScript / jQuery.
* **Integraciones:**
    * **Firebase Cloud Messaging:** Notificaciones Push.
    * **Brevo API (SMTP):** Notificaciones por correo electrónico.
    * **FPDF:** Generación dinámica de documentos PDF.
* **Entorno de Desarrollo:** XAMPP / Apache.

## 🔧 Instalación y Configuración
1. **Clonar el repositorio** dentro de la carpeta `htdocs` de tu servidor local (XAMPP).
2. **Base de Datos:**
   - Crear una base de datos llamada `pfc` en phpMyAdmin.
   - Importar el archivo `database.sql` situado en la raíz del proyecto.
3. **Configuración de Conexión:**
   - Revisar `modelos/conectar.php` para asegurar que las credenciales de DB (host, user, pass) coinciden con tu entorno.
4. **APIs y Secretos:**
   - Configurar las API Keys de Brevo y Firebase en `config/secrets.php` para habilitar las notificaciones.

---
**Autor:** Yassin Lahhit
*Desarrollo de Aplicaciones Web - CPS Ibaiondo (Bilbao)*
Curso 2025-2026

`


```
pfc
├─ CLAUDE.md
├─ config
├─ controladores
│  ├─ admin
│  │  ├─ academico
│  │  │  ├─ calificarModulos.php
│  │  │  ├─ calificarRetos.php
│  │  │  └─ enviarNotasMasivo.php
│  │  ├─ anuncios
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  └─ insertar.php
│  │  ├─ ciclos
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  └─ insertar.php
│  │  ├─ directores
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  └─ insertar.php
│  │  ├─ estudiantes
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  ├─ insertar.php
│  │  │  └─ subirTFG.php
│  │  ├─ eventos
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  └─ insertar.php
│  │  ├─ inventario
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  ├─ devolver.php
│  │  │  ├─ insertar.php
│  │  │  └─ prestar.php
│  │  ├─ mensajes
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  └─ insertar.php
│  │  ├─ modulos
│  │  │  ├─ actualizar.php
│  │  │  ├─ actualizarProfesores.php
│  │  │  ├─ borrar.php
│  │  │  └─ insertar.php
│  │  ├─ pagos
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  ├─ insertar.php
│  │  │  └─ obtenerEstadoFinanciero.php
│  │  ├─ pfc
│  │  │  ├─ borrar.php
│  │  │  ├─ calificar.php
│  │  │  └─ gestionar.php
│  │  ├─ profesores
│  │  │  ├─ actualizar.php
│  │  │  ├─ actualizarModulos.php
│  │  │  ├─ borrar.php
│  │  │  └─ insertar.php
│  │  └─ retos
│  │     ├─ actualizar.php
│  │     ├─ borrar.php
│  │     ├─ calificar.php
│  │     └─ insertar.php
│  ├─ comunes
│  │  ├─ email_helper.php
│  │  └─ notificaciones_grades.php
│  ├─ contacto_landing.php
│  ├─ estudiantes
│  │  ├─ mensajes
│  │  │  ├─ insertar.php
│  │  │  └─ marcar_visto.php
│  │  ├─ perfil
│  │  │  └─ actualizar.php
│  │  └─ pfc
│  │     ├─ eliminar.php
│  │     └─ subir.php
│  ├─ firebase
│  │  ├─ firebase_helper.php
│  │  └─ guardar_token.php
│  ├─ logout.php
│  ├─ profesores
│  │  ├─ calificaciones
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  ├─ calificarModulos_prof.php
│  │  │  ├─ calificarRetos.php
│  │  │  └─ insertar.php
│  │  ├─ estudiantes
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  └─ insertar.php
│  │  ├─ mensajes
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  ├─ insertar.php
│  │  │  └─ marcar_visto.php
│  │  ├─ perfil
│  │  │  └─ actualizar.php
│  │  ├─ pfc
│  │  │  ├─ actualizar.php
│  │  │  ├─ borrar.php
│  │  │  └─ calificar.php
│  │  └─ retos
│  │     ├─ actualizar.php
│  │     ├─ borrar.php
│  │     └─ insertar.php
│  └─ validacion.php
├─ database.sql
├─ diagramas.md
├─ escribir.md
├─ features.txt
├─ firebase-messaging-sw.js
├─ IMPLEMENTACION_COMPLETA.md
├─ index.html
├─ memoria-tfg.docx
├─ modelos
│  ├─ anuncios.php
│  ├─ calificaciones.php
│  ├─ ciclos.php
│  ├─ conectar.php
│  ├─ directores.php
│  ├─ estudiantes.php
│  ├─ eventos.php
│  ├─ inventario.php
│  ├─ modulos.php
│  ├─ niveles.php
│  ├─ pagos.php
│  ├─ panelDeControl.php
│  ├─ profesores.php
│  ├─ reclamaciones.php
│  ├─ retos.php
│  └─ tfg.php
├─ prompt_casos_uso.md
├─ prompt_entidad_relacion.md
├─ public
│  ├─ css
│  │  ├─ estilo.css
│  │  ├─ landing.css
│  │  ├─ login.css
│  │  ├─ notificaciones.css
│  │  └─ responsive.css
│  ├─ imagenes
│  │  ├─ aulapro.jpeg
│  │  ├─ aulapro.png
│  │  ├─ favicon.ico
│  │  ├─ fondo.png
│  │  ├─ fondo.webp
│  │  ├─ fp.png
│  │  └─ gobierno.png
│  ├─ js
│  │  ├─ filtros.js
│  │  ├─ firebase
│  │  │  ├─ firebase-init.js
│  │  │  ├─ firebase.js
│  │  │  └─ notificaciones-ui.js
│  │  ├─ menu.js
│  │  ├─ paginacion.js
│  │  ├─ profesores-form.js
│  │  └─ retos.js
│  └─ uploads
├─ README.md
├─ TFG_Proyecto.txt
├─ vistas
│  ├─ admin
│  │  ├─ academico
│  │  │  ├─ calificacionesModulos.php
│  │  │  ├─ calificacionesRetos.php
│  │  │  ├─ calificacionesTFG.php
│  │  │  └─ resultadosFinales.php
│  │  ├─ anuncios
│  │  │  ├─ agregarAnuncios.php
│  │  │  ├─ detallesAnuncio.php
│  │  │  ├─ gestionAnuncios.php
│  │  │  └─ modificarAnuncios.php
│  │  ├─ ciclos
│  │  │  ├─ agregarCiclos.php
│  │  │  ├─ modificarCiclos.php
│  │  │  └─ verCiclos.php
│  │  ├─ comunes
│  │  │  ├─ footer.php
│  │  │  ├─ index.php
│  │  │  └─ nav.php
│  │  ├─ directores
│  │  │  ├─ agregarDirectores.php
│  │  │  ├─ modificarDirectores.php
│  │  │  ├─ verDetallesDirectores.php
│  │  │  └─ verDirectores.php
│  │  ├─ estudiantes
│  │  │  ├─ agregarEstudiantes.php
│  │  │  ├─ modificarEstudiantes.php
│  │  │  ├─ verDetallesEstudiantes.php
│  │  │  └─ verEstudiantes.php
│  │  ├─ eventos
│  │  │  ├─ agregarEvento.php
│  │  │  ├─ gestionEventos.php
│  │  │  └─ modificarEvento.php
│  │  ├─ inicio
│  │  │  └─ dashboard.php
│  │  ├─ inventario
│  │  │  ├─ agregarArticulo.php
│  │  │  ├─ agregarPrestamo.php
│  │  │  ├─ gestionarPrestamos.php
│  │  │  ├─ modificarArticulo.php
│  │  │  └─ verInventario.php
│  │  ├─ mensajes
│  │  │  ├─ agregar.php
│  │  │  ├─ detalles.php
│  │  │  ├─ lista.php
│  │  │  └─ modificarReclamacion.php
│  │  ├─ modulos
│  │  │  ├─ agregarModulos.php
│  │  │  ├─ asignarProfesorModulo.php
│  │  │  ├─ modificarModulos.php
│  │  │  └─ verModulos.php
│  │  ├─ pagos
│  │  │  ├─ agregarPagos.php
│  │  │  ├─ historialEstudiante.php
│  │  │  ├─ modificarPagos.php
│  │  │  └─ verPagosGeneral.php
│  │  ├─ pfc
│  │  ├─ profesores
│  │  │  ├─ agregarProfesores.php
│  │  │  ├─ asignarModulos.php
│  │  │  ├─ modificarProfesores.php
│  │  │  ├─ verDetallesProfesores.php
│  │  │  └─ verProfesores.php
│  │  └─ retos
│  │     ├─ agregarRetos.php
│  │     ├─ calificarReto.php
│  │     ├─ modificarRetos.php
│  │     └─ verRetos.php
│  ├─ estudiantes
│  │  ├─ academico
│  │  │  └─ resultadosFinales.php
│  │  ├─ anuncios
│  │  │  └─ lista.php
│  │  ├─ calificaciones
│  │  │  ├─ lista.php
│  │  │  └─ retos.php
│  │  ├─ comunes
│  │  │  ├─ footer.php
│  │  │  └─ nav.php
│  │  ├─ eventos
│  │  │  └─ lista.php
│  │  ├─ inicio
│  │  │  └─ dashboard.php
│  │  ├─ mensajes
│  │  │  ├─ agregar.php
│  │  │  ├─ detalles.php
│  │  │  └─ lista.php
│  │  ├─ pagos
│  │  │  └─ lista.php
│  │  ├─ perfil
│  │  │  ├─ editar.php
│  │  │  └─ ver.php
│  │  ├─ pfc
│  │  │  ├─ lista.php
│  │  │  └─ subir.php
│  │  └─ retos
│  │     └─ lista.php
│  ├─ login.php
│  └─ profesores
│     ├─ academico
│     │  └─ resultadosFinales.php
│     ├─ anuncios
│     │  └─ lista.php
│     ├─ calificaciones
│     │  ├─ agregar.php
│     │  ├─ editar.php
│     │  ├─ lista.php
│     │  ├─ retos.php
│     │  └─ tfg.php
│     ├─ ciclos
│     │  └─ lista.php
│     ├─ comunes
│     │  ├─ footer.php
│     │  └─ nav.php
│     ├─ estudiantes
│     │  ├─ agregar.php
│     │  ├─ detalles.php
│     │  ├─ editar.php
│     │  └─ lista.php
│     ├─ eventos
│     │  └─ lista.php
│     ├─ inicio
│     │  └─ dashboard.php
│     ├─ mensajes
│     │  ├─ agregar.php
│     │  ├─ detalles.php
│     │  ├─ editar.php
│     │  └─ lista.php
│     ├─ modulos
│     │  └─ lista.php
│     ├─ perfil
│     │  ├─ editar.php
│     │  └─ ver.php
│     ├─ pfc
│     │  ├─ editar.php
│     │  └─ lista.php
│     └─ retos
│        ├─ agregar.php
│        ├─ editar.php
│        └─ lista.php
└─ ~$moria-tfg.docx

```