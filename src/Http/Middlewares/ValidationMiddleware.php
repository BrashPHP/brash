<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Middlewares;

use Brash\Framework\Http\Exceptions\HttpBadRequestException;
use Brash\Framework\Http\Exceptions\UnprocessableEntityException;
use Brash\Framework\Http\Interfaces\ValidationInterface;
use Brash\Framework\Validation\Facade\ValidationFacade;
use Brash\Framework\Validation\ValidationExceptions\ValidationError;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ValidationInterface $validationInterface) {}

    public function process(Request $request, RequestHandler $handler): Response
    {
        $parsedBody = $request->getParsedBody();

        if (is_null($parsedBody)) {
            throw new HttpBadRequestException($request);
        }

        $rules = $this->validationInterface->rules($request) ?? [];
        $messages = $this->validationInterface->messages() ?? [];

        $validationFacade = new ValidationFacade($rules, $messages);
        $validator = $validationFacade->createValidations();
        $result = $validator->validate($parsedBody);

        if ($result instanceof ValidationError) {
            throw new UnprocessableEntityException($request, $result->getMessage(), $result);
        }

        return $handler->handle($request);
    }
}
