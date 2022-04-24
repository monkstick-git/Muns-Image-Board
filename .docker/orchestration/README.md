# Generate
```
site_stack_name: "{{ deploy_matched_site.url | replace('.','_') }}"
site_path: "/mnt/volumes/{{ site_url }}"
site_nginx_image: "registry/repo_nginx:tag"
site_php_image: "registry/repo_php:tag"
```

# Sites List entry
```
_id: "some.site"
path: "/some/absolute/path"
volumes:
  - "private_files/"
  - "files/"
reporitory: "git@github.com/site"
self_signed: True
swarm_cluster: "some_cluster_name"
images:
  php: "client_php:version"
  nginx: "client_nginx:version"
basic_auth:
  - 'user1:SHA1_encoded_password'
  - 'user1:SHA1_encoded_password'
expiry_date: "unix_timestamp"
env_vars:
  CC_ENV: "production"
  DRUPAL_DB_NAME: "petname_drupal"
  CIVICRM_DB_NAME: "petname_civicrm"
  DRUPAL_DB_USER: "petname"
  DRUPAL_DB_PASS: "{{ vault_entry }}"
  CIVICRM_DB_USER: "petname"
  CIVICRM_DB_PASS: "{{ vault_entry }}"
  DRUPAL_HASH_SALT: "{{ vault_entry }}"
  CIVICRM_SITE_KEY: "{{ vault_entry }}"
```