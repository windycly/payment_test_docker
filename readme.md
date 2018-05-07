# Environment

- Docker
- Nginx
- PHP
- MySQL
- Redis
- Laravel PHP Framework

## Setup the project

Copy .env.example as new file named .env

## Start the project

```shell
# php composer.phar install --dev
# docker-compose up -d
# docker ps
# docker-compose exec app php artisan key:generate
# docker-compose exec app php artisan optimize
# docker-compose exec app php artisan migrate
```

Access http://localhost:8080/ to make payment

Access http://localhost:8080/check to check payment

## Documentation

Documentation for Docker can be found on the [Docker website](https://docs.docker.com/install/).

Documentation for PHP can be found on the [PHP website](http://php.net/manual/en/install.php).

Documentation for MySQL can be found on the [MySQL website](https://dev.mysql.com/downloads/installer/).

Documentation for Laravel framework can be found on the [Laravel website](http://laravel.com/docs).
