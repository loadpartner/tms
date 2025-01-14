# LoadPartner TMS
[![Deploy to DO](https://www.deploytodo.com/do-btn-blue.svg)](https://cloud.digitalocean.com/apps/new?repo=https://github.com/loadpartner/tms/tree/do-deploy-button)

Open source and extensible freight brokerage TMS


### Development
Standing up the development environment after cloning the repository should follow these steps:

#### Setup [Laravel Sail](https://laravel.com/docs/11.x/sail)
``` bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

Can't find `sail` in your path? Add to your bashrc/zshrc
``` bash
export PATH=./vendor/bin:$PATH
```
  
#### Copy `env` file
``` bash
cp .env.example .env
```

#### Up sail
``` bash
sail up -d
```

#### Setup database 
``` bash
sail artisan migrate
```

#### Install npm modules
``` bash
sail npm install
```

#### Generate APP_KEY
``` bash
sail artisan key:generate
```

#### Run Vite
``` bash
sail npm run dev
```