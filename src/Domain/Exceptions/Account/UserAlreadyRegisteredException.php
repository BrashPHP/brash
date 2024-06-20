<?php

namespace App\Domain\Exceptions\Account;

use App\Domain\Exceptions\Protocols\HttpSpecializedAdapter;
use Core\Http\Errors\HttpExceptionAdapter;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpForbiddenException;
use Psr\Http\Message\ServerRequestInterface;

class UserAlreadyRegisteredException extends HttpExceptionAdapter
{
    private string $responsaMessage = 'O nome de usuário ou o email escolhido já foi utilizado';

    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        return new HttpForbiddenException($request, $this->responsaMessage);
    }
}
