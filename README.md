# Inventory management system
## Useful commands:
### Migration:
- migrate a specific table with path :
```laravel
 php artisan migrate --path=database/migrations/2023_04_11_164724_create_demande_comptes_table.php
```
- rolling back migrations by steps :
```laravel
 php artisan migrate --step=2
```
### Controllers:
```laravel
php artisan make:controller [--api] [--type TYPE] [--force] [-i|--invokable] [-m|--model [MODEL]] [-p|--parent [PARENT]] [-r|--resource] [-R|--requests] [-s|--singleton] [--creatable] [--test] [--pest] [--] <name>
```
- Quotes :
```laravel 
php artisan inspire
```


