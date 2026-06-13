# AulaPro - Sistema de Gestión Académica (TFG)

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg)](https://www.php.net/)

**AulaPro** es una plataforma web integral diseñada para la gestión de centros de formación y escuelas, con un enfoque especial en Ciclos de Formación Profesional (FP). Este proyecto fue desarrollado como Trabajo de Fin de Grado (TFG) para modernizar y centralizar la administración académica, la comunicación y el control de recursos.

## 🚀 Funcionalidades Principales

### 📚 Gestión Académica
- **Matriculación y Usuarios:** Control total sobre el alta de alumnos, profesores y personal administrativo.
- **Ciclos y Módulos:** Estructuración de la oferta formativa por ciclos y sus respectivos módulos.
- **Sistema de Calificaciones Avanzado:** Cálculo automático de la nota final basado en un baremo configurable (por defecto: 75% módulos y 25% retos/proyectos).
- **Generación de Boletines:** Creación de informes de notas en PDF con códigos QR de verificación.

### 💬 Comunicación y Notificaciones
- **Chat Interno:** Sistema de mensajería en tiempo real para la comunidad educativa.
- **Notificaciones Push:** Integración con Firebase para alertas instantáneas.
- **Email Transaccional:** Envío de avisos y reportes a través de la API de Brevo.

### 📦 Gestión de Recursos
- **Inventario:** Control de existencias y sistema de préstamos de material para estudiantes.
- **Gestión de Pagos:** Seguimiento de las cuotas mensuales de los alumnos.
- **Repositorio de TFG:** Espacio para la subida y corrección de proyectos finales.

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.2 (Nativo)
- **Base de Datos:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3 (Fuente Gilroy), JavaScript (jQuery)
- **Librerías principales:**
  - `mpdf/mpdf`: Generación de documentos PDF.
  - `endroid/qr-code`: Creación de códigos QR.
  - `phpoffice/phpspreadsheet`: Exportación de datos a Excel.
- **Servicios Externos:** Firebase (Notificaciones) y Brevo (Emails).

## 📋 Requisitos

- Servidor web (Apache recomendado) con PHP 8.2 o superior.
- MySQL 5.7 o MariaDB 10.4+.
- [Composer](https://getcomposer.org/) instalado.
- (Opcional) Docker Desktop para ejecución en contenedores.

## 🔧 Instalación

### Opción 1: Servidor Local (XAMPP/Laragon)
1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/tu-usuario/pfc.git
   cd pfc
   ```

2. **Instalar dependencias:**
   ```bash
   composer install
   ```

3. **Configuración de la Base de Datos:**
   - Crea una base de datos en tu servidor MySQL.
   - Importa el archivo inicial `database.sql`.
   - Aplica las actualizaciones en la carpeta `migrations/` en orden cronológico.

4. **Variables de Entorno:**
   - Copia el archivo de ejemplo: `cp .env.example .env` (en Windows: `copy .env.example .env`).
   - Edita el archivo `.env` con tus credenciales de base de datos y claves de API.
   - **Nota:** Si tienes problemas con caracteres especiales en el `.env` (común en algunos hostings), puedes usar el plan B creando un archivo `config/db.php` (ver `docs/PROBLEMA_ENV_SOLUCION.txt`).

### Opción 2: Docker (Recomendado)
Para una ejecución rápida sin instalar dependencias locales:
1. Navega a la carpeta de Docker: `cd adocker`
2. Inicia los contenedores: `docker compose up --build`
3. Accede a `http://localhost:8080`
4. Consulta `adocker/howtorun.txt` para más detalles.

## 🧪 Pruebas

El proyecto incluye tests unitarios básicos para validaciones y seguridad.
Para ejecutarlos (asumiendo que PHPUnit está disponible):
```bash
./vendor/bin/phpunit tests
```

---
**Autor:** Yassin Lahhit
**Año:** 2026
**Proyecto:** Trabajo de Fin de Grado (TFG)
