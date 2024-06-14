<?php
declare(strict_types=1);

namespace Tests\Presentation\Auth;

use App\Data\Protocols\Auth\LoginServiceInterface;
use App\Domain\Dto\Credentials;
use App\Presentation\Actions\Protocols\ActionError;
use App\Presentation\Actions\Protocols\ActionPayload;
use App\Presentation\Actions\Protocols\ErrorsEnum;
use App\Presentation\Helpers\Validation\Validators\Interfaces\ValidationInterface;
use DI\Container;
use Mockery\MockInterface;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->app = $this->getAppInstance();
    $service = createMockService();
    $this->autowireContainer(LoginServiceInterface::class, $service);
    $validator = createValidatorService();
    $this->autowireContainer(ValidationInterface::class, $validator);
    $this->endpoint = '/auth/login';
});

test('should call authentication with correct values', function () {
    /** @var Container $container */
    $container = $this->getContainer();
    $service = createMockService();
    $service->expects('auth')
        ->andReturn(makeCredentials());
    $container->set(LoginServiceInterface::class, $service);
    $credentials = new Credentials(access:'any_mail@gmail.com', password: 'Password04');
    $request = $this->createRequest('POST', $this->endpoint);
    $request->getBody()
        ->write(json_encode($credentials));
    $request->getBody()
        ->rewind();
    $this
        ->app
        ->handle($request);
});
test('should return422 if validation returns error', function () {
    $app = $this->app;
    $this->setUpErrorHandler($app);
    $body = new Credentials('mike@gmail.com', 'pass');
    $request = $this->createJsonRequest('POST', $this->endpoint, $body->jsonSerialize());

    $response = $app->handle($request);

    $payload = (string) $response->getBody();
    $expectedError = new ActionError(ErrorsEnum::UNPROCESSABLE_ENTITY->value, '[password]: Password wrong my dude');
    $expectedPayload = new ActionPayload(statusCode: 422, error: $expectedError);
    $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

    expect($payload)->toEqual($serializedPayload);

    assertEquals($response->getStatusCode(), 422);
});
test('expects two errors', function () {
    $app = $this->app;
    $this->setUpErrorHandler($app);
    $request = $this->constructPostRequest(new Credentials('GABRI@MAIL', 'pass'), 'POST', $this->endpoint);

    $response = $app->handle($request);

    $payload = (string) $response->getBody();
    $payloadDecoded = json_decode($payload);

    $errors = explode("\n", $payloadDecoded
        ->error
        ->description);

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
