# Diagrama Entidad-Relación — AulaPro (TFG)

Base de datos relacional MySQL con 20 tablas organizadas en 5 bloques funcionales.

---

## 1. Bloques de la Base de Datos

### Bloque 1 — Estructura Educativa
- **NIVELES**: Grado Medio o Grado Superior. Agrupa ciclos.
- **CICLOS**: Programa formativo completo (DAW, SMR, DAM). Pertenece a un nivel, tiene precio.
- **MODULOS**: Asignaturas individuales de un ciclo, con horas máximas.
- **AULAS**: Espacios físicos del centro.

### Bloque 2 — Usuarios
- **DIRECTORES**: Control total del sistema. Tienen DNI, email, contraseña y token FCM para notificaciones push.
- **PROFESORES**: Imparten módulos y evalúan retos. Tienen DNI, teléfono, dirección y token FCM.
- **ESTUDIANTES**: Matriculados en un ciclo. Tienen DNI, token FCM, y dos campos para el TFG (`archivoTFG`, `fechaSubidaTFG`).

### Bloque 3 — Académico y Evaluación
- **RETOS**: Proyectos transversales con fechas de inicio/fin y horas estimadas.
- **MODULO_RETO** *(pivot)*: Un reto puede abarcar varios módulos y viceversa.
- **CALIFICACIONES_MODULOS**: Nota de un estudiante en un módulo. Guarda 4 valores: 1ª Evaluación, 1ª Final, 2ª Evaluación, 2ª Final, más observaciones del profesor.
- **CALIFICACIONES_RETOS**: Nota global (0–10) de un estudiante en un reto.

### Bloque 4 — Asignaciones (Tablas Pivot)
- **CICLO_PROFESOR**: Relaciona profesores con los ciclos en que son tutores.
- **PROFESOR_MODULO**: Relaciona exactamente qué módulos imparte cada profesor.
- **CICLO_AULA**: Asigna aulas a ciclos.

### Bloque 5 — Administración y Comunicación
- **PAGOS**: Cuotas abonadas por estudiantes. Incluye tipo (mensual, trimestral…), comprobante y próxima fecha de cobro.
- **DISPOSITIVOS**: Inventario de hardware del centro (número de serie, estado: disponible/prestado).
- **PRESTAMOS**: Relaciona un estudiante con un dispositivo. Registra fechas de entrega y devolución, y estado (en curso/devuelto).
- **ANUNCIOS**: Avisos con fecha de expiración, dirigidos a todos, estudiantes o profesores.
- **EVENTOS**: Calendario escolar con hora y ubicación.
- **RECLAMACIONES**: Sistema de mensajería interna. Puede ser enviado por estudiante, profesor o admin. Tiene estado (pendiente/atendido), campo de respuesta y flag de leído.

---

## 2. Lógica de Negocio Clave

- **Nota final de módulo**: No se almacena directamente. Se calcula en tiempo de ejecución: 75% del promedio de `CALIFICACIONES_MODULOS` (convocatorias) + 25% del promedio de `CALIFICACIONES_RETOS` asociados via `MODULO_RETO`.
- **TFG**: No tiene tabla propia. Los campos `archivoTFG` (ruta del PDF) y `fechaSubidaTFG` están directamente en la tabla `ESTUDIANTES`.
- **Notificaciones push**: Los tres tipos de usuario (director, profesor, estudiante) tienen un campo `fcm_token` para recibir notificaciones vía Firebase Cloud Messaging.
- **Préstamos**: La tabla `PRESTAMOS` referencia a `DISPOSITIVOS` por `numeroSerie` (clave de negocio), no por `idDispositivo`.

---

## 3. Diagrama Mermaid (Entidad-Relación)

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

    AULAS {
        int idAula PK
        string nombreAula
    }

    MODULOS {
        int idModulo PK
        string nombreModulo
        int horasMaximas
        int idCiclo FK
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

    DIRECTORES {
        int idDirector PK
        string nombreDirector
        string emailDirector
        string dniDirector
        string fcm_token
    }

    PROFESORES {
        int idProfesor PK
        string nombreProfesor
        string emailProfesor
        string dniProfesor
        string telefonoProfesor
        string fcm_token
    }

    ESTUDIANTES {
        int idEstudiante PK
        string nombreEstudiante
        string emailEstudiante
        string dniEstudiante
        int idCiclo FK
        string archivoTFG
        datetime fechaSubidaTFG
        string fcm_token
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

    CALIFICACIONES_MODULOS {
        int idCalificacion PK
        int idEstudiante FK
        int idModulo FK
        decimal nota_1ev
        decimal nota_1final
        decimal nota_2ev
        decimal nota_2final
        text observaciones
    }

    CALIFICACIONES_RETOS {
        int idCalificacion PK
        int idEstudiante FK
        int idReto FK
        decimal nota
    }

    PAGOS {
        int idPago PK
        int idEstudiante FK
        decimal monto
        date fechaPago
        date fechaProximoPago
        enum tipoPago
        string comprobante
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
        date fechaDevolucion
        enum estadoPrestamo
    }

    ANUNCIOS {
        int idAnuncio PK
        string titulo
        text mensaje
        date fechaExpiracion
        enum dirigidoA
    }

    EVENTOS {
        int idEvento PK
        string tituloEvento
        text descripcionEvento
        date fechaEvento
        time horaEvento
        string ubicacionEvento
    }

    RECLAMACIONES {
        int idReclamacion PK
        int idEstudiante FK
        int idProfesor FK
        enum emisor_rol
        string asunto
        text descripcion
        date fecha
        enum estadoReclamacion
        boolean leido
        text respuesta
    }

    NIVELES        ||--o{ CICLOS            : "agrupa"
    CICLOS         ||--o{ MODULOS           : "contiene"
    CICLOS         ||--o{ ESTUDIANTES       : "matricula a"
    CICLOS         ||--o{ CICLO_PROFESOR    : "asigna"
    CICLOS         ||--o{ CICLO_AULA        : "usa"
    AULAS          ||--o{ CICLO_AULA        : "asignada a"
    PROFESORES     ||--o{ CICLO_PROFESOR    : "pertenece a"
    PROFESORES     ||--o{ PROFESOR_MODULO   : "imparte"
    MODULOS        ||--o{ PROFESOR_MODULO   : "impartido por"
    MODULOS        ||--o{ MODULO_RETO       : "participa en"
    RETOS          ||--o{ MODULO_RETO       : "abarca"
    ESTUDIANTES    ||--o{ CALIFICACIONES_MODULOS : "recibe nota en"
    MODULOS        ||--o{ CALIFICACIONES_MODULOS : "evaluado en"
    ESTUDIANTES    ||--o{ CALIFICACIONES_RETOS   : "recibe nota en"
    RETOS          ||--o{ CALIFICACIONES_RETOS   : "evaluado en"
    ESTUDIANTES    ||--o{ PAGOS             : "realiza"
    ESTUDIANTES    ||--o{ PRESTAMOS         : "solicita"
    DISPOSITIVOS   ||--o{ PRESTAMOS         : "prestado en"
    ESTUDIANTES    ||--o{ RECLAMACIONES     : "envía o recibe"
    PROFESORES     ||--o{ RECLAMACIONES     : "envía o recibe"
```
