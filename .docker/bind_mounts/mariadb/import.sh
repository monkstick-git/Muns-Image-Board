#!/bin/bash
set -u # Treat unset variables as an error when substituting
set -e # Exit if any command returns a non-zero status
set -o pipefail # Same for piped commands

# Set defaults
SKIP_DRUPAL="no"
SKIP_CIVICRM="no"
IMPORT_DRUPAL_DB_DUMP=""
IMPORT_CIVICRM_DB_DUMP=""
[ -f "/tmp/import/drupal.sql.gz" ] && IMPORT_DRUPAL_DB_DUMP="/tmp/import/drupal.sql.gz"
[ -f "/tmp/import/civicrm.sql.gz" ] && IMPORT_CIVICRM_DB_DUMP="/tmp/import/civicrm.sql.gz"
[ -f "/tmp/import/drupal.sql" ] && IMPORT_DRUPAL_DB_DUMP="/tmp/import/drupal.sql"
[ -f "/tmp/import/civicrm.sql" ] && IMPORT_CIVICRM_DB_DUMP="/tmp/import/civicrm.sql"
set +u # Ignore unset variables error to check provided env vars
( [ -z "$IMPORT_DRUPAL_DB_NAME" ]  || [ ! -f "${IMPORT_DRUPAL_DB_DUMP}"  ] ) && SKIP_DRUPAL="yes" &&  echo "DB Import Script | Skipping drupal db import due to missing vars or files."
( [ -z "$IMPORT_CIVICRM_DB_NAME" ] || [ ! -f "${IMPORT_CIVICRM_DB_DUMP}" ] ) && SKIP_CIVICRM="yes" && echo "DB Import Script | Skipping civicrm db import due to missing vars or files"
set -u # Treat unset variables as an error when substituting

echo "$MARIADB_ROOT_PASSWORD"

if [ "$SKIP_CIVICRM" != "yes" ]; then
  echo "DB Import Script | Creating civicrm database  🤖"
  mysql --user=root --password="$MARIADB_ROOT_PASSWORD" -e "CREATE DATABASE $IMPORT_CIVICRM_DB_NAME;"
  echo "DB Import Script | Importing civicrm database 📥"
  zcat -f "${IMPORT_CIVICRM_DB_DUMP}" |
  sed \
    -e 's/DEFINER[ ]*=[ ]*[^*]*\*/\*/' \
    -e 's/DEFINER[ ]*=[ ]*[^*]*PROCEDURE/PROCEDURE/' \
    -e 's/DEFINER[ ]*=[ ]*[^*]*FUNCTION/FUNCTION/' |\
  mysql \
    --user=root \
    --password="$MARIADB_ROOT_PASSWORD" \
    --database="$IMPORT_CIVICRM_DB_NAME"
  echo "DB Import Script | Done importing civicrm database 👍"
fi

if [ "$SKIP_DRUPAL" != "yes" ]; then
  echo "DB Import Script | Creating drupal database   🤖"
  mysql --user=root --password="$MARIADB_ROOT_PASSWORD" -e "CREATE DATABASE $IMPORT_DRUPAL_DB_NAME;"
  echo "DB Import Script | Importing drupal database  📥"
  zcat -f "${IMPORT_DRUPAL_DB_DUMP}" |
  sed \
    -e 's/DEFINER[ ]*=[ ]*[^*]*\*/\*/' \
    -e 's/DEFINER[ ]*=[ ]*[^*]*PROCEDURE/PROCEDURE/' \
    -e 's/DEFINER[ ]*=[ ]*[^*]*FUNCTION/FUNCTION/' |\
  mysql \
    --user=root \
    --password="$MARIADB_ROOT_PASSWORD" \
    --database="$IMPORT_DRUPAL_DB_NAME"
  echo "DB Import Script | Done importing drupal database 👍"
fi

echo "DB Import Script | Done importing database 🎉"
