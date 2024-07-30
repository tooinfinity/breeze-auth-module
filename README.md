# breeze-auth-module
to install this package run
```
composer require tooinfinity/auth-module:dev-main --dev
```

after that run this commands

```
php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
```

```
composer dump-autoload
```

```
php artisan module:make Auth
```
```
php artisan module:make-model User Auth
```

to create auth API run this 

```
php artisan Auth-module:install module-api
```
to create auth blade run this 

```
php artisan Auth-module:install
```
