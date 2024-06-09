<?php

declare(strict_types=1);

namespace App\Presentation\Actions\User;

use Core\Attributes\Routing\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

#[Route(path: '/users', method: 'GET')]
class ListUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        $users = $this->userService->findAll();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}
