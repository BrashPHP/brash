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
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator;

#[Route(path: "list-all", method: "GET")]
class ListAllUsers extends Action
{
    public function __construct(
        private AccountRepository $accountRepository,
        protected LoggerInterface $logger
    ) {
        $this->cookieManager = new CookieTokenManager();
    }

    public function action(Request $request): Response
    {
        $accounts = $this->accountRepository->listAll();
        $mappedAccounts = array_map(fn(DoctrineAccount $docctrineAccount) => $docctrineAccount->toModel(), $accounts);
        
        return $this->respondWithData($mappedAccounts);
    }
}
