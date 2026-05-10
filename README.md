# AulaPro — Sistema de Gestión Académica
> Trabajo de Fin de Grado · Desarrollo de Aplicaciones Web · CPS Ibaiondo
> **Autor:** Yassin Lahhit · **Versión:** 1.0 · **Curso:** 2025–2026

---

## Información General

| Campo | Detalle |
|---|---|
| Estado | Completado — v1.0 |
| Arquitectura | MVC Tri-Portal (sin frameworks) |
| Backend | PHP 8+ (procedural) |
| Frontend | CSS puro, JavaScript, jQuery |
| Base de datos | MySQL |
| Integraciones | Firebase Cloud Messaging, Brevo API |
| Servidor local | XAMPP |
| URL demo | [yassin.agency](https://yassin.agency) |

---

## Descripción

AulaPro es una plataforma web de gestión académica diseñada para centros de Formación Profesional. Centraliza en un solo sistema la gestión de estudiantes, profesores y administración, eliminando el uso de hojas de cálculo y herramientas dispersas.

El proyecto está construido desde cero en PHP con arquitectura MVC propia, sin frameworks externos. El frontend usa CSS puro y jQuery. La base de datos es MySQL.

---

## Portales

### Portal Administrador
- Gestión de usuarios: estudiantes, profesores, directores
- Configuración de ciclos formativos, módulos y aulas
- Gestión de pagos y seguimiento financiero
- Inventario del centro con sistema de préstamos
- Publicación de anuncios con notificaciones push (Firebase) y email (Brevo)
- Mensajería interna con bandeja de entrada y alertas de no leídos
- Panel de control con estadísticas globales en tiempo real
- Gestión de Trabajos de Fin de Grado (TFG)
- Calendario de eventos

### Portal Profesores
- Calificación de módulos con lógica de re-evaluación (recuperación)
- Evaluación de retos/proyectos (metodología ABP)
- Resultados finales con pesos configurados (75% módulos / 25% retos)
- Gestión de TFGs asignados
- Mensajería con estudiantes
- Calendario de eventos y anuncios

### Portal Estudiantes
- Consulta de calificaciones por módulo con soporte de recuperaciones
- Seguimiento de retos y notas de proyectos
- Resultados finales del ciclo
- Subida y gestión del TFG propio
- Control de pagos y estado de matrícula
- Mensajería con el equipo docente
- Anuncios y calendario de eventos

---

## Tecnologías Utilizadas

| Capa | Tecnología |
|---|---|
| Backend | PHP 8+ (MVC propio, sin framework) |
| Base de datos | MySQL |
| Frontend | HTML5, CSS3, JavaScript, jQuery |
| Notificaciones | Firebase Cloud Messaging (FCM) v9 |
| Email | Brevo API (SMTP transaccional) |
| Iconos | Font Awesome 6 |
| Servidor | XAMPP (Apache + MySQL) |

---

## Arquitectura MVC

```
pfc/
├── controladores/        # Lógica de negocio y validación por portal
│   ├── admin/
│   ├── profesores/
│   └── estudiantes/
├── modelos/              # Funciones SQL y acceso a datos
├── vistas/               # Plantillas PHP (HTML + datos)
│   ├── admin/
│   ├── profesores/
│   └── estudiantes/
├── public/               # Recursos estáticos
│   ├── css/
│   ├── js/
│   └── imagenes/
├── config/               # Credenciales (excluido de Git)
├── database.sql          # Esquema completo de la base de datos
└── index.html            # Landing page pública
```

---

## Instalación Local

1. Instalar [XAMPP](https://www.apachefriends.org/) y arrancar Apache + MySQL.
2. Clonar o copiar la carpeta del proyecto en `C:/xampp/htdocs/pfc/`.
3. Importar `database.sql` en phpMyAdmin (nombre de la BD: `pfc`).
4. Crear el archivo `modelos/conectar.php` con las credenciales de la BD:
   ```php
   <?php
   $conexion = new mysqli('localhost', 'root', '', 'pfc');
   ```
5. (Opcional) Configurar `config/secrets.php` con la API Key de Brevo para el envío de emails.
6. (Opcional) Configurar `config/service-account.json` con las credenciales de Firebase para notificaciones push.
7. Abrir el navegador en `http://localhost/pfc/`.

---

## Funcionalidades Destacadas

- **Sin frameworks externos** — PHP procedural puro con arquitectura MVC diseñada desde cero.
- **Sistema de calificaciones con recuperación** — lógica de pesos y gestión de convocatorias.
- **Notificaciones duales** — push en tiempo real via Firebase y email automático via Brevo API.
- **Diseño completamente responsive** — adaptado a escritorio, tablet y móvil desde un único CSS sin librerías.
- **Tres portales independientes** — cada rol (admin, profesor, estudiante) tiene su propio entorno con sesión y permisos separados.
- **Gestión de TFGs** — subida, revisión y descarga de proyectos finales integrada en los tres portales.

---

## Autor

**Yassin Lahhit**
Estudiante de 2.º de DAW — CPS Ibaiondo
Bilbao, 2026
