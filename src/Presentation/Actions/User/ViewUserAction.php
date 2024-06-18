<?php

declare(strict_types=1);

namespace App\Presentation\Actions\User;

use App\Presentation\Actions\ActionGroups\UsersEntrypoint;
use Core\Http\Attributes\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


#[Route(path: '/{id}', method: 'GET', group: UsersEntrypoint::class)]
class ViewUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        $userId = (int) $this->resolveArg('id');
        $user = $this->userService->findUserOfId($userId);

        $this->logger->info(sprintf('User of id `%d` was viewed.', $userId));

        return $this->respondWithData($user);
    }
}
