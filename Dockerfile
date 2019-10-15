# install our php stuff
FROM composer:1.9.0
COPY configurator /app
RUN cd /app && \
    composer install --ignore-platform-reqs --no-scripts && \
    composer dump-autoload --optimize --no-dev --classmap-authoritative

FROM ubuntu:latest
ARG TAG
LABEL TAG=${TAG}

ENV ACME_EMAIL="test@example.com"
ENV ACME_STORAGE="/traefik/acme.json"

ENV TINI_VERSION=0.18.0
ENV GOENVSUBST_VERSION=0.2.0

RUN apt-get update -y && \
    apt-get upgrade -y && \
    apt-get install -y tzdata ca-certificates curl php-cli && \
    apt-get clean && \
    adduser www-data root && \
    mkdir /traefik

ADD https://github.com/krallin/tini/releases/download/v${TINI_VERSION}/tini-amd64 /tini
ADD https://github.com/libgraviton/goenvsubst/releases/download/v${GOENVSUBST_VERSION}/goenvsubst-amd64 /envsubst

COPY --from=traefik:2.0 /usr/local/bin/traefik /traefik/traefik

# our configurator
COPY --from=0 /app /opt/configurator

COPY src/ /

RUN chown -R www-data:root /traefik /opt/configurator && \
    chmod -R ug+rwx /traefik && \
    chmod +x /traefik/traefik /tini /envsubst /entrypoint.sh

USER www-data

ENTRYPOINT ["/tini", "--"]
CMD ["/bin/bash", "/entrypoint.sh"]