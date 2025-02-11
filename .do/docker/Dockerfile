FROM ghcr.io/loadpartner/tms:image-base

WORKDIR /var/www/html
COPY . .

ENV DEBIAN_FRONTEND=noninteractive


RUN cp .env.example .env \
    && composer install \
    && touch database/database.sqlite \
    && npm install \
    && php artisan key:generate \
    && npm run build 

# chown all files to www-data
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080/tcp

ENTRYPOINT ["start-container"]