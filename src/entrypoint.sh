#!/bin/bash

if [ -n "$CONFIG" ]; then
  cat ${CONFIG} | /envsubst > /traefik/traefik.yml
else
  /usr/bin/php -d variables_order=E /opt/configurator/configure.php | /envsubst > /traefik/traefik.yml
fi

cat /traefik_config.yml | /envsubst > /traefik/traefik_static.yml
exec /traefik/traefik --configFile=/traefik/traefik_static.yml

