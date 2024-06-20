<?php

namespace App\Domain\Exceptions\Account;

use App\Domain\Exceptions\Protocols\HttpSpecializedAdapter;
use App\Domain\Exceptions\Protocols\HttpSpecializedAdapterCustom;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpForbiddenException;
use Psr\Http\Message\ServerRequestInterface;

class UserAlreadyRegisteredException extends HttpSpecializedAdapterCustom
{
    private string $responsaMessage = 'O nome de usuário ou o email escolhido já foi utilizado';

    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        return new HttpForbiddenException($request, $this->responsaMessage);
    }
}
