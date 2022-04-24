#!/bin/bash
set -u          # Treat unset variables as an error when substituting
set -e          # Exit if any command returns a non-zero status
set -o pipefail # Same for piped commands

RELEASE_SCRIPT="/var/www/default/htdocs/httpdocs/sites/default/site_release.sh"
[ ! -f "${RELEASE_SCRIPT}" ] && echo "Startup Script | No Release script found here: ${RELEASE_SCRIPT}" && exit 0
CURRENT_USER=$(whoami)
RUN_USER=$(id -un ${NEW_UID})
if [ ${CURRENT_USER} != ${RUN_USER} ]; then
  echo "Startup Script | Changing users from ${CURRENT_USER} to ${RUN_USER}"
  su -m ${RUN_USER} -c "HOME=/home/${RUN_USER} bash ${RELEASE_SCRIPT}"
else
  bash ${RELEASE_SCRIPT}
fi
