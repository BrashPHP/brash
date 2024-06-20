<?php

declare(strict_types=1);

namespace App\Data\UseCases\Authentication\Errors;

use App\Domain\Exceptions\Protocols\HttpSpecializedAdapterCustom;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;


class IncorrectPasswordException extends HttpSpecializedAdapterCustom
{
    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        return new HttpBadRequestException($request, "The passwords don't match");
    }
}
