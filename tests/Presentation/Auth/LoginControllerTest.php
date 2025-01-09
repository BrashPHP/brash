<?php

declare(strict_types=1);

namespace Tests\Presentation\Auth;

use App\Data\Protocols\Auth\LoginServiceInterface;
use App\Domain\Dto\Credentials;
use App\Domain\Dto\TokenLoginResponse;
use App\Presentation\Actions\Auth\LoginController;
use Core\Http\Domain\ActionPayload;
use Core\Http\Errors\ActionError;
use Core\Http\Errors\ErrorsEnum;
use Core\Validation\Interfaces\ValidationInterface;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->app = $this->getAppInstance();
    $this->endpoint = '/login';
});

test('should call authentication with correct values', function () {
    $app = $this->getAppInstance();

    $service = createMockService();

    $service->shouldReceive('auth')
        ->once()
        ->andReturn(new TokenLoginResponse('', ''));
    /** @var MockInterface|LoggerInterface */
    $logger = mock(LoggerInterface::class);
    $logger->shouldReceive('info')->andReturn(null);
    $this->getContainer()->set(LoginServiceInterface::class, $service);
    $this->getContainer()->set(
        LoginController::class,
        new LoginController(
            $service,
            $logger
        )
    );

    $credentials = new Credentials(access: 'any_mail@gmail.com', password: 'Password04');
    $request = $this->createRequest('POST', '/login');
    $request->getBody()
        ->write(json_encode($credentials));
    $request->getBody()
        ->rewind();
    $app->handle($request);
});

test('should return 422 if validation returns error', function () {
    $app = $this->app;
    $body = new Credentials('mike@gmail.com', 'pass');
    $request = $this->createJsonRequest('POST', $this->endpoint, $body->jsonSerialize());

    $response = $app->handle($request);

    $payload = (string) $response->getBody();
    $expectedError = new ActionError(ErrorsEnum::UNPROCESSABLE_ENTITY, '[{"password":"Password wrong my dude"}]');
    $expectedPayload = new ActionPayload(statusCode: 422, error: $expectedError);
    $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

    expect($payload)->toEqual($serializedPayload);

    expect($response->getStatusCode())->toEqual(422);
});

test('expects two errors', function () {
    $app = $this->app;
    $request = $this->constructPostRequest(new Credentials('GABRI@MAIL', 'pass'), 'POST', $this->endpoint);

    $response = $app->handle($request);

    $payload = (string) $response->getBody();
    $payloadDecoded = json_decode($payload);

    $errors = json_decode(
        $payloadDecoded
            ->error
            ->description
    );

    expect(count($errors))->toEqual(2);
})->group('ignore');
function makeCredentials(): Credentials
{
    return new Credentials('any_mail@gmail.com', 'Password04');
}

function createValidatorService(): ValidationInterface|MockInterface
{
    return mock(ValidationInterface::class);
}

function createMockService(): LoginServiceInterface|MockInterface
{
    return mock(LoginServiceInterface::class);
}
