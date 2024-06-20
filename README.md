# ARTchie's Backoffice API

[//]: # [![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim-Skeleton/badge.svg?branch=master)](https://coveralls.io/github/slimphp/Slim-Skeleton?branch=master)

Backoffice API made in Slim framework using PHP 8.

## Install the Application

- Install dependencies via composer (composer install).

Check out the scripts defined at the composer.json file to start the application.

You can use also use `docker-compose` to run the app with `docker`, so you can run these commands:

```bash
cd [my-app-name]
docker-compose up -d
```

After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

## TODO

### ROADMAP

[] Incluir testes para camada HTTP do CORE
    [] RouteCollector
    [] RouteGroup
[] Server.php
    [] Criar hot reload com server.php
    [] Utilizar entrypoint docker via server.php
    [] Criar watchmode local via server.php