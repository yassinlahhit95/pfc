# TFG - Sistema de Gestión Académica (AulaPro)
> Proyecto Final de Grado - Plataforma educativa integral desarrollada en PHP Nativo con arquitectura MVC Tri-Portal.

---

## 📋 Información General
| Característica | Detalle |
|----------------|---------|
| Estado | ✅ Completado (Versión 1.1) |
| Tecnología | PHP 7.4+, MySQL, HTML5, CSS3, JavaScript |
| Arquitectura | Modelo-Vista-Controlador (MVC) Tri-Portal |
| Licencia | MIT |
| Autor | Yassin Lahhit |
| URL Live | [yassin.agency](https://yassin.agency) |

---

## 📖 Características Principales

### 👨‍🎓 Portal Estudiantes
- **Mi Perfil:** Gestión de datos personales con avatares.
- **Académico:** Consulta de calificaciones por módulos (Recuperaciones incluidas).
- **Retos:** Seguimiento de metodología basada en retos (ABP).
- **PFC/TFG:** Sistema de subida y gestión de Proyectos Finales de Ciclo.
- **Pagos:** Control de mensualidades y estado financiero en tiempo real.
- **Mensajería:** Comunicación directa con el equipo docente.

### 👨‍🏫 Portal Profesores
- **Gestión Académica:** Introducción de notas y evaluación de módulos con lógica de re-evaluación.
- **Evaluación de Retos:** Calificación de competencias y proyectos transversales.
- **Mensajería:** Buzón de entrada y notificaciones a estudiantes.

### 👑 Portal Administrador
- **Control Global:** Gestión de usuarios (Estudiantes, Profesores, Directores).
- **Estructura:** Configuración dinámica de Ciclos, Módulos y Aulas.
- **Inventario:** Sistema de préstamos de hardware con trazabilidad.
- **Anuncios:** Publicación de avisos con **Notificaciones Push (Firebase)** y **Email Automático (Brevo)**.
- **Finanzas:** Supervisión integral de recaudación y cobros pendientes.

---

## 🚀 Innovaciones y Mejoras Recientes (v1.1)
- **Login Dinámico:** Nueva interfaz de acceso con integración de video de introducción (`intro.mp4`) y diseño de alta conversión.
- **Lógica Académica Avanzada**: Implementación de sistema de pesos (75% Módulos / 25% Retos) con soporte automático para exámenes de recuperación.
- **Sistema de Notificaciones Dual**: Integración de **Firebase Cloud Messaging** para avisos en escritorio y **Brevo API** para notificaciones por email.
- **Optimización Responsive 100%**: Refactorización completa de CSS para garantizar que todas las vistas de detalles y tablas funcionen perfectamente en smartphones y tablets (iOS/Android).
- **Seguridad y Normalización**: Validación de datos con Regex, normalización de correos electrónicos (trim/lowercase) y protección de archivos sensibles.

---

## 📂 Estructura del Proyecto
```
pfc/
├── 📁 controladores/         # Lógica de negocio dividida por portal
├── 📁 modelos/               # Capa de datos compartida
├── 📁 vistas/                # Interfaz de usuario (Templates PHP)
├── 📁 public/                # Recursos estáticos (CSS, JS, Imágenes, Videos)
├── 📁 config/                # Configuraciones de seguridad (Ignorado en Git)
└── database.sql              # Esquema optimizado para servidores de hosting
```

---

## ⚙️ Instalación

1. **Clonar Repositorio.**
2. **Importar DB:** Usar `database.sql` en un servidor MySQL.
3. **Configurar Credenciales:**
   - `modelos/conectar.php`: Datos de acceso a la BD.
   - `config/secrets.php`: API Key de Brevo.
   - `config/service-account.json`: Credenciales de Firebase.

---

## 👤 Autor
**Yassin Lahhit**  
*Proyecto Final de Grado - Desarrollo de Aplicaciones Web*
