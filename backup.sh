#!/usr/bin/env bash
#
# MySQL backup script for vcr_rt_rw
# Usage: ./backup.sh [project_root]
# If project_root is omitted, uses the directory containing this script.
#

set -euo pipefail

# Project root: directory containing this script (and .env)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="${1:-$SCRIPT_DIR}"
BACKUP_DIR="${HOME}/backup-vcr"
DB_NAME="vcr_rt_rw"
RETENTION_DAYS=7

# Load DB credentials from .env
ENV_FILE="${PROJECT_ROOT}/.env"
if [[ ! -f "$ENV_FILE" ]]; then
  echo "Error: .env not found at $ENV_FILE" >&2
  exit 1
fi

export DB_HOST="127.0.0.1"
export DB_PORT="3306"
export DB_DATABASE="$DB_NAME"
export DB_USERNAME="root"
export DB_PASSWORD=""

while IFS= read -r line; do
  [[ "$line" =~ ^#.*$ ]] && continue
  [[ -z "${line// }" ]] && continue
  if [[ "$line" =~ ^DB_HOST= ]]; then DB_HOST="${line#DB_HOST=}"; fi
  if [[ "$line" =~ ^DB_PORT= ]]; then DB_PORT="${line#DB_PORT=}"; fi
  if [[ "$line" =~ ^DB_DATABASE= ]]; then DB_DATABASE="${line#DB_DATABASE=}"; fi
  if [[ "$line" =~ ^DB_USERNAME= ]]; then DB_USERNAME="${line#DB_USERNAME=}"; fi
  if [[ "$line" =~ ^DB_PASSWORD= ]]; then DB_PASSWORD="${line#DB_PASSWORD=}"; fi
done < "$ENV_FILE"

# Strip optional quotes from values
strip_quotes() { echo "$1" | sed -e "s/^['\"]//" -e "s/['\"]$//"; }
DB_HOST=$(strip_quotes "$DB_HOST")
DB_PORT=$(strip_quotes "$DB_PORT")
DB_DATABASE=$(strip_quotes "$DB_DATABASE")
DB_USERNAME=$(strip_quotes "$DB_USERNAME")
DB_PASSWORD=$(strip_quotes "$DB_PASSWORD")

mkdir -p "$BACKUP_DIR"

TIMESTAMP=$(date +%Y-%m-%d_%H-%M)
BACKUP_FILE="${BACKUP_DIR}/${DB_NAME}_${TIMESTAMP}.sql"

echo "Backing up ${DB_DATABASE} to ${BACKUP_FILE} ..."

if [[ -n "$DB_PASSWORD" ]]; then
  MYSQL_PWD="$DB_PASSWORD" mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" \
    --single-transaction --quick --lock-tables=false \
    "$DB_DATABASE" > "$BACKUP_FILE"
else
  mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" \
    --single-transaction --quick --lock-tables=false \
    "$DB_DATABASE" > "$BACKUP_FILE"
fi

echo "Backup completed: ${BACKUP_FILE}"

# Remove backups older than RETENTION_DAYS
echo "Removing backups older than ${RETENTION_DAYS} days ..."
find "$BACKUP_DIR" -maxdepth 1 -name "${DB_NAME}_*.sql" -type f -mtime +${RETENTION_DAYS} -delete

echo "Done."
