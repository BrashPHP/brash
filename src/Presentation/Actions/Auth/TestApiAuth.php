<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Auth;

use App\Data\Entities\Doctrine\DoctrineAccount;
use App\Domain\Repositories\AccountRepository;
use App\Presentation\Actions\Auth\Utilities\CookieTokenManager;
use Core\Http\Action;
use Core\Http\Attributes\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;



#[Route(path: "/api/test-auth", method: "GET")]
class TestApiAuth extends Action
{
    public function action(Request $request): Response
    {
        $response = $this->response;

        $response->getBody()->write('Works');

        return $response;
    }
}
