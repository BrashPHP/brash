<?php

declare(strict_types=1);

namespace Core\Http\Middlewares;

use Core\Http\Exceptions\HttpBadRequestException;
use Core\Http\Exceptions\UnprocessableEntityException;
use Core\Http\Interfaces\ValidationInterface;
use Core\Validation\Facade\ValidationFacade;
use Core\Validation\ValidationExceptions\ValidationError;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private ValidationInterface $validationInterface) {}

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
