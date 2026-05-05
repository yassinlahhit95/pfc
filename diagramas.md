# Diagramas del Proyecto

## Diagrama ER (Entidad-Relación)

```mermaid
erDiagram
    NIVELES {
        int idNivel PK
        string nombreNivel
    }
    AULAS {
        int idAula PK
        string nombreAula
    }
    CICLOS {
        int idCiclo PK
        string nombreCiclo
        string abreviaturaCiclo
        decimal precioCiclo
        int idNivel FK
    }
    MODULOS {
        int idModulo PK
        string nombreModulo
        int horasMaximas
        int idCiclo FK
    }
    PROFESORES {
        int idProfesor PK
        string nombreProfesor
        string emailProfesor
        string telefonoProfesor
        string dniProfesor
    }
    ESTUDIANTES {
        int idEstudiante PK
        string nombreEstudiante
        string emailEstudiante
        int idCiclo FK
        string archivoTFG
    }
    DIRECTORES {
        int idDirector PK
        string nombreDirector
        string emailDirector
    }
    RETOS {
        int idReto PK
        string nombreReto
        date fechaInicio
        date fechaFin
        int horasReto
    }
    MODULO_RETO {
        int idModulo FK
        int idReto FK
    }
    CALIFICACIONES_RETOS {
        int idCalificacion PK
        int idEstudiante FK
        int idReto FK
        decimal nota
    }
    CALIFICACIONES_MODULOS {
        int idCalificacion PK
        int idEstudiante FK
        int idModulo FK
        decimal nota_1ev
        decimal nota_1final
        decimal nota_2ev
        decimal nota_2final
    }
    DISPOSITIVOS {
        int idDispositivo PK
        string nombreDispositivo
        string numeroSerie
        enum estadoDispositivo
    }
    PRESTAMOS {
        int idPrestamo PK
        int idEstudiante FK
        string numeroSerie
        date fechaPrestamo
        enum estadoPrestamo
    }
    ANUNCIOS {
        int idAnuncio PK
        string titulo
        text mensaje
        date fechaExpiracion
        enum dirigidoA
    }
    RECLAMACIONES {
        int idReclamacion PK
        int idEstudiante FK
        int idProfesor FK
        enum emisor_rol
        string asunto
        text descripcion
        enum estadoReclamacion
    }
    PAGOS {
        int idPago PK
        int idEstudiante FK
        decimal monto
        date fechaPago
        date fechaProximoPago
        enum tipoPago
    }
    EVENTOS {
        int idEvento PK
        string tituloEvento
        text descripcionEvento
        date fechaEvento
        time horaEvento
        string ubicacionEvento
    }
    CICLO_PROFESOR {
        int idCiclo FK
        int idProfesor FK
    }
    CICLO_AULA {
        int idCiclo FK
        int idAula FK
    }
    PROFESOR_MODULO {
        int idProfesor FK
        int idModulo FK
    }

    NIVELES ||--o{ CICLOS : "contiene"
    CICLOS ||--o{ MODULOS : "incluye"
    CICLOS ||--o{ ESTUDIANTES : "ofrece"
    CICLOS ||--o{ CICLO_PROFESOR : "asocia"
    CICLOS ||--o{ CICLO_AULA : "usa"
    MODULOS ||--o{ MODULO_RETO : "relaciona"
    MODULOS ||--o{ PROFESOR_MODULO : "asigna"
    RETOS ||--o{ MODULO_RETO : "agrega"
    PROFESORES ||--o{ PROFESOR_MODULO : "imparte"
    PROFESORES ||--o{ CICLO_PROFESOR : "participa"
    ESTUDIANTES ||--o{ CALIFICACIONES_RETOS : "recibe"
    ESTUDIANTES ||--o{ CALIFICACIONES_MODULOS : "recibe"
    ESTUDIANTES ||--o{ PRESTAMOS : "toma"
    ESTUDIANTES ||--o{ PAGOS : "paga"
    ESTUDIANTES ||--o{ RECLAMACIONES : "envía"
    PROFESORES ||--o{ RECLAMACIONES : "recibe"
```

## Diagrama de Secuencia (Profesor califica un reto)

```mermaid
sequenceDiagram
    participant Profesor
    participant Sistema
    participant BaseDatos as "Base de Datos"
    participant Estudiante

    Profesor->>Sistema: Inicia sesión
    Sistema->>BaseDatos: Comprueba email y password
    BaseDatos-->>Sistema: Usuario profesor encontrado
    Sistema-->>Profesor: Acceso concedido

    Profesor->>Sistema: Elige un ciclo, módulo y reto
    Sistema->>BaseDatos: Carga módulos y retos del profesor
    BaseDatos-->>Sistema: Devuelve opciones
    Sistema-->>Profesor: Muestra formulario de selección

    Profesor->>Sistema: Selecciona estudiante y completa nota de reto
    Sistema->>BaseDatos: Inserta/actualiza calificaciones_retos
    BaseDatos-->>Sistema: Confirma operación
    Sistema-->>Profesor: Muestra mensaje de éxito

    Estudiante->>Sistema: Consulta sus notas de retos
    Sistema->>BaseDatos: Busca calificaciones_retos por idEstudiante
    BaseDatos-->>Sistema: Envía notas
    Sistema-->>Estudiante: Muestra calificaciones
```
## Tablas de la base de datos

- `niveles`: grados formativos del centro (Grado Medio/Superior).
- `aulas`: aulas y laboratorios disponibles.
- `ciclos`: ciclos formativos como DAW, DAM y SMR.
- `modulos`: módulos que pertenecen a cada ciclo.
- `profesores`: profesores con sus datos y credenciales.
- `estudiantes`: estudiantes con ciclo asignado y archivo TFG.
- `directores`: directores y administrador del sistema.
- `retos`: retos del curso con fechas y horas.
- `modulo_reto`: relación de qué módulos participan en cada reto.
- `calificaciones_retos`: notas de reto por estudiante.
- `calificaciones_modulos`: notas de módulo por estudiante.
- `dispositivos`: inventario de dispositivos.
- `prestamos`: préstamos de dispositivos a estudiantes.
- `anuncios`: comunicados para todos, estudiantes o profesores.
- `eventos`: eventos del centro con fecha y ubicación.
- `reclamaciones`: reclamaciones de estudiantes o profesores.
- `pagos`: pagos realizados por estudiantes.
- `ciclo_profesor`: asignaciones de profesores a ciclos.
- `ciclo_aula`: asignaciones de aulas a ciclos.
- `profesor_modulo`: módulos que imparte cada profesor.

## Cómo funciona el proyecto

- El sistema usa roles reales: `directores` (incluye admin), `profesores` y `estudiantes`.
- `ciclos` se agrupan por `niveles` y contienen `modulos`.
- Los profesores se asignan a ciclos y módulos mediante `ciclo_profesor` y `profesor_modulo`.
- Los retos se vinculan a módulos con `modulo_reto`.
- Las notas se guardan en `calificaciones_retos` para retos y `calificaciones_modulos` para módulos.
- Los estudiantes pueden pagar con registros en `pagos`, enviar reclamaciones en `reclamaciones`, ver anuncios y eventos.
- El inventario usa `dispositivos` y `prestamos` para controlar equipos prestados.

## Datos del proyecto reales en `database.sql`

- Hay ciclos de ejemplo: `Desarrollo de Aplicaciones Web` (DAW), `Desarrollo de Aplicaciones Multiplataforma` (DAM), `Sistemas Microinformáticos y Redes` (SMR).
- Hay módulos como `Programación`, `Bases de Datos`, `Desarrollo Web en Entorno Cliente` y `Desarrollo Web en Entorno Servidor`.
- Hay dos profesores de prueba y cuatro estudiantes de ejemplo.
- Se crearon dos retos: `PROYECTO E-COMMERCE PHP` y `CONFIGURACIÓN RED CORPORATIVA`.
- También hay anuncios de mantenimiento y entrega de proyectos, y eventos como ciberseguridad y graduación.
## Cómo usar estos diagramas

- Abre `diagramas.md` en VS Code con la extensión Mermaid.
- O copia el contenido a https://mermaid.live/ para ver las imágenes.

## Nota

El entorno no pudo generar las imágenes locales porque `npx` falló por permisos en la caché. El archivo Markdown ya está listo para visualizar como gráficos Mermaid.
