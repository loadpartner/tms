docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

export PATH=./vendor/bin:$PATH

cp .env.example .env

sail up -d

touch database/database.sqlite

sail artisan migrate

sail npm install

sail artisan key:generate

sail npm run dev
