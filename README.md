skeleton for speaker design library, reworked from scratch for licensing.

**Prepare Files**
<cp storage/garage.toml.example storage/garage.toml> Create security keys
<cp env.example .env> Create security keys and setup environment
<cp storage/forum/forum.env.example storage/forum/forum.env> Create admin account

**Start all services**
docker compose up -d

**First Time Run**
composer update
composer install
php artisan migrate
php artisan db:seed
php artisan filament:install
php artisan filament:user
php artisan filament:assets

Node is for development
Garage to manage S3 for object storage(must create key, create uploads,assets,temp buckets, authorize key to all 3 buckets)
Mysql for unified db (site and forum)
Filament for admin panel and forms, styles mapped to DaisyUI Variables in app.css for unified styling
