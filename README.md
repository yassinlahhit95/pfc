# AulaPro - Sistema de Gestión Académica Premium (TFG)

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**AulaPro** es una plataforma web integral de alto nivel diseñada para la gestión de centros de formación y escuelas, con un enfoque especial en Ciclos de Formación Profesional (FP). Desarrollada como Trabajo de Fin de Grado (TFG), la plataforma combina una arquitectura robusta con una experiencia de usuario fluida (Smooth UI) para modernizar la administración académica.

## 🚀 Funcionalidades Destacadas

### 📝 Sistema de Admisión Inteligente (CRM)
- **Portal de Pre-matrícula:** Asistente dinámico por pasos para solicitantes con subida asíncrona de documentación (DNI, Expedientes).
- **Conversión Automática:** Proceso de un solo clic para convertir aspirantes en alumnos activos, generando automáticamente sus cuentas.
- **Identidad Institucional:** Generación automática de correos corporativos (`nombre.apellido@aulapro.com`) para los nuevos alumnos.
- **Seguimiento de Estado:** Los aspirantes pueden consultar el estado de su solicitud en tiempo real mediante su DNI.

### 🏆 Gestión de Retos y Proyectos (Premium)
- **Materiales Multi-archivo:** Soporte para adjuntar múltiples documentos (PDF, imágenes) a cada reto.
- **Barra de Progreso AJAX:** Feedback visual en tiempo real durante la subida de archivos pesados.
- **Descarga Inteligente:** Sistema de "Hover Reveal" para archivos individuales y opción de descarga masiva en formato **ZIP**.
- **Edición Fluida:** Borrado de materiales mediante AJAX con animaciones de desvanecimiento sin recarga de página.

### 💬 Comunicación y Control Modular
- **Notificaciones Premium:** Sistema basado en Firebase con interfaz **Glassmorphism**, animaciones suaves y sincronización con el contador de la campana en el menú superior.
- **Chat en Tiempo Real:** Mensajería instantánea optimizada con foco automático y notificaciones sonoras.
- **Panel de Control Modular:** Sistema de **Feature Toggles** que permite activar o desactivar módulos (Chat, Admisiones, Inventario) en tiempo real desde la configuración.
- **Email Transaccional:** Automatización de avisos y credenciales mediante la API de Brevo.

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.2 (Programación Orientada a Objetos y Procedimental)
- **Base de Datos:** MySQL / MariaDB (Esquema optimizado con integridad referencial)
- **Frontend:** HTML5, CSS3 (Custom Dashboard UI), JavaScript (jQuery + AJAX)
- **Librerías principales:**
  - `mpdf/mpdf`: Generación de informes y boletines PDF.
  - `endroid/qr-code`: Códigos QR para verificación de documentos.
  - `SweetAlert2`: Notificaciones y modales elegantes.
  - `GSAP`: Animaciones de interfaz de alto rendimiento.

## 📋 Requisitos e Instalación

### Requisitos del Sistema
- Servidor web (Apache/Nginx) con PHP 8.2+.
- MySQL 5.7 o MariaDB 10.4+.
- Extensión `zip` de PHP habilitada (para descargas masivas).
- [Composer](https://getcomposer.org/) para gestión de dependencias.

### Pasos de Instalación
1. **Clonar e instalar dependencias:**
   ```bash
   git clone https://github.com/tu-usuario/pfc.git
   cd pfc
   composer install
   ```

2. **Base de Datos:**
   - Importa el archivo `database.sql` en tu servidor MySQL. Este archivo contiene la estructura completa y datos iniciales optimizados.

3. **Configuración de APIs:**
   - Renombra `.env.example` a `.env` y configura tus credenciales:
     - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
     - `BREVO_API_KEY` (para emails).
     - `FIREBASE_PROJECT_ID` (para notificaciones push).

4. **Permisos de Carpeta:**
   - Asegúrate de que `public/uploads/` y sus subcarpetas tengan permisos de escritura (775/777).

---
**Autor:** Yassin Lahhut
**Año:** 2026
**Proyecto:** Trabajo de Fin de Grado (TFG) - AulaPro Premium
