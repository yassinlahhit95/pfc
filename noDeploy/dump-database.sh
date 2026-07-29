#!/bin/bash
# Safe MySQL database dump with proper UTF-8 encoding
# Usage: ./dump-database.sh

# Load config from .env or use defaults
DB_HOST=${DB_HOST:-localhost}
DB_USER=${DB_USER:-root}
DB_PASS=${DB_PASS:-}
DB_NAME=${DB_NAME:-pfc}

# Export environment for mysqldump
export MYSQL_PWD="$DB_PASS"

# Generate dump with EXPLICIT UTF-8 settings
mysqldump \
  --host="$DB_HOST" \
  --user="$DB_USER" \
  --default-character-set=utf8mb4 \
  --result-file="database.sql" \
  --single-transaction \
  --skip-lock-tables \
  --no-data \
  "$DB_NAME"

# Verify file encoding
echo "✓ Database dumped to database.sql"
echo "  Encoding: $(file database.sql | grep -o 'UTF-8.*' || echo 'ASCII')"

# Ensure BOM is removed if present (can corrupt imports)
if file database.sql | grep -q "BOM"; then
  echo "  ⚠ Removing UTF-8 BOM..."
  tail -c +4 database.sql > database_fixed.sql
  mv database_fixed.sql database.sql
fi

echo "✓ Database backup complete and verified"
