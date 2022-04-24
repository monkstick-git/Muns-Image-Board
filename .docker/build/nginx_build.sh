#!/bin/bash
set -u # Treat unset variables as an error when substituting
set -e # Exit if any command returns a non-zero status
set +o pipefail # Ignore for piped commands

# Select image name and tag
echo "🧮 | Calculating which Branch / Tag / Commithash to use"
repo=$(git remote get-url --push origin | sed -E 's|git@.*/(.*)\.git$|\1|')
if [[ "$#" -gt 1 ]]; then
  echo "ERROR: Illegal number of arguments. Script takes one optional argument for image tag."
  echo "       If not arguments are passed the image tag is selected from the repository"
  exit 1
elif [[ "$#" -eq 1 ]]; then
  tagToBuild="${1}"
else
  commit=$(git log -n1 --format='%H')
  branch=$( ( git log -n1 --format='%D' | sed "s|origin/HEAD||" | grep -o -e "origin/[^ ,]*" || echo "" ) | sed "s|origin/||" | head -1 )
  tag=$( ( git log -n1 --format='%D' | grep -o -e "tag:[^,]*" || echo "" ) | sed "s|tag: *||" | head -1 )
  tagToBuild=${tag}
  [ -z ${tag} ] && tagToBuild=${branch}
  [ -z ${tag} ] && [ -z ${branch} ] && tagToBuild=${commit}
fi
echo "🧮 | Done!"


echo "🔧 | Creating Dockerfile for Nginx"
touch Dockerfile
echo "FROM compucorp/nginx-waf:1.21.4-owasp3.2.0" > Dockerfile
echo "COPY --chown=1001 .docker/build/nginx_content/includes /etc/nginx/conf.d/includes" >>Dockerfile
echo "COPY --chown=1001 .docker/build/nginx_content/site.conf /etc/nginx/conf.d/default.conf" >>Dockerfile
echo "COPY --chown=1001 .docker/build/nginx_content/nginx.conf /etc/nginx/nginx.conf" >>Dockerfile

# foreach folder in $(ls -d */), copy the folder to the docker image
for folder in $(find . -mindepth 1 -maxdepth 1 -not -path '*/.*' -type d); do
  echo "COPY --chown=1001 $folder /var/www/default/htdocs/httpdocs/$folder" >>Dockerfile
done

# foreach file in current directory, copy to docker image
for file in $(ls *.*); do
  echo "COPY --chown=1001 $file /var/www/default/htdocs/httpdocs/" >>Dockerfile
done
echo "🔧 | Done!"

echo "🏗️ | Building Docker Image"
docker build . -t ${repo}_nginx:${tagToBuild}
echo "🏗️ | Done!"

echo "🎉 | Created image: ${repo}_nginx:${tagToBuild}"
