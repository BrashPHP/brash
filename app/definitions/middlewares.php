<?php

use App\Presentation\Middleware\JWTAuthMiddleware;
use App\Presentation\Middleware\SessionMiddleware;
use App\Presentation\Middleware\ResponseAdapterMiddleware;
use Slim\Middleware\BodyParsingMiddleware;

return [
    SessionMiddleware::class,
    JWTAuthMiddleware::class,
    BodyParsingMiddleware::class,
    ResponseAdapterMiddleware::class,
    //ErrorMiddleware::class
];