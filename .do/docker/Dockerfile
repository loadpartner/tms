FROM ubuntu:24.04

ARG WWWGROUP=1000
ARG NODE_VERSION=22
ARG PHP_VERSION=8.4
ARG DOCKERFILE_PATH=.do/docker

WORKDIR /var/www/html
COPY . .

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_USER="www-data"

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::BrokenProxy    true;" >> /etc/apt/apt.conf.d/99custom

RUN apt-get update && apt-get upgrade -y \
    && mkdir -p /etc/apt/keyrings \
    && apt-get install -y vim gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nginx \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php$PHP_VERSION-fpm php$PHP_VERSION-cli \
        php$PHP_VERSION-pgsql php$PHP_VERSION-sqlite3 php$PHP_VERSION-gd \
        php$PHP_VERSION-curl php$PHP_VERSION-imap  php$PHP_VERSION-mbstring \
        php$PHP_VERSION-xml php$PHP_VERSION-zip php$PHP_VERSION-bcmath php$PHP_VERSION-soap \
        php$PHP_VERSION-intl php$PHP_VERSION-readline \
        php$PHP_VERSION-ldap \
        php$PHP_VERSION-msgpack php$PHP_VERSION-igbinary php$PHP_VERSION-redis \
        php$PHP_VERSION-pcov php$PHP_VERSION-imagick \
        php$PHP_VERSION-dev php$PHP_VERSION-xdebug \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && npm install -g pnpm \
    && npm install -g bun \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /etc/apt/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt noble-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y yarn \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configure Nginx
RUN rm /etc/nginx/sites-enabled/default
COPY ${DOCKERFILE_PATH}/nginx.conf /etc/nginx/sites-available/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

COPY ${DOCKERFILE_PATH}/start-container /usr/local/bin/start-container
COPY ${DOCKERFILE_PATH}/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ${DOCKERFILE_PATH}/php.ini /etc/php/${PHP_VERSION}/fpm/conf.d/99-php.ini
COPY ${DOCKERFILE_PATH}/php-fpm.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf
RUN chmod +x /usr/local/bin/start-container

RUN cp .env.example .env \
    && composer install \
    && touch database/database.sqlite \
    && php artisan migrate --seed \
    && npm install \
    && php artisan key:generate \
    && npm run build 

# chown all files to www-data
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080/tcp

ENTRYPOINT ["start-container"]