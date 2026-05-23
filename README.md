# AulaPro - Sistema de Gestión Académica

Proyecto Final de Grado desarrollado para la gestión integral de centros de formación.

## Descripción
AulaPro permite el control de alumnos, profesores, módulos y ciclos formativos. Incluye un sistema de calificaciones adaptado a la formación profesional (por retos y módulos), gestión económica de cuotas y un panel de administración para el control total del centro.

## Características
* Acceso por roles: Administrador, Profesor y Estudiante.
* Control de asistencia y notas (1ª Ev, 2ª Ev, Finales).
* Cálculo automático de notas finales (75% Módulos - 25% Retos).
* Mensajería interna y tablón de anuncios.
* Gestión de inventario y préstamos de material.
* Subida y evaluación de TFG.
* Notificaciones push (Firebase) y envío de correos (Brevo).

## Tecnologías utilizadas
* PHP (mysqli)
* MySQL
* HTML5 / CSS3 (Fuente: Gilroy)
* JavaScript puro y jQuery
* API de Firebase para notificaciones
* API de Brevo para emails

## Instalación
1. Clonar el repositorio.
2. Importar el archivo `database.sql` en PHPMyAdmin.
3. Configurar la conexión en `modelos/conectar.php`.
4. Configurar las API Keys en `config/secrets.php` (este archivo no se incluye por seguridad).

## Autor
Desarrollado por Yassin Lahhit como TFG (2025/2026).
