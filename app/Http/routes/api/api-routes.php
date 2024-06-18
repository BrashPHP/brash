<?php

namespace Core\Http\Routing\Subs\Api;

use App\Presentation\Actions\FileUpload\UploadAction;
use App\Presentation\Actions\Home\HomeController;
use App\Presentation\Actions\ResourcesSecurity\KeyCreatorAction;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (Group $group) {
    $group->get('/', HomeController::class);
    
    $group->get(
        '/test-auth', function (RequestInterface $request, ResponseInterface $response): ResponseInterface {
            $response->getBody()->write('Works');

            return $response;
        }
    );

    $group->group('/museum', include __DIR__ . '/museum/museum-routes.php');

    $group->group('/marker', include __DIR__ . '/marker/marker-routes.php');

    $group->post('/upload-file', UploadAction::class);

    $group->post('/create-app-key', KeyCreatorAction::class);
};
