# AulaPro — Sistema de Gestión Académica
> Trabajo de Fin de Grado (TFG) · Desarrollo de Aplicaciones Web · CPS Ibaiondo
> **Versión:** 1.0 · **Curso:** 2025–2026

---

## 📝 Descripción del Proyecto

**AulaPro** es una solución integral diseñada para la gestión académica y administrativa de centros de Formación Profesional. Este sistema permite centralizar el control de estudiantes, personal docente y administración en una única plataforma, eliminando la dependencia de hojas de cálculo externas y mejorando la comunicación interna del centro.

El proyecto destaca por su arquitectura **MVC (Modelo-Vista-Controlador)** desarrollada de forma artesanal en PHP, sin el uso de frameworks pesados, priorizando la velocidad, la simplicidad y la legibilidad del código.

---

## 🚀 Tecnologías y Herramientas

| Capa | Tecnología |
|---|---|
| **Backend** | PHP 8+ (Arquitectura MVC procedural) |
| **Base de Datos** | MySQL |
| **Frontend** | HTML5, CSS3 (Diseño Responsive propio), JavaScript, jQuery |
| **Notificaciones** | Firebase Cloud Messaging (FCM) |
| **Correo** | Brevo API (Envío de notas y alertas por SMTP) |
| **Entorno** | XAMPP / Servidor Apache |

---

## 🛠️ Implementación Técnica y Buenas Prácticas

En el desarrollo de AulaPro se han aplicado soluciones técnicas pensadas para un entorno real de centro educativo:

- **Filtrado Dinámico "Sencillito"**: Implementación de una función en JavaScript que permite filtrar tablas (alumnos, módulos, etc.) en tiempo real. Esto mejora la experiencia del usuario al encontrar datos sin tener que recargar la página constantemente.
- **Seguridad en el Lado del Servidor**: Se ha prescindido de las validaciones simples de HTML (como `required`) para mover toda la lógica de control a los controladores PHP. Usando `isset()` y `empty()`, el sistema es más robusto frente a intentos de salto de validación.
- **Código Humanizado**: El código está documentado con un estilo cercano y pedagógico ("de estudiante a sí mismo"), explicando el porqué de cada función y lógica de negocio, lo que facilita su mantenimiento futuro.
- **Semántica y Accesibilidad**: Estructura basada en HTML5 semántico (uso de `<section>`, `<nav>`, `<aside>`, etc.) para una mejor organización y compatibilidad.
- **Interfaz Multi-Portal**: Tres entornos totalmente independientes para **Administradores**, **Profesores** y **Estudiantes**, cada uno con su propio flujo de trabajo y permisos de seguridad.
- **Generación de Reportes PDF**: Uso de la librería FPDF para la creación de boletines de notas, certificados académicos y sobres de envío de forma automatizada.
- **Comunicación Masiva**: Herramienta para el envío masivo de notas y avisos por email mediante la integración con la API de Brevo.

---

## 📦 Instalación y Configuración

Para poner en marcha el proyecto en un entorno local (como XAMPP), sigue estos pasos:

### 1. Preparación del Entorno
*   Clona o descarga este repositorio en la carpeta `htdocs` de tu servidor local.
*   Asegúrate de tener activados los módulos de **Apache** y **MySQL**.

### 2. Base de Datos
*   Crea una nueva base de datos en `phpMyAdmin` (nombre recomendado: `pfc`).
*   Importa el archivo `database.sql` que se encuentra en la raíz del proyecto para generar todas las tablas y datos de prueba iniciales.

### 3. Conexión al Sistema
*   Edita el archivo `modelos/conectar.php` con tus credenciales locales (normalmente `root` y sin contraseña en XAMPP).
*   Asegúrate de que la base de datos configurada coincida con la creada en el paso anterior.

### 4. Configuración de "Secretos" (Opcional)
Para habilitar las funciones de correo y notificaciones push, deberás añadir tus propias llaves en la carpeta `config/`:
*   **Brevo**: Configura tu API Key en `config/secrets.php` para el envío de emails.
*   **Firebase**: Sube tu archivo `service-account.json` para habilitar las notificaciones en tiempo real.

---

## 🌟 Funcionalidades Destacadas

- **Gestión Académica Completa**: Calificaciones de módulos, retos (metodología ABP) y cálculo automático de resultados finales (75% módulos + 25% retos).
- **Sistema de Recuperaciones**: Lógica integrada para gestionar notas de evaluaciones y finales en varias convocatorias.
- **Generación de Documentación Oficial**: Creación de boletines y certificados en PDF listos para imprimir.
- **Control de Inventario**: Registro de dispositivos y gestión de préstamos a estudiantes.
- **Comunicación en Tiempo Real**: Tablón de anuncios con notificaciones push y avisos por email.
- **Gestión de TFG**: Espacio dedicado para la subida, revisión y calificación de Trabajos de Fin de Grado.
- **Diseño Móvil Primero**: Interfaz adaptada a cualquier dispositivo sin usar librerías externas de CSS.

---

## 👨‍💻 Autor

Proyecto desarrollado como Trabajo de Fin de Grado por **Yassin Lahhit**.
*Estudiante de Desarrollo de Aplicaciones Web (DAW) — CPS Ibaiondo.*
Bilbao, 2026.
