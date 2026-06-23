# AulaPro — copia de seguridad (Windows / Laragon, entorno de desarrollo)
# Dump de la BD + ZIP de uploads, con rotación. Programar con el Programador de tareas.
#
# Ejemplo (tarea diaria 03:15):
#   schtasks /create /tn "AulaPro Backup" /tr "powershell -File C:\laragon\www\pfc\security\backups\backup.ps1" /sc daily /st 03:15

$ErrorActionPreference = 'Stop'

# ── Configuración ────────────────────────────────────────────
$AppDir        = 'C:\laragon\www\pfc'
$BackupRoot    = 'D:\Backups\AulaPro'          # idealmente OTRO disco
$DbName        = 'pfc'                           # nombre local de la BD
$DbUser        = 'root'
$DbPass        = ''                              # Laragon root suele ir sin contraseña
$MysqlDumpExe  = (Get-ChildItem 'C:\laragon\bin\mysql\*\bin\mysqldump.exe' | Select-Object -First 1).FullName
$RetentionDays = 14

$ts   = Get-Date -Format 'yyyyMMdd_HHmmss'
$dest = Join-Path $BackupRoot $ts
New-Item -ItemType Directory -Force -Path $dest | Out-Null

# ── 1) Base de datos ─────────────────────────────────────────
$dumpFile = Join-Path $dest 'db.sql'
$passArg  = if ([string]::IsNullOrEmpty($DbPass)) { @() } else { @("-p$DbPass") }
& $MysqlDumpExe "-u$DbUser" @passArg --single-transaction --routines --triggers --events --default-character-set=utf8mb4 $DbName | Out-File -Encoding utf8 $dumpFile
Compress-Archive -Path $dumpFile -DestinationPath "$dumpFile.zip" -Force
Remove-Item $dumpFile

# ── 2) Uploads ───────────────────────────────────────────────
$uploads = Join-Path $AppDir 'public\uploads'
if (Test-Path $uploads) {
    Compress-Archive -Path $uploads -DestinationPath (Join-Path $dest 'uploads.zip') -Force
}

# ── 3) Rotación ──────────────────────────────────────────────
Get-ChildItem $BackupRoot -Directory |
  Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-$RetentionDays) } |
  Remove-Item -Recurse -Force

Write-Host "Backup completado: $dest"
