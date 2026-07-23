# Conectar Cloudflare R2 — guía paso a paso

Esta guía explica cómo conectar el proyecto a Cloudflare R2 (almacenamiento de
ficheros, capa gratuita), rellenando las 5 variables `R2_*` de `.env`. El
código ya está preparado (`include/R2Client.php`) — mientras estas variables
estén vacías, la aplicación sigue funcionando exactamente igual que antes
(todo se sirve desde `public/uploads/` en disco local). No hay nada urgente
ni obligatorio: puedes seguir esta guía cuando quieras activar R2.

Enlaces útiles:
- Panel de Cloudflare: https://dash.cloudflare.com/
- Documentación oficial de R2: https://developers.cloudflare.com/r2/

---

## 0. Qué es R2 y por qué

R2 es almacenamiento de objetos compatible con la API de Amazon S3. Se usa
aquí para sacar los ficheros subidos (TFG, recursos de aula, justificantes,
comprobantes, imágenes de blog/landing/ofertaCiclos) del disco del servidor
compartido, sin coste mientras se esté dentro de la capa gratuita:

- 10 GB de almacenamiento
- 1.000.000 operaciones Clase A / mes (subidas, borrados, listados)
- 10.000.000 operaciones Clase B / mes (descargas, lecturas)
- **Salida de datos (egress) siempre gratis, sin límite** — esta es la
  diferencia clave frente a S3, donde descargar ficheros cuesta dinero

Los ficheros que ya existen hoy en `public/uploads/` **no se migran** — se
siguen sirviendo desde ahí para siempre. Solo las subidas nuevas (a partir de
que rellenes `.env`) van a R2.

---

## 1. Crear cuenta y entrar al panel

1. Ve a https://dash.cloudflare.com/ y crea una cuenta gratuita (o inicia
   sesión si ya tienes una).
2. No hace falta tener un dominio en Cloudflare para usar R2 — es un
   producto independiente.

---

## 2. Crear el bucket

1. En el menú lateral izquierdo del panel, entra en **R2 Object Storage**
   (si es la primera vez, Cloudflare pedirá activar R2 para la cuenta —
   solo pide un método de pago para verificar identidad, no cobra nada
   mientras estés dentro de la capa gratuita).
2. Pulsa **Create bucket**.
3. Nombre del bucket: elige algo identificable, p. ej. `aulapro-uploads`.
   Anota este nombre exacto — es el valor de `R2_BUCKET_NAME`.
4. Región: **Automatic** (R2 no permite elegir región manualmente; el
   código ya usa `auto` internamente).
5. Pulsa **Create bucket**.

---

## 3. Obtener el Account ID

1. Con el bucket ya creado, entra en él.
2. En la página del bucket (pestaña **Settings**, o en el panel general de
   R2 → **Overview**) verás **Account ID** — una cadena hexadecimal larga.
3. Cópiala → es el valor de `R2_ACCOUNT_ID`.

(También aparece en la barra lateral derecha de casi cualquier página del
panel de Cloudflare, bajo el nombre de la cuenta.)

---

## 4. Crear el token de API (Access Key ID + Secret Access Key)

**Importante:** crea un token limitado a este bucket, nunca un token global
de la cuenta — así, si el token se filtra alguna vez, el daño queda
contenido a este único bucket.

1. Dentro de R2 → **Manage API Tokens** (o **Overview** → botón
   **Manage API Tokens**, arriba a la derecha).
2. Pulsa **Create API Token**.
3. Nombre del token: algo descriptivo, p. ej. `aulapro-produccion`.
4. Permisos: **Object Read & Write**.
5. **Specify bucket(s)** → selecciona únicamente el bucket creado en el
   paso 2 (NO "Apply to all buckets").
6. TTL / expiración: opcional — puedes dejarlo sin caducidad o poner una
   fecha lejana. Si eliges caducidad, tendrás que repetir este paso antes
   de que expire.
7. Pulsa **Create API Token**.
8. Cloudflare mostrará **una sola vez**:
   - **Access Key ID** → valor de `R2_ACCESS_KEY_ID`
   - **Secret Access Key** → valor de `R2_SECRET_ACCESS_KEY`

   Cópialos ahora mismo a un lugar seguro (gestor de contraseñas). Si
   cierras esta pantalla sin copiarlos, no hay forma de recuperarlos —
   habría que borrar el token y crear uno nuevo.

---

## 5. Rellenar `.env`

Abre `.env` (si no existe, cópialo desde `.env.example` primero) y rellena:

```
R2_ACCOUNT_ID=el_account_id_del_paso_3
R2_ACCESS_KEY_ID=el_access_key_id_del_paso_4
R2_SECRET_ACCESS_KEY=el_secret_access_key_del_paso_4
R2_BUCKET_NAME=el_nombre_del_bucket_del_paso_2
R2_PUBLIC_URL=
```

Deja `R2_PUBLIC_URL` vacío por ahora — se rellena en el paso 6, y solo hace
falta si quieres que las imágenes de blog/landing/ofertaCiclos se sirvan
desde R2 también (los documentos protegidos como TFG o justificantes NO
necesitan esta variable, usan URLs firmadas en su lugar).

---

## 6. (Opcional, recomendado) Activar acceso público para imágenes

Las imágenes de blog, landing y catálogo de ciclos son contenido público de
marketing (sin control de acceso), así que se sirven con una URL pública
permanente en vez de una URL firmada con caducidad. Para activarlo:

1. Entra en el bucket → pestaña **Settings**.
2. Busca **Public Access** (a veces aparece como **R2.dev subdomain**).
3. Actívalo. Cloudflare mostrará una URL del tipo:
   `https://pub-xxxxxxxxxxxxxxxx.r2.dev`
4. Copia esa URL → es el valor de `R2_PUBLIC_URL` en `.env` (sin barra `/`
   al final).

**Nota de producción:** Cloudflare avisa de que el subdominio `.r2.dev`
gratuito está pensado para pruebas, no para tráfico de producción sostenido
(puede aplicar limitación de tasa). Cuando quieras pasar a producción de
verdad, conecta un dominio propio a este bucket:
**Settings → Custom Domains → Connect Domain** (necesita que el dominio, o
un subdominio como `cdn.aulapro.yassin.agency`, esté gestionado por
Cloudflare). Esto es un cambio de configuración, no de código — solo
cambiaría el valor de `R2_PUBLIC_URL`.

---

## 7. Probar en local

1. Guarda `.env` con los 5 valores rellenos.
2. Recarga cualquier página de la aplicación (no hace falta reiniciar
   Apache/PHP — `.env` se lee en cada petición).
3. Sube un fichero de prueba real a través de la propia aplicación — por
   ejemplo, como estudiante, sube un TFG, o como admin, sube una imagen de
   blog.
4. Vuelve al panel de Cloudflare → tu bucket → deberías ver el fichero
   nuevo ahí, dentro de una carpeta con el nombre del tipo de contenido
   (`pfc/`, `blog/`, `aula/archivos/`, etc.).

Si algo falla, revisa `logs/` — `R2Client.php` registra los errores de
conexión ahí vía `Logger::error()`.

---

## 8. Verificar que el acceso protegido funciona

Para documentos protegidos (TFG, justificantes, comprobantes, recursos de
aula):

1. Abre el enlace de "Ver" o "Descargar" del fichero que acabas de subir.
2. Debería redirigirte a una URL larga tipo
   `https://<account-id>.r2.cloudflarestorage.com/...?X-Amz-Signature=...`
   y el fichero debe abrirse/descargarse con normalidad.
3. Copia esa URL completa y ábrela en una pestaña nueva pasados 5-10
   minutos — debería fallar (la URL firmada caduca en 300 segundos por
   defecto). Volver a pulsar "Ver"/"Descargar" desde la aplicación genera
   una URL nueva y válida.

Para imágenes públicas (blog/landing/ofertaCiclos), la URL no caduca nunca
— confirma simplemente que la imagen se ve en la página pública.

---

## 9. Desplegar a producción

1. Sube el `.env` de producción (con sus propios valores `R2_*` — puedes
   reutilizar el mismo bucket que en local, o crear uno aparte para
   producción; ambas opciones son válidas, pero usar buckets separados
   evita mezclar ficheros de pruebas con datos reales).
2. No hace falta ningún cambio de código ni de base de datos — la
   integración ya está desplegada junto con el resto de controladores.
3. Repite las pruebas del paso 7 y 8 directamente contra producción tras
   subir el `.env`.

---

## Solución de problemas

- **"Cloudflare R2 no está configurado" (RuntimeException)** — falta
  rellenar alguna de las 5 variables `R2_*` en `.env`.
- **Las subidas fallan silenciosamente / el fichero no llega a R2** —
  revisa `logs/` para el mensaje de `Logger::error()`; normalmente indica
  credenciales incorrectas o el token no tiene permiso de escritura sobre
  el bucket correcto.
- **Las imágenes de blog/landing no cargan** — falta `R2_PUBLIC_URL`, o el
  bucket no tiene Public Access activado (paso 6).
- **Un enlace de descarga da error de firma ("SignatureDoesNotMatch")** —
  revisa que `R2_ACCESS_KEY_ID`/`R2_SECRET_ACCESS_KEY` se copiaron
  completos y sin espacios en `.env`.
- **Quiero revocar el acceso** — R2 → Manage API Tokens → busca el token →
  **Delete**. Efecto inmediato; después habría que crear uno nuevo y
  actualizar `.env` para volver a subir/servir ficheros nuevos (los ya
  subidos siguen en el bucket, solo se pierde la capacidad de la app de
  firmar nuevas peticiones hasta poner el token nuevo).
