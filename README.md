# TFG - Sistema de Gestión Académica
> Proyecto Final de Grado - Plataforma educativa integral desarrollada en PHP Nativo con arquitectura MVC

---

## 📋 Información General
| Característica | Detalle |
|----------------|---------|
| Estado | ✅ En desarrollo (Fase de Pulido) |
| Tecnología | PHP 7.4+, MySQL, HTML5, CSS3, JavaScript |
| Arquitectura | Modelo-Vista-Controlador (MVC) |
| Licencia | MIT |
| Repositorio | https://github.com/yassinlahhit95/tfg.git |

---

## 📖 Descripción
Sistema de gestión académica completo que permite administrar de forma centralizada:
- 👨‍🎓 Estudiantes (Gestión, Perfil, Calificaciones, Pagos)
- 👨‍🏫 Profesores (Gestión de Módulos, Evaluación de Retos, Mensajería)
- 🎓 Directores (Administración global, Gestión de Personal)
- 📚 Ciclos formativos y Módulos
- 🏫 Aulas y Gestión de Espacios
- 📝 Retos Educativos y Calificaciones (Peso 75% Módulos / 25% Retos)
- 💸 Pagos y Control Financiero
- 📢 Anuncios del Centro y Notificaciones Push (FCM)
- 📩 Buzón de Mensajería y Reclamaciones
- 📦 Inventario y Préstamos de Material

---

## 📂 Estructura del Proyecto
```
pfc/
├── 📁 controladores/         # Lógica de negocio (MVC)
│   ├── 📁 admin/             # Controladores para Administradores
│   ├── 📁 estudiantes/       # Controladores para Estudiantes
│   └── 📁 profesores/        # Controladores para Profesores
├── 📁 modelos/               # Acceso a datos y lógica de BD
│   ├── conectar.php          # Conexión principal a la BD
│   └── (entidades).php       # Lógica por entidad (Estudiantes, Profesores, etc.)
├── 📁 vistas/                # Interfaz de usuario (Plantillas PHP)
│   ├── 📁 admin/             # Vistas de Administración
│   ├── 📁 estudiantes/       # Vistas de Estudiantes
│   └── 📁 profesores/        # Vistas de Profesores
├── 📁 public/                # Recursos estáticos
│   ├── 📁 css/               # Hojas de estilo (Diseño Moderno)
│   ├── 📁 js/                # Scripts Frontend
│   └── 📁 uploads/           # Documentos y TFGs subidos
├── 📁 config/                # Configuraciones externas (Firebase, etc.)
└── database.sql              # Esquema de la Base de Datos
```

---

## ⚙️ Requisitos del Sistema
✅ PHP 7.4 o superior  
✅ MySQL / MariaDB 10.3+  
✅ Servidor Web (Apache 2.4+ / Nginx)  
✅ Extensiones PHP: mysqli, gd, curl, mbstring

---

## 🚀 Instalación Paso a Paso

### 1. Descargar código
```bash
git clone https://github.com/yassinlahhit95/tfg.git
cd tfg
```

### 2. Configurar Base de Datos
1. Crear base de datos MySQL: `cuhq4y87y_pfc`
2. Importar esquema: `database.sql`
3. Verificar credenciales en: `modelos/conectar.php`

### 3. Configurar Servidor Web
> **XAMPP/WAMP**: Colocar carpeta `pfc` dentro de `htdocs`  
> **Permisos**: Asegurar permisos de escritura en carpeta `public/uploads/`

---

## 🎨 Estándares de Interfaz (UI)
> ❗ **Consistencia visual obligatoria**

- **Botones de Acción:** Texto en **MAYÚSCULAS** (ej: `NUEVO ESTUDIANTE`).
- **Iconografía:** Uso estandarizado de FontAwesome. Los botones "Añadir/Nuevo" siempre usan `<i class="fas fa-plus"></i>`.
- **Validación:** Los errores de campos duplicados o vacíos se muestran **debajo del input** en color rojo (`error-campo`).

---

## 📏 Estándares de Codificación y Seguridad

### ✅ Protección contra Duplicados
- Se han implementado funciones de comprobación en los modelos (`checkEntityExistente`) para evitar DNIs, Emails o Nombres duplicados.
- Los controladores deben manejar estas excepciones y devolver mensajes específicos al usuario.

### ✅ Seguridad
- ✅ OBLIGATORIO usar `htmlspecialchars()` en TODAS las salidas de datos dinámicos.
- ✅ OBLIGATORIO usar `mysqli_real_escape_string()` o Prepared Statements para consultas SQL.
- ✅ Los formularios deben mantener el estado de los datos (`datos_estudiante`, etc.) tras un error de validación.

---

## 📦 Funcionalidades Implementadas
- [x] Arquitectura MVC Completa.
- [x] CRUDs protegidos contra duplicados.
- [x] Unificación de estilos y botones en el Panel Admin.
- [x] Páginas dedicadas para Añadir Aulas y Artículos.
- [x] Sistema de cálculo de notas finales con ponderación (75/25).
- [x] Notificaciones Push integradas con Firebase.

---

## 📝 Changelog Reciente
- **Mayo 2026**: 
  - Estandarización de botones y textos en todo el Panel Admin.
  - Implementación de protección contra entradas duplicadas (DNI, Email, Nombres).
  - Mejora de la validación de formularios con mensajes debajo de cada campo.
  - Creación de páginas dedicadas para la inserción de Aulas y Artículos de Inventario.

---

## 👤 Autor
Yassin Lahhit  
Proyecto Final de Grado 2025/2026
