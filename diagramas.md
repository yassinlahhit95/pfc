# Diagramas del Proyecto

## Diagrama ER (Entidad-Relación)

```mermaid
erDiagram
    NIVELES {
        int idNivel PK
        string nombreNivel
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
        string fcm_token
    }
    ESTUDIANTES {
        int idEstudiante PK
        string nombreEstudiante
        string emailEstudiante
        int idCiclo FK
        string archivoTFG
        string fcm_token
    }
    DIRECTORES {
        int idDirector PK
        string nombreDirector
        string emailDirector
        string fcm_token
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
    CALIFICACIONES_TFG {
        int idCalificacion PK
        int idEstudiante FK
        decimal nota
        text observaciones
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
    PROFESOR_MODULO {
        int idProfesor FK
        int idModulo FK
    }

    NIVELES ||--o{ CICLOS : "contiene"
    CICLOS ||--o{ MODULOS : "incluye"
    CICLOS ||--o{ ESTUDIANTES : "ofrece"
    CICLOS ||--o{ CICLO_PROFESOR : "asocia"
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
    ESTUDIANTES ||--o{ CALIFICACIONES_TFG : "recibe nota"
```

## Diagrama de Secuencia (Flujo de Calificación de Reto)

```mermaid
sequenceDiagram
    participant P as Profesor
    participant S as Sistema (Portal Profesor)
    participant M as Modelo (Retos/Calificaciones)
    participant B as Base de Datos
    participant E as Estudiante

    P->>S: Selecciona Ciclo y Módulo
    S->>M: obtenerRetosPorModulo($idModulo)
    M->>B: SELECT * FROM retos...
    B-->>M: Lista de retos
    M-->>S: Retos disponibles
    S-->>P: Muestra lista de retos

    P->>S: Selecciona Reto y Estudiante
    P->>S: Introduce nota (0-10)
    S->>M: guardarCalificacionReto($idEstudiante, $idReto, $nota)
    M->>B: INSERT/UPDATE calificaciones_retos...
    B-->>M: Éxito
    M-->>S: Confirmación
    S-->>P: Mensaje "Nota guardada correctamente"

    Note over S,E: El estudiante recibe notificación
    E->>S: Accede a su portal
    S->>M: obtenerCalificacionesRetos($idEstudiante)
    M->>B: SELECT * FROM calificaciones_retos...
    B-->>M: Notas
    M-->>S: Calificaciones
    S-->>E: Muestra sus notas actualizadas
```
## Tablas de la base de datos

- `niveles`: grados formativos del centro (Grado Medio/Superior).
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
