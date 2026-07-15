# landing-system — Guía para Developers de Front-End

> Esta carpeta es **tu área de trabajo**. Aquí vive todo lo que necesitas para crear y modificar plantillas y secciones de la landing pública del centro.
> **No necesitas tocar ningún otro archivo del proyecto.**

---

## Estructura de carpetas

```
landing-system/
├── plantillas/        ← ⭐ CREA Y EDITA PLANTILLAS AQUÍ
├── secciones/         ← ⭐ CREA Y EDITA SECCIONES AQUÍ
├── temas/             ← CSS de cada plantilla (base.css + tema-*.css)
├── assets/
│   ├── js/            ← landing.js (scripts de la landing pública: contadores, lightbox, etc.)
│   └── imagenes/      ← Thumbnails de plantillas (.svg recomendado)
├── views/
│   └── public/        ← Estructura HTML de la landing (_head, _nav, _footer)
└── engine/            ← 🔒 Motor interno. NO MODIFICAR.
    ├── secciones.php  ← Catálogo de tipos de sección y sus campos
    ├── plantillas.php ← Registro de plantillas disponibles
    └── modelo.php     ← Funciones de base de datos
```

> ⚠️ El **panel de administración** (constructor, arrastrar-soltar, editor de secciones, selector de plantillas) vive **fuera** de esta carpeta por completo: HTML en `vistas/admin/landing/builder.php` y `vistas/admin/landing/plantillas.php`, JS en `public/js/features/landing-builder.js`, CSS en `public/css/features/landing-builder.css`. `landing-system/` solo contiene la landing **pública** — si vas a tocar el constructor, edita en `vistas/admin/landing/`, no aquí.

---

## Cómo crear una nueva PLANTILLA

Una plantilla define qué secciones aparecen por defecto y con qué contenido inicial.

### Paso 1 — Crea el archivo PHP

Copia `plantillas/_nueva_plantilla.example.php` con el nombre de tu plantilla:

```
plantillas/moderna.php
```

Rellena los campos del array:
```php
return [
    'slug'        => 'moderna',
    'nombre'      => 'Moderna',
    'descripcion' => 'Diseño dinámico y actual.',
    'thumbnail'   => 'plantilla-moderna.svg',    // en assets/imagenes/
    'colorAcento' => '#6d28d9',
    'secciones'   => [
        ['tipo' => 'hero', 'contenido' => [
            'titulo' => 'Tu futuro empieza aquí',
        ]],
        ['tipo' => 'cifras'],
        ['tipo' => 'contacto'],
    ],
];
```

> ✅ **No necesitas registrar el slug en ningún otro archivo.**
> El sistema detecta automáticamente todos los `.php` de esta carpeta que no empiecen por `_`.

### Paso 2 — Añade el thumbnail

Crea un `.svg` representativo y ponlo en `assets/imagenes/plantilla-moderna.svg`.

¡Listo! La plantilla aparecerá automáticamente en el panel de administración.

---

## Cómo crear una nueva SECCIÓN

Una sección es un bloque renderizable de la landing (hero, galería, FAQs, etc.).

### Paso 1 — Define los campos en el motor

Abre `engine/secciones.php` y añade tu tipo dentro de `landing_tipos()`:

```php
'galeria' => [
    'nombre' => 'Galería de imágenes',
    'icono'  => 'fa-images',
    'menu'   => null,
    'campos' => [
        'titulo' => ['tipo' => 'text', 'etiqueta' => 'Título', 'max' => 100],
        'items'  => ['tipo' => 'lista', 'etiqueta' => 'Imágenes', 'max' => 12,
                     'subcampos' => [
                         'imagen' => ['tipo' => 'imagen', 'etiqueta' => 'Imagen'],
                         'alt'    => ['tipo' => 'text',   'etiqueta' => 'Texto alternativo', 'max' => 100],
                     ]],
    ],
    'defecto' => [
        'titulo' => 'Nuestra galería',
        'items'  => [],
    ],
],
```

### Paso 2 — Crea el archivo de renderizado

Crea `secciones/galeria.php` (copia `secciones/_nueva_seccion.example.php` como base).

La variable `$contenido` ya viene disponible con los datos de la base de datos.

---

## Cómo editar el CSS de un tema

Cada plantilla tiene su propio CSS en `temas/`:

| Archivo | Uso |
|---|---|
| `temas/base.css` | Estilos compartidos por **todas** las plantillas |
| `temas/tema-institucional.css` | Sobreescrituras específicas de "Institucional" |
| `temas/tema-clasico.css` | Sobreescrituras específicas de "Clásico académico" |
| `temas/tema-vocacional.css` | Sobreescrituras específicas de "Vocacional" |
| `temas/tema-universidad.css` | Sobreescrituras específicas de "Universidad" |

Para un nuevo tema `moderna`, crea `temas/tema-moderna.css`.

---

## Variables CSS disponibles (en todos los temas)

```css
:root {
    --lp-acento:      /* Color de acento personalizado por el admin */
    --lp-primario:    /* Color principal del tema */
    --lp-texto:       /* Color de texto principal */
    --lp-fondo:       /* Color de fondo */
    --lp-superficie:  /* Fondo de tarjetas/paneles */
}
```

---

## Tipos de campo disponibles para `engine/secciones.php`

| Tipo | Descripción |
|---|---|
| `text` | Campo de texto corto. Añade `'max' => N` para limitar. |
| `textarea` | Texto largo / párrafo. |
| `select` | Desplegable. Añade `'opciones' => ['valor' => 'Etiqueta']`. |
| `imagen` | Selector de imagen. Guarda el nombre de archivo en `uploads/landing/`. |
| `color` | Selector de color (hex). |
| `lista` | Lista de items repetibles con `'subcampos'` y `'max'`. |

---

## ⚠️ Reglas importantes

1. **No toques la carpeta `engine/`** salvo para añadir campos de tipo o registrar slugs.
2. Los nombres de archivo de plantillas/secciones deben ser en **minúsculas sin espacios** (ej: `mi-plantilla.php`).
3. Todo el HTML de las secciones debe escapar contenido con `Security::escapeHtml()`.
4. Los `thumbnail` de plantillas se sirven desde `assets/imagenes/`. Usa `.svg` para mejor rendimiento.
5. **No commites** el archivo `.env` ni la carpeta `config/` (pertenecen al backend).

---

## Flujo de trabajo en git

```
# Clonar solo landing-system/ (sparse checkout)
git clone --no-checkout https://github.com/tu-org/pfc.git
cd pfc
git sparse-checkout init --cone
git sparse-checkout set landing-system index.php
git checkout main
```
