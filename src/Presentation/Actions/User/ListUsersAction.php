<?php

declare(strict_types=1);

namespace App\Presentation\Actions\User;

use App\Presentation\Actions\ActionGroups\UsersEntrypoint;
use Core\Http\Attributes\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use React\Promise\Promise;

#[Route(path: '/', method: 'GET', group: UsersEntrypoint::class)]
class ListUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response|Promise
    {
        return new Promise(
            function (\Closure $resolve) {
                $users = $this->userService->findAll();

                $this->logger->info("Users list was viewed.");

                $resolve($this->respondWithData($users));
            }
        );

    }
}
