# PFC - Sistema de Gestión Académica
> Proyecto Final de Grado - Plataforma educativa integral desarrollada en PHP Nativo con arquitectura MVC

---

## 📋 Información General
| Característica | Detalle |
|----------------|---------|
| Estado | ✅ En desarrollo |
| Tecnología | PHP 7.4+, MySQL, HTML5, CSS3, JavaScript |
| Arquitectura | Modelo-Vista-Controlador (MVC) |
| Licencia | MIT |
| Repositorio | https://github.com/yassinlahhit95/pfc.git |

---

## 📖 Descripción
Sistema de gestión académica completo que permite administrar de forma centralizada:
- 👨‍🎓 Estudiantes
- 👨‍🏫 Profesores
- 🎓 Directores
- 📚 Ciclos formativos
- 🏫 Aulas y horarios
- 📝 Retos y calificaciones
- 💸 Pagos y facturación
- 📢 Anuncios y notificaciones
- 📩 Reclamaciones y seguimiento

---

## 📂 Estructura del Proyecto
```
pfc/
├── 📁 admin/                 # Panel Super Administración
│   ├── dashboardAdmin.php    # Panel principal
│   ├── 📁 controladores/     # Lógica de negocio (MVC)
│   ├── 📁 modelos/           # Acceso a datos y BD
│   ├── 📁 vistas/            # Plantillas interfaz
│   ├── 📁 estiloAdmin/       # Estilos CSS
│   ├── 📁 uploads/           # Archivos subidos
│   └── 📁 imagenesSuperAdmin/# Recursos gráficos
│
├── 📁 directores/            # Panel de Directores
├── 📁 profesores/            # Panel de Profesores
├── 📁 estudiantes/           # Panel de Alumnos
├── 📁 landing/               # Página pública de acceso
├── 📁 features/              # Documentación funcional
└── database.sql              # Esquema base de datos
```

---

## ⚙️ Requisitos del Sistema
✅ PHP 7.4 o superior  
✅ MySQL / MariaDB 10.3+  
✅ Servidor Web (Apache 2.4+ / Nginx)  
✅ Extensiones PHP: mysqli, gd, curl, mbstring  
✅ Mod_rewrite habilitado (Apache)

---

## 🚀 Instalación Paso a Paso

### 1. Descargar código
```bash
git clone https://github.com/yassinlahhit95/pfc.git
cd pfc
```

### 2. Configurar Base de Datos
1. Crear base de datos MySQL vacía
2. Importar esquema: `database.sql`
3. Editar credenciales en: `admin/modelos/conexion.php`

### 3. Configurar Servidor Web
> **XAMPP/WAMP**: Colocar carpeta `pfc` dentro de `htdocs`  
> **Apache**: DocumentRoot apuntando a la carpeta raíz del proyecto  
> **Permisos**: Asegurar permisos de escritura en carpeta `admin/uploads/`

### 4. Verificar instalación
Acceder en navegador: `http://localhost/pfc/landing/`

---

## 🔑 Accesos por Perfil

| Perfil | Ruta de Acceso | Funcionalidades |
|--------|----------------|-----------------|
| Super Admin | `/admin/dashboardAdmin.php` | Gestión completa sistema |
| Director | `/directores/` | Gestión centro educativo |
| Profesor | `/profesores/` | Gestión alumnos, notas, retos |
| Estudiante | `/estudiantes/` | Consultar información personal |

---

## 🧩 Arquitectura MVC

### 📌 Modelos (`admin/modelos/`)
- Responsables exclusivos de la interacción con la base de datos
- Todas las consultas SQL se definen aquí
- No contienen lógica de negocio ni presentación

### ⚙️ Controladores (`admin/controladores/`)
- Reciben peticiones HTTP
- Validan datos de entrada
- Coordinan modelos y vistas
- Contienen la lógica de negocio de la aplicación

### 🎨 Vistas (`admin/vistas/`)
- Únicamente presentan información recibida
- No contienen lógica de negocio ni consultas BD
- No Utilizan `htmlspecialchars()` para sanitizar salidas

---

## 📏 Estándares de Codificación Obligatorios
> ❗ **Estas reglas deben cumplirse SIEMPRE en TODO el proyecto**

### ✅ Validación Números Enteros
```php
// MÉTODO ESTÁNDAR - NO USAR filter_var()
if (!is_numeric($valor) || !ctype_digit($valor) || !preg_match('/^[0-9]+$/', $valor)) {
    // Devolver error de validación
}
```

### ✅ Validación Números Decimales
```php
if (!preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $valor)) {
    // Error para importes monetarios
}
```

### ✅ Seguridad
- ❌ PROHIBIDO usar `htmlentities()`
- ✅ OBLIGATORIO usar `htmlspecialchars()` en TODAS las salidas HTML
- ✅ Formularios: usar `type="text"` para campos numéricos (validación en controlador)
- ✅ Todas las consultas con mysqli prepared statements

---

## 📦 Funcionalidades Implementadas
- [x] CRUD completo Usuarios / Roles
- [x] Gestión de Ciclos y Aulas
- [x] Sistema de Retos educativos
- [x] Calificaciones y evaluaciones
- [x] Gestión de Pagos
- [x] Sistema de Reclamaciones
- [x] Anuncios y notificaciones
- [x] Paneles diferenciados por perfil

---

## 🛠️ Mantenimiento
### Comandos útiles
```bash
# Actualizar repositorio
git pull origin main

# Crear backup base de datos
mysqldump -u usuario -p nombre_bd > backup_$(date +%Y%m%d).sql
```

---

## 📝 Changelog
> Actualizar esta sección con cada modificación importante
- v1.0 - Versión inicial del sistema
- Estructura MVC implementada
- Módulos básicos completados

---

## 👤 Autor
Yassin Lahhit  
Proyecto Final de Grado 2025/2026

---

> ✅ Archivo actualizado y listo para reutilizar en futuras fases del proyecto