#!/usr/bin/env bash
#
# AulaPro — copia de seguridad automática (Linux / producción)
# Hace: dump de la BD + archivo de /public/uploads, verifica, cifra (opcional),
#       sube a almacenamiento inmutable (opcional) y rota las copias locales.
#
# Uso:   ./backup.sh
# Cron:  15 3 * * *  /var/www/aulapro/security/backups/backup.sh >> /var/backups/aulapro/cron.log 2>&1
#
set -euo pipefail

# ── Configuración (ajusta o exporta como variables de entorno) ───────────────
APP_DIR="${APP_DIR:-/var/www/aulapro}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/aulapro}"          # FUERA del web root
DB_NAME="${DB_NAME:-yassjjzw_pfc}"
DB_CRED_FILE="${DB_CRED_FILE:-/etc/aulapro/db-backup.cnf}" # fichero [client] chmod 600
RETENTION_DAYS="${RETENTION_DAYS:-14}"                     # copias locales a conservar
RCLONE_REMOTE="${RCLONE_REMOTE:-}"                         # ej: s3lock:aulapro-bk  (vacío = sin offsite)
BACKUP_GPG_RECIPIENT="${BACKUP_GPG_RECIPIENT:-}"          # ej: backups@tu-dominio  (vacío = sin cifrado)

TS="$(date +%Y%m%d_%H%M%S)"
DEST="$BACKUP_DIR/$TS"
LOG="$BACKUP_DIR/backup.log"
mkdir -p "$DEST"
log(){ echo "$(date '+%F %T') $*" | tee -a "$LOG"; }

trap 'log "ERROR en la línea $LINENO — backup ABORTADO"; exit 1' ERR

# ── 1) Base de datos (dump consistente, sin bloquear la app) ─────────────────
log "[$TS] Volcando base de datos $DB_NAME…"
mysqldump --defaults-extra-file="$DB_CRED_FILE" \
  --single-transaction --quick --routines --triggers --events \
  --default-character-set=utf8mb4 "$DB_NAME" \
  | gzip -9 > "$DEST/db.sql.gz"

# ── 2) Ficheros subidos por usuarios ─────────────────────────────────────────
log "Archivando uploads…"
tar -czf "$DEST/uploads.tar.gz" -C "$APP_DIR/public" uploads

# ── 3) Verificación de integridad (un backup sin verificar no cuenta) ────────
log "Verificando integridad…"
gzip -t "$DEST/db.sql.gz"
gzip -t "$DEST/uploads.tar.gz"
( cd "$DEST" && sha256sum db.sql.gz uploads.tar.gz > SHA256SUMS )

# ── 4) Cifrado en reposo (recomendado si sale del servidor) ──────────────────
if [ -n "$BACKUP_GPG_RECIPIENT" ]; then
  log "Cifrando con GPG ($BACKUP_GPG_RECIPIENT)…"
  for f in "$DEST"/*.gz; do
    gpg --yes --batch --encrypt -r "$BACKUP_GPG_RECIPIENT" "$f"
    rm -f "$f"
  done
fi

# ── 5) Copia offsite INMUTABLE (defensa anti-ransomware) ─────────────────────
if [ -n "$RCLONE_REMOTE" ]; then
  log "Subiendo a $RCLONE_REMOTE/$TS…"
  rclone copy "$DEST" "$RCLONE_REMOTE/$TS" --immutable
fi

# ── 6) Rotación local (el bucket con versionado retiene las copias remotas) ──
log "Rotando copias locales de más de $RETENTION_DAYS días…"
find "$BACKUP_DIR" -maxdepth 1 -type d -name '20*' -mtime +"$RETENTION_DAYS" -exec rm -rf {} +

log "Backup COMPLETADO: $DEST"
