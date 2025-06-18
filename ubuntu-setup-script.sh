#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

PROGRESS_FILE=".setup-progress"
VARS_FILE=".setup-vars"

# Create files if they don't exist
touch "$PROGRESS_FILE"
touch "$VARS_FILE"

# Source variables file if it exists
if [[ -f "$VARS_FILE" ]]; then
    source "$VARS_FILE"
fi

echo -e "${PURPLE}🚛 ===== LoadPartner TMS Magic Deployment Wizard ===== 🚛${NC}"
echo -e "${CYAN}🎉 Get ready to deploy the most awesome TMS in the universe! 🎉${NC}"
echo ""

# Function to generate random password
generate_random_password() {
    openssl rand -base64 32 | tr -d "=+/" | cut -c1-25
}

prompt_if_empty() {
    local var_name=$1
    local prompt=$2
    local default_value=$3
    local current_value="${!var_name}"
    
    if [[ -z "$current_value" ]]; then
        if [[ -n "$default_value" ]]; then
            read -p "$prompt [$default_value]: " value
        else
            read -p "$prompt: " value
        fi
        # Trim whitespace
        value=$(echo "$value" | xargs)
        if [[ -z "$value" && -n "$default_value" ]]; then
            value="$default_value"
        fi
        if [[ -n "$value" ]]; then
            export "$var_name"="$value"
            echo "$var_name=\"$value\"" >> "$VARS_FILE"
        else
            echo -e "${RED}❌ This field is required! Please enter a value.${NC}"
            prompt_if_empty "$var_name" "$prompt" "$default_value"
        fi
    else
        export "$var_name"="$current_value"
        echo -e "${YELLOW}🔄 Using saved value for $var_name: $current_value${NC}"
    fi
}

prompt_optional() {
    local var_name=$1
    local prompt=$2
    local current_value="${!var_name}"
    
    if [[ -z "$current_value" ]]; then
        read -p "$prompt: " value
        # Trim whitespace
        value=$(echo "$value" | xargs)
        if [[ -n "$value" ]]; then
            export "$var_name"="$value"
            echo "$var_name=\"$value\"" >> "$VARS_FILE"
        else
            # Ensure variable is unset if empty
            unset "$var_name"
        fi
    else
        export "$var_name"="$current_value"
        echo -e "${YELLOW}🔄 Using saved value for $var_name: $current_value${NC}"
    fi
}

# Collect all required variables with fun prompts
echo -e "${BLUE}📝 Let's gather some info to make your TMS deployment perfect!${NC}"
echo ""

prompt_if_empty "APP_NAME" "🏷️  What should we call your TMS instance?" "loadpartner-tms"
prompt_if_empty "LINUX_USER" "👤 What Linux user should manage your TMS?" "tmsadmin"
prompt_if_empty "DB_NAME" "🗄️  PostgreSQL database name for your TMS" "loadpartner_tms"
prompt_if_empty "DB_USER" "👨‍💼 PostgreSQL database user" "tms_user"

if [[ -z "$DB_PASS" ]]; then
    echo -e "${YELLOW}🔐 Time for a super secret password!${NC}"
    RANDOM_PASS=$(generate_random_password)
    read -s -p "🔑 Enter PostgreSQL password for user '$DB_USER' [press ENTER for random]: " DB_PASS
    echo ""
    if [[ -z "$DB_PASS" ]]; then
        DB_PASS="$RANDOM_PASS"
        echo -e "${GREEN}🎲 Generated random password for database user${NC}"
    fi
    echo "DB_PASS=\"$DB_PASS\"" >> "$VARS_FILE"
fi

echo -e "${CYAN}🌐 Got a fancy domain for your TMS?${NC}"
prompt_optional "DOMAIN_NAME" "🏠 Enter domain (or leave blank to use IP)"

# Hardcode the TMS repository
GIT_REPO="https://github.com/loadpartner/tms.git"

# Set derived variables
APP_PATH="/var/www/$APP_NAME"
PHP_VERSION="8.4"

# Validate required variables
if [[ -z "$APP_NAME" || -z "$LINUX_USER" || -z "$DB_NAME" || -z "$DB_USER" || -z "$DB_PASS" ]]; then
    echo -e "${RED}💥 Oops! Missing some required info. Please run the script again.${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}🎯 LoadPartner TMS Configuration Summary:${NC}"
echo -e "${CYAN}📦 TMS Instance: $APP_NAME${NC}"
echo -e "${CYAN}👤 Linux User: $LINUX_USER${NC}"
echo -e "${CYAN}🗄️  Database: $DB_NAME${NC}"
echo -e "${CYAN}👨‍💼 DB User: $DB_USER${NC}"
echo -e "${CYAN}🔑 DB Password: ${DB_PASS:0:3}***${NC}"
echo -e "${CYAN}📂 TMS Repo: $GIT_REPO${NC}"
echo -e "${CYAN}🌐 Domain: ${DOMAIN_NAME:-'(will use server IP)'}${NC}"
echo -e "${CYAN}📁 Install Path: $APP_PATH${NC}"
echo ""
echo -e "${PURPLE}🚀 Ready to launch your TMS into orbit! 🚀${NC}"
echo ""

STEP() {
    local name=$1
    shift
    
    if grep -q "^$name$" "$PROGRESS_FILE" 2>/dev/null; then
        echo -e "${YELLOW}✅ Skipping $name (already completed like a boss!)${NC}"
        return 0
    fi
    
    echo -e "${BLUE}🔧 === $name ===${NC}"
    
    if eval "$@"; then
        echo "$name" >> "$PROGRESS_FILE"
        echo -e "${GREEN}🎉 Completed: $name${NC}"
    else
        echo -e "${RED}💥 Failed: $name${NC}"
        exit 1
    fi
}

# System update with fun messages
STEP "🔄 Updating the mothership" '
echo -e "${CYAN}📡 Downloading the latest and greatest packages...${NC}" &&
sudo apt update && 
sudo apt upgrade -y && 
sudo apt install -y curl git unzip zip software-properties-common lsb-release ca-certificates apt-transport-https gnupg &&
echo -e "${GREEN}✨ System is now fresh and ready!${NC}"
'

# Install Nginx
STEP "🌐 Installing Nginx web server" '
echo -e "${CYAN}🚀 Setting up the web server that will serve your TMS...${NC}" &&
sudo apt install -y nginx &&
sudo systemctl enable nginx &&
sudo systemctl start nginx &&
echo -e "${GREEN}🎯 Nginx is locked and loaded!${NC}"
'

# Install PHP
STEP "🐘 Installing PHP powerhouse" '
echo -e "${CYAN}⚡ Installing PHP '"$PHP_VERSION"' - the engine of your TMS...${NC}" &&
sudo add-apt-repository ppa:ondrej/php -y &&
sudo apt update &&
sudo apt install -y php'"$PHP_VERSION"' php'"$PHP_VERSION"'-fpm php'"$PHP_VERSION"'-cli php'"$PHP_VERSION"'-mbstring php'"$PHP_VERSION"'-xml php'"$PHP_VERSION"'-curl php'"$PHP_VERSION"'-pgsql php'"$PHP_VERSION"'-sqlite3 php'"$PHP_VERSION"'-bcmath php'"$PHP_VERSION"'-zip php'"$PHP_VERSION"'-gd php'"$PHP_VERSION"'-common &&
sudo systemctl enable php'"$PHP_VERSION"'-fpm &&
sudo systemctl start php'"$PHP_VERSION"'-fpm &&
echo -e "${GREEN}🔥 PHP is ready to power your TMS!${NC}"
'

# Install Composer
STEP "🎼 Installing Composer dependency manager" '
echo -e "${CYAN}📦 Getting Composer - the package wizard...${NC}" &&
curl -sS https://getcomposer.org/installer | php &&
sudo mv composer.phar /usr/local/bin/composer &&
sudo chmod +x /usr/local/bin/composer &&
echo -e "${GREEN}🎵 Composer is ready to orchestrate your dependencies!${NC}"
'

# Install PostgreSQL
STEP "🐘 Installing PostgreSQL database" '
echo -e "${CYAN}🗄️  Setting up PostgreSQL - where your TMS data will live...${NC}" &&
curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo gpg --dearmor -o /usr/share/keyrings/postgresql.gpg &&
echo "deb [signed-by=/usr/share/keyrings/postgresql.gpg] http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" | sudo tee /etc/apt/sources.list.d/pgdg.list &&
sudo apt update &&
sudo apt install -y postgresql-17 postgresql-client-17 &&
sudo systemctl enable postgresql &&
sudo systemctl start postgresql &&
echo -e "${GREEN}🎯 PostgreSQL is ready to store all your TMS magic!${NC}"
'

# Create user
STEP "👤 Creating TMS user" '
echo -e "${CYAN}👨‍💼 Creating user '"$LINUX_USER"' to manage your TMS...${NC}" &&
if ! id "$LINUX_USER" &>/dev/null; then
    sudo adduser --disabled-password --gecos "" "$LINUX_USER" &&
    sudo usermod -aG www-data "$LINUX_USER" &&
    echo -e "${GREEN}🎉 User '"$LINUX_USER"' is ready to rock!${NC}"
else
    echo -e "${YELLOW}👤 User '"$LINUX_USER"' already exists - perfect!${NC}"
fi
'

# Generate SSH key
STEP "🔑 Generating SSH keys" '
echo -e "${CYAN}🔐 Creating SSH keys for secure Git access...${NC}" &&
sudo -u "$LINUX_USER" mkdir -p /home/"$LINUX_USER"/.ssh &&
sudo -u "$LINUX_USER" chmod 700 /home/"$LINUX_USER"/.ssh &&
if [[ ! -f /home/"$LINUX_USER"/.ssh/id_ed25519 ]]; then
    sudo -u "$LINUX_USER" ssh-keygen -t ed25519 -N "" -f /home/"$LINUX_USER"/.ssh/id_ed25519 &&
    echo -e "${GREEN}🔑 Fresh SSH keys generated!${NC}"
else
    echo -e "${YELLOW}🔑 SSH keys already exist - we'"'"'re good to go!${NC}"
fi
'

# SSH key upload prompt
if ! grep -q "^ssh_key_uploaded$" "$PROGRESS_FILE"; then
    echo ""
    echo -e "${PURPLE}🚨 === IMPORTANT: GitHub Access Required === 🚨${NC}"
    echo -e "${YELLOW}📋 Copy this SSH key and add it to your GitHub account:${NC}"
    echo -e "${CYAN}🔗 Go to: https://github.com/settings/ssh/new${NC}"
    echo ""
    echo -e "${GREEN}--- SSH PUBLIC KEY (copy everything below) ---${NC}"
    sudo cat /home/"$LINUX_USER"/.ssh/id_ed25519.pub
    echo -e "${GREEN}--- END OF SSH KEY ---${NC}"
    echo ""
    echo -e "${YELLOW}⏳ Waiting for you to add the key to GitHub...${NC}"
    read -p "🎯 Press ENTER after adding the SSH key to GitHub and you're ready to continue..."
    echo "ssh_key_uploaded" >> "$PROGRESS_FILE"
    echo -e "${GREEN}🎉 Awesome! Let's continue with the TMS deployment!${NC}"
fi

# Install NVM and Node.js
STEP "📦 Installing Node.js via NVM" '
echo -e "${CYAN}⚡ Installing Node.js for frontend magic...${NC}" &&
sudo -u "$LINUX_USER" bash -c "
export NVM_DIR=\"/home/$LINUX_USER/.nvm\" &&
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash &&
source \"/home/$LINUX_USER/.nvm/nvm.sh\" &&
nvm install --lts &&
nvm use --lts
" &&
echo -e "${GREEN}🚀 Node.js is ready for frontend awesomeness!${NC}"
'

# Clone repository
STEP "📥 Cloning LoadPartner TMS repository" '
echo -e "${CYAN}🚛 Downloading the LoadPartner TMS from GitHub...${NC}" &&
sudo mkdir -p "$APP_PATH" &&
sudo chown -R "$LINUX_USER":"$LINUX_USER" "$APP_PATH" &&
sudo -u "$LINUX_USER" bash -c "
cd /home/$LINUX_USER &&
ssh-keyscan -H github.com >> ~/.ssh/known_hosts 2>/dev/null || true &&
git clone \"$GIT_REPO\" \"$APP_PATH\"
" &&
echo -e "${GREEN}🎉 LoadPartner TMS successfully downloaded!${NC}"
'

# Configure PostgreSQL
STEP "🗄️  Configuring TMS database" '
echo -e "${CYAN}🔧 Setting up your TMS database with all the right permissions...${NC}" &&
sudo -i -u postgres psql <<EOF
CREATE DATABASE "$DB_NAME";
CREATE USER "$DB_USER" WITH PASSWORD '"'"'$DB_PASS'"'"';
GRANT ALL PRIVILEGES ON DATABASE "$DB_NAME" TO "$DB_USER";
\c "$DB_NAME"
GRANT USAGE, CREATE ON SCHEMA public TO "$DB_USER";
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO "$DB_USER";
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO "$DB_USER";
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO "$DB_USER";
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO "$DB_USER";
EOF
echo -e "${GREEN}🎯 TMS database is configured and ready!${NC}"
'

# Install Laravel dependencies
STEP "📦 Installing TMS dependencies" '
echo -e "${CYAN}⚡ Installing all the PHP packages your TMS needs...${NC}" &&
cd "$APP_PATH" &&
sudo -u "$LINUX_USER" composer install --no-dev --optimize-autoloader &&
echo -e "${GREEN}🎵 All TMS dependencies are locked and loaded!${NC}"
'

# Configure Laravel environment
STEP "⚙️  Configuring TMS environment" '
echo -e "${CYAN}🔧 Setting up your TMS configuration for production...${NC}" &&
cd "$APP_PATH" &&
if [[ ! -f .env && -f .env.example ]]; then
    sudo -u "$LINUX_USER" cp .env.example .env
fi &&
sudo -u "$LINUX_USER" php artisan key:generate --force &&
# Set production environment settings
sudo -u "$LINUX_USER" sed -i "s/^#*APP_ENV=.*/APP_ENV=production/" .env &&
sudo -u "$LINUX_USER" sed -i "s/^#*APP_DEBUG=.*/APP_DEBUG=false/" .env &&
# Configure database connection for PostgreSQL
# Handle both commented (#DB_CONNECTION) and uncommented (DB_CONNECTION) lines
sudo -u "$LINUX_USER" sed -i "s/^#*DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env &&
sudo -u "$LINUX_USER" sed -i "s/^#*DB_HOST=.*/DB_HOST=127.0.0.1/" .env &&
sudo -u "$LINUX_USER" sed -i "s/^#*DB_PORT=.*/DB_PORT=5432/" .env &&
sudo -u "$LINUX_USER" sed -i "s/^#*DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env &&
sudo -u "$LINUX_USER" sed -i "s/^#*DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env &&
sudo -u "$LINUX_USER" sed -i "s/^#*DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env &&
# If the lines don'"'"'t exist at all, add them
if ! grep -q "^APP_ENV=" .env; then
    echo "APP_ENV=production" | sudo -u "$LINUX_USER" tee -a .env > /dev/null
fi &&
if ! grep -q "^APP_DEBUG=" .env; then
    echo "APP_DEBUG=false" | sudo -u "$LINUX_USER" tee -a .env > /dev/null
fi &&
if ! grep -q "^DB_CONNECTION=" .env; then
    echo "DB_CONNECTION=pgsql" | sudo -u "$LINUX_USER" tee -a .env > /dev/null
fi &&
if ! grep -q "^DB_HOST=" .env; then
    echo "DB_HOST=127.0.0.1" | sudo -u "$LINUX_USER" tee -a .env > /dev/null
fi &&
if ! grep -q "^DB_PORT=" .env; then
    echo "DB_PORT=5432" | sudo -u "$LINUX_USER" tee -a .env > /dev/null
fi &&
if ! grep -q "^DB_DATABASE=" .env; then
    echo "DB_DATABASE=$DB_NAME" | sudo -u "$LINUX_USER" tee -a .env > /dev/null
fi &&
if ! grep -q "^DB_USERNAME=" .env; then
    echo "DB_USERNAME=$DB_USER" | sudo -u "$LINUX_USER" tee -a .env > /dev/null
fi &&
if ! grep -q "^DB_PASSWORD=" .env; then
    echo "DB_PASSWORD=$DB_PASS" | sudo -u "$LINUX_USER" tee -a .env > /dev/null
fi &&
echo -e "${GREEN}🎯 TMS environment configured for production excellence!${NC}"
'

# Run database migrations and optimize Laravel
STEP "🗄️  Setting up TMS database structure" '
echo -e "${CYAN}🔧 Creating all the database tables your TMS needs...${NC}" &&
cd "$APP_PATH" &&
echo -e "${YELLOW}📊 Running database migrations...${NC}" &&
sudo -u "$LINUX_USER" php artisan migrate --force &&
echo -e "${YELLOW}⚡ Optimizing TMS for maximum performance...${NC}" &&
sudo -u "$LINUX_USER" php artisan config:cache &&
sudo -u "$LINUX_USER" php artisan route:cache &&
sudo -u "$LINUX_USER" php artisan view:cache &&
echo -e "${GREEN}🚀 TMS database is ready and optimized!${NC}"
'

# Build frontend assets
STEP "🎨 Building TMS frontend" '
echo -e "${CYAN}🎨 Building the beautiful TMS user interface...${NC}" &&
cd "$APP_PATH" &&
if [[ -f package.json ]]; then
    echo -e "${YELLOW}📦 Installing frontend dependencies...${NC}" &&
    sudo -u "$LINUX_USER" bash -c "
    source /home/$LINUX_USER/.nvm/nvm.sh &&
    npm install
    " &&
    echo -e "${YELLOW}🔨 Building frontend assets...${NC}" &&
    sudo -u "$LINUX_USER" bash -c "
    source /home/$LINUX_USER/.nvm/nvm.sh &&
    npm run build
    " &&
    echo -e "${GREEN}✨ TMS frontend is built and beautiful!${NC}"
else
    echo -e "${YELLOW}📝 No package.json found, skipping frontend build${NC}"
fi
'

# Set permissions
STEP "🔐 Setting up file permissions" '
echo -e "${CYAN}🔒 Configuring secure file permissions for your TMS...${NC}" &&
sudo chown -R "$LINUX_USER":www-data "$APP_PATH" &&
sudo find "$APP_PATH" -type d -exec chmod 755 {} \; &&
sudo find "$APP_PATH" -path "$APP_PATH/node_modules" -prune -o -type f -exec chmod 644 {} \; &&
if [[ -d "$APP_PATH/storage" ]]; then
    sudo chmod -R 775 "$APP_PATH/storage" &&
    sudo chown -R "$LINUX_USER":www-data "$APP_PATH/storage"
fi &&
if [[ -d "$APP_PATH/bootstrap/cache" ]]; then
    sudo chmod -R 775 "$APP_PATH/bootstrap/cache" &&
    sudo chown -R "$LINUX_USER":www-data "$APP_PATH/bootstrap/cache"
fi &&
echo -e "${GREEN}🔐 File permissions are locked down tight!${NC}"
'

# Configure Nginx
STEP "🌐 Configuring Nginx for TMS" '
echo -e "${CYAN}⚙️  Setting up Nginx to serve your awesome TMS...${NC}" &&
sudo rm -f /etc/nginx/sites-enabled/default &&
SERVER_NAME_BLOCK="_" &&
if [[ -n "$DOMAIN_NAME" ]]; then
    SERVER_NAME_BLOCK="$DOMAIN_NAME www.$DOMAIN_NAME _"
fi &&
NGINX_CONF="/etc/nginx/sites-available/$APP_NAME" &&
sudo tee "$NGINX_CONF" > /dev/null <<EOF
server {
    listen 80 default_server;
    server_name $SERVER_NAME_BLOCK;

    root $APP_PATH/public;
    index index.php index.html;

    access_log /var/log/nginx/$APP_NAME.access.log;
    error_log /var/log/nginx/$APP_NAME.error.log;

    # Client and buffer settings
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    client_header_buffer_size 32k;
    large_client_header_buffers 8 32k;

    # FastCGI buffer settings for handling large headers
    fastcgi_buffer_size 32k;
    fastcgi_buffers 8 32k;
    fastcgi_busy_buffers_size 64k;
    fastcgi_temp_file_write_size 64k;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        fastcgi_param HTTP_PROXY "";
        
        # FastCGI buffer settings (duplicate for this location block)
        fastcgi_buffer_size 32k;
        fastcgi_buffers 8 32k;
        fastcgi_busy_buffers_size 64k;
        fastcgi_connect_timeout 60s;
        fastcgi_send_timeout 60s;
        fastcgi_read_timeout 60s;
        
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Deny access to sensitive files
    location ~* \.(htaccess|htpasswd|ini|log|sh|sql|conf)\$ {
        deny all;
    }
}
EOF
sudo ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/ &&
sudo nginx -t &&
sudo systemctl reload nginx &&
echo -e "${GREEN}🎯 Nginx is configured and ready to serve your TMS!${NC}"
'

# Install and configure Supervisor
STEP "👮 Installing Supervisor process manager" '
echo -e "${CYAN}👮‍♂️ Installing Supervisor to keep your TMS running smoothly...${NC}" &&
sudo apt install -y supervisor &&
sudo systemctl enable supervisor &&
sudo systemctl start supervisor &&
echo -e "${GREEN}👮 Supervisor is on duty!${NC}"
'

# Configure Supervisor for Laravel queues
STEP "⚡ Configuring TMS background workers" '
echo -e "${CYAN}⚙️  Setting up background workers for your TMS...${NC}" &&
SUPERVISOR_CONF="/etc/supervisor/conf.d/tms-worker.conf" &&
sudo tee "$SUPERVISOR_CONF" > /dev/null <<EOF
[program:tms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $APP_PATH/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=$LINUX_USER
numprocs=1
redirect_stderr=true
stdout_logfile=$APP_PATH/storage/logs/worker.log
stopwaitsecs=3600
EOF
sudo supervisorctl reread &&
sudo supervisorctl update &&
sudo supervisorctl start tms-worker:* &&
echo -e "${GREEN}⚡ TMS background workers are ready to work hard!${NC}"
'

# Configure Laravel Scheduler Cron Job
STEP "⏰ Setting up TMS scheduler" '
echo -e "${CYAN}⏰ Setting up the TMS scheduler for automated tasks...${NC}" &&
CRON_COMMAND="* * * * * cd $APP_PATH && php artisan schedule:run >> /dev/null 2>&1" &&
# Check if cron job already exists to avoid duplicates
sudo -u "$LINUX_USER" bash -c "
if ! crontab -l 2>/dev/null | grep -F \"$APP_PATH && php artisan schedule:run\" > /dev/null; then
    (crontab -l 2>/dev/null; echo \"$CRON_COMMAND\") | crontab -
    echo \"TMS scheduler is now running every minute!\"
else
    echo \"TMS scheduler was already configured!\"
fi
" &&
if ! sudo -u "$LINUX_USER" crontab -l 2>/dev/null | grep -F "$APP_PATH && php artisan schedule:run" > /dev/null; then
    echo -e "${GREEN}⏰ TMS scheduler is now running every minute!${NC}"
else
    echo -e "${YELLOW}⏰ TMS scheduler was already configured!${NC}"
fi
'

echo ""
echo -e "${PURPLE}🎉 ===== DEPLOYMENT COMPLETE! ===== 🎉${NC}"
echo -e "${GREEN}🚛 LoadPartner TMS '$APP_NAME' is now LIVE and ready to manage loads! 🚛${NC}"
echo ""
echo -e "${CYAN}🌐 Access your TMS at: http://$(curl -s ifconfig.me)${NC}"
if [[ -n "$DOMAIN_NAME" ]]; then
    echo -e "${CYAN}🌐 Or at your domain: http://$DOMAIN_NAME${NC}"
fi
echo ""
echo -e "${YELLOW}🚀 Next Level Steps:${NC}"
echo -e "${BLUE}   🔒 Set up SSL/TLS with Let's Encrypt: sudo certbot --nginx${NC}"
echo -e "${BLUE}   🌐 Configure your domain's DNS to point to this server${NC}"
echo -e "${BLUE}   ⚙️  Review your TMS .env configuration${NC}"
echo -e "${BLUE}   💾 Set up automated backups for your database and files${NC}"
echo -e "${BLUE}   📊 TMS scheduler is running every minute for automated tasks${NC}"
echo -e "${BLUE}   👥 Create your first TMS admin user through the web interface${NC}"
echo ""
echo -e "${PURPLE}🎯 Your LoadPartner TMS is ready to revolutionize logistics! 🎯${NC}"
echo -e "${GREEN}Happy load managing! 🚛💨${NC}"
