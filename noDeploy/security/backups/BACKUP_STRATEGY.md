# AulaPro — Estrategia de copias de seguridad automáticas

Objetivo: poder restaurar el sistema completo (datos + ficheros) tras un fallo de
disco, un borrado accidental o un **ransomware**, con pérdida máxima de unas horas.

## 1. Qué se respalda
| Activo | De dónde | Cómo |
|--------|----------|------|
| **Base de datos** | MySQL/MariaDB (`yassjjzw_pfc`) | `mysqldump --single-transaction` (consistente, sin parar la app) |
| **Ficheros subidos** | `public/uploads/` (admisiones, aula, documentos) | `tar`/zip |
| **Configuración** | `.env`, `config/db.php` | copia manual cifrada (cambia poco; no en cada backup) |
| **Código** | repositorio Git | ya versionado; no necesita backup propio |

## 2. Regla 3-2-1 (el estándar)
- **3** copias de los datos (la de producción + 2 backups).
- **2** soportes distintos (disco local del servidor + almacenamiento remoto).
- **1** copia **fuera del servidor** y, contra ransomware, **inmutable**.

## 3. Frecuencia y retención
| Tipo | Frecuencia | Retención |
|------|-----------|-----------|
| Completo (BD + uploads) | **diario** (de madrugada) | 14 días local · 30 diarios + 12 mensuales offsite |
| Antes de un despliegue | manual (ejecutar el script) | hasta validar el despliegue |

Para sitios con muchos cambios, añade un dump de BD cada 6 h (la BD es pequeña y barata de volcar).

## 4. Cómo se automatiza

### Producción (Linux) — `backup.sh` + cron
1. Crea credenciales de solo-dump (mínimo privilegio) y un fichero protegido:
   ```sql
   CREATE USER 'aulapro_bk'@'127.0.0.1' IDENTIFIED BY '<larga-aleatoria>';
   GRANT SELECT, SHOW VIEW, LOCK TABLES, EVENT, TRIGGER ON yassjjzw_pfc.* TO 'aulapro_bk'@'127.0.0.1';
   ```
   ```ini
   # /etc/aulapro/db-backup.cnf   (chmod 600, owner root)
   [client]
   user=aulapro_bk
   password=<larga-aleatoria>
   host=127.0.0.1
   ```
2. Programa el cron (3:15 cada día):
   ```cron
   15 3 * * *  /var/www/aulapro/security/backups/backup.sh >> /var/backups/aulapro/cron.log 2>&1
   ```
3. (Recomendado) activa offsite inmutable y cifrado exportando variables antes del cron:
   `RCLONE_REMOTE=s3lock:aulapro-bk` y `BACKUP_GPG_RECIPIENT=backups@tu-dominio`.

### Desarrollo (Windows/Laragon) — `backup.ps1` + Programador de tareas
```
schtasks /create /tn "AulaPro Backup" /tr "powershell -File C:\laragon\www\pfc\security\backups\backup.ps1" /sc daily /st 03:15
```

## 5. Offsite + anti-ransomware (clave)
El ransomware cifra también los backups si puede alcanzarlos. Por eso:
- Sube a un bucket con **versionado + Object Lock / WORM** (S3 Object Lock, Backblaze B2,
  Wasabi). Una vez escrito, **nadie puede borrar ni sobrescribir** hasta que expire la retención.
- La cuenta que sube tiene permiso de **escribir**, no de **borrar** (append-only).
- Mantén los backups **fuera del web root** y en un disco/credenciales distintos a los de la app.
- `rclone copy --immutable` evita reescribir objetos ya subidos.

## 6. Verificación (un backup no probado no existe)
- El script hace `gzip -t` + `sha256sum` en cada copia.
- **Una vez al mes**, restaura en una BD de pruebas y comprueba que la app arranca:
  ```bash
  gunzip < db.sql.gz | mysql -u root -p aulapro_restore_test
  ```
- Apunta la fecha de la última restauración probada.

## 7. Procedimiento de recuperación (runbook)
1. **Aísla** el servidor afectado (córtale red) si se sospecha intrusión/ransomware.
2. Aprovisiona un host limpio y `git clone` del código (el código no se restaura del backup).
3. Restaura la BD desde la última copia íntegra:
   ```bash
   gunzip < /var/backups/aulapro/<TS>/db.sql.gz | mysql --defaults-extra-file=/etc/aulapro/db.cnf yassjjzw_pfc
   ```
4. Restaura los ficheros:
   ```bash
   tar -xzf /var/backups/aulapro/<TS>/uploads.tar.gz -C /var/www/aulapro/public/
   ```
5. **Rota todos los secretos** (BD, BREVO_API_KEY, Firebase, APP_KEY) — ver RUNBOOK §8.
6. Revisa integridad (`SHA256SUMS`) y vuelve a poner en servicio.

## 8. Monitorización
- Alerta si el cron no deja una copia nueva en 26 h (backup fallido silencioso es el peor caso).
- Revisa `backup.log` y el tamaño de las copias (una caída brusca de tamaño = problema).
