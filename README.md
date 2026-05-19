# AulaPro — Sistema de Gestión Académica

AulaPro es una aplicación web para gestionar el día a día de un centro de formación profesional. Permite llevar el control de estudiantes, profesores, módulos, calificaciones, pagos, eventos y mucho más, todo desde un único panel.

---

## Tecnologías usadas

- **PHP** (backend y lógica de negocio)
- **MySQL** (base de datos)
- **XAMPP** (entorno de desarrollo local)
- **HTML5 / CSS3 / JavaScript / jQuery** (interfaz)
- **FontAwesome** (iconos)
- **Firebase FCM** (notificaciones push)
- **Brevo** (envío de correos electrónicos)

---

## Roles de usuario

El sistema tiene tres tipos de usuario, cada uno con su propio panel:

| Rol | Acceso |
|---|---|
| **Administrador / Director** | Control total: estudiantes, profesores, ciclos, módulos, pagos, inventario, resultados... |
| **Profesor** | Gestión de sus módulos, calificaciones, retos y mensajes con estudiantes |
| **Estudiante** | Consulta de notas, retos, anuncios, eventos, pagos y entrega del PFC |

---

## Funcionalidades principales

### Administrador
- Gestión de estudiantes, profesores y directores
- Creación y edición de ciclos formativos y módulos
- Asignación de profesores a módulos
- Sistema de calificaciones: módulos, retos y TFG
- Resultados finales (ponderación 75% módulos / 25% retos)
- Control de pagos e historial por estudiante
- Inventario y préstamos de material
- Publicación de anuncios y eventos
- Gestión de reclamaciones / mensajes
- Panel de control con estadísticas generales

### Profesor
- Listado y detalles de sus estudiantes
- Calificación de módulos y retos
- Calificación del TFG/PFC
- Creación y edición de retos
- Mensajería interna
- Edición de su perfil

### Estudiante
- Consulta de sus calificaciones (módulos y retos)
- Ver resultados finales
- Listado de retos disponibles
- Anuncios y eventos del centro
- Historial de pagos
- Subida y seguimiento del PFC
- Mensajes / reclamaciones
- Edición de su perfil

---

## Estructura del proyecto

```
pfc/
├── index.html                  ← Página de inicio / landing
├── vistas/
│   ├── login.php               ← Formulario de acceso
│   ├── admin/                  ← Vistas del administrador
│   ├── profesores/             ← Vistas del profesor
│   └── estudiantes/            ← Vistas del estudiante
├── controladores/
│   ├── validacion.php          ← Gestión del login
│   ├── logout.php
│   ├── admin/
│   ├── profesores/
│   └── estudiantes/
├── modelos/                    ← Acceso a la base de datos
│   ├── conectar.php
│   └── *.php (una entidad por archivo)
└── public/
    ├── css/
    ├── js/
    └── imagenes/
```

La arquitectura sigue el patrón **MVC**: los modelos gestionan los datos, los controladores procesan las acciones y las vistas muestran la información al usuario.

---

## Instalación local

1. Instala [XAMPP](https://www.apachefriends.org/) y arranca Apache y MySQL.
2. Clona o copia este proyecto en `C:/xampp/htdocs/pfc`.
3. Importa la base de datos desde phpMyAdmin (archivo `.sql` del proyecto).
4. Abre el navegador y accede a `http://localhost/pfc`.

---

## Acceso al sistema

Desde la página de inicio pulsa **Acceso** para ir al formulario de login. Según el rol asignado en la base de datos, el sistema redirige automáticamente al panel correspondiente.

---

## Autor

Desarrollado por **Yassin Lahhit** como Proyecto Final de Grado (curso 2025/2026).
