# TFG - Sistema de Gestión Académica (AulaPro)
> Proyecto Final de Grado - Plataforma educativa integral desarrollada en PHP Nativo con arquitectura MVC Tri-Portal.

---

## 📋 Información General
| Característica | Detalle |
|----------------|---------|
| Estado | ✅ Completado (Versión 1.0) |
| Tecnología | PHP 7.4+, MySQL, HTML5, CSS3, JavaScript |
| Arquitectura | Modelo-Vista-Controlador (MVC) Tri-Portal |
| Licencia | MIT |
| Autor | Yassin Lahhit |

---

## 📖 Descripción
Sistema de gestión académica avanzado diseñado para centros educativos de Formación Profesional. La plataforma se divide en tres portales independientes pero interconectados para una gestión eficiente:

### 👨‍🎓 Portal Estudiantes
- **Mi Perfil:** Gestión de datos personales.
- **Académico:** Consulta de calificaciones por módulos.
- **Retos:** Seguimiento de metodología basada en retos.
- **PFC/TFG:** Sistema de subida y gestión de Proyectos Finales de Ciclo.
- **Pagos:** Control de mensualidades y estado financiero.
- **Mensajería:** Comunicación directa con profesores.

### 👨‍🏫 Portal Profesores
- **Gestión Académica:** Introducción de notas y evaluación de módulos.
- **Evaluación de Retos:** Calificación de proyectos y competencias.
- **Mensajería:** Buzón de entrada y envío de mensajes a estudiantes.
- **Perfil:** Administración de datos docentes.

### 👑 Portal Administrador (Dashboard Global)
- **Control Total:** Gestión de usuarios (Estudiantes, Profesores, Directores).
- **Estructura:** Configuración de Ciclos, Módulos y Aulas.
- **Inventario:** Préstamos y devoluciones de material tecnológico.
- **Comunicación:** Publicación de anuncios globales y notificaciones Push (Firebase).
- **Finanzas:** Supervisión de pagos de todo el centro.

---

## 📂 Estructura del Proyecto
```
pfc/
├── 📁 controladores/         # Lógica de negocio dividida por portal
│   ├── 📁 admin/             # Operaciones administrativas
│   ├── 📁 estudiantes/       # Acciones del alumno
│   └── 📁 profesores/        # Gestión docente
├── 📁 modelos/               # Capa de datos compartida (PDO/MySQLi)
│   ├── conectar.php          # Conexión principal
│   └── (entidades).php       # Clases de lógica de datos
├── 📁 vistas/                # Interfaz de usuario (Templates PHP)
│   ├── 📁 admin/             # Vistas del Dashboard Admin
│   ├── 📁 estudiantes/       # Panel del Alumno
│   └── 📁 profesores/        # Panel del Profesor
├── 📁 public/                # Recursos públicos y estáticos
│   ├── 📁 css/               # Estilos organizados (admin, responsive, notificaciones)
│   ├── 📁 js/                # Scripts Frontend y Firebase
│   └── 📁 uploads/           # Almacenamiento de documentos y PFCs
├── 📁 config/                # Configuraciones de seguridad y Firebase
└── database.sql              # Esquema completo de la Base de Datos
```

---

## ⚙️ Requisitos e Instalación

### Requisitos
- **Servidor Web:** Apache 2.4+ / Nginx.
- **PHP:** 7.4 o superior (Extensiones: `mysqli`, `curl`, `mbstring`).
- **Base de Datos:** MySQL 5.7+ o MariaDB 10.3+.

### Instalación Paso a Paso
1. **Clonar Repositorio:**
   ```bash
   git clone https://github.com/yassinlahhit95/tfg.git
   ```
2. **Base de Datos:**
   - Crear DB: `cuhq4y87y_pfc`.
   - Importar `database.sql`.
3. **Configuración:**
   - Editar `modelos/conectar.php` con tus credenciales.
   - En `config/`, copiar `secrets.php.example` a `secrets.php` y añadir tu API Key de Brevo (Email).
   - Añadir `service-account.json` para Firebase en `config/`.

---

## 🎨 Estándares de Desarrollo
- **Botones:** Texto en MAYÚSCULAS para acciones principales.
- **Seguridad:** Uso de `mysqli_real_escape_string()` y protección contra entradas duplicadas.
- **UI:** Diseño responsive basado en Flexbox y CSS Grid.
- **Mensajería:** Feedback inmediato mediante toasts y mensajes de sesión (`$_SESSION['exito']`/`error`).

---

## 📦 Changelog Reciente
- **V1.0 Final**:
  - Implementación completa del sistema tri-portal.
  - Refactorización de estilos CSS y optimización responsive.
  - Integración de notificaciones Push con Firebase.
  - Mejora en la validación de formularios y protección de datos.

---

## 👤 Autor
**Yassin Lahhit**  
*Proyecto Final de Grado - Desarrollo de Aplicaciones Web*
