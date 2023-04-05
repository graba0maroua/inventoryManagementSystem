# Inventory management system

Create a new Laravel project command:

```bash
 composer create-project laravel/laravel example-app
```
Migration
```bash
 php artisan migrate
```
Run
```bash
 php artisan serve
```
ApiController
```bash
php artisan make:controller theController --api
```
SQLSERVER Connection
- database.php
```bash
'default' => env('DB_CONNECTION', 'sqlsrv'),
____________________________________________
 'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            //'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],
```
- .env
```bash
DB_CONNECTION=sqlsrv
DB_HOST=DESKTOP-HO21M5V\SQLEXPRESS
DB_PORT=1433
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=maroua
```
- you might require to run these commands in error cases :
```bash
 composer dump-autoload
  php artisan config:clear
```


