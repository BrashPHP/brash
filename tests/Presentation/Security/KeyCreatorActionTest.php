<?php

declare(strict_types=1);
use App\Data\Protocols\AsymCrypto\SignerInterface;
use App\Presentation\Actions\ResourcesSecurity\KeyCreatorAction;
use Core\Http\Exceptions\UnprocessableEntityException;
use Mockery\MockInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

beforeEach(function () {
    $service = createMockService();
    $this->sut = new KeyCreatorAction($service);
});

test('if uuid is valid', function () {
    $this->expectException(UnprocessableEntityException::class);

    $this->sut->__invoke($this->createRequest('POST', '/'), new Response(), []);
});

test('should call asymmetric signer with correct values', function () {
    $prophecyService = createMockService();
    $prophecyService
        ->expects('sign')
        ->once()
        ->with('914e4c51-a049-4594-ae5c-921bbadf686b')
        ->andReturn('');
    $action = new KeyCreatorAction($prophecyService);
    $response = $action(createMockRequest($this), new Response(), []);
    $payload = (string) $response->getBody();
    expect(is_string($payload))->toBeTrue();
});

test('should return200 with correct input', function () {
    $service = createMockService();
    $testString = base64_encode('expectedString');
    $service->expects('sign')->andReturn($testString);

    $serviceMocked = $service;
    $action = new KeyCreatorAction($serviceMocked);
    $response = $action->__invoke(createMockRequest($this), new Response(), []);
    $decoded = json_decode((string) $response->getBody());
    expect($decoded->statusCode)->toBe(200);
    expect($decoded->data->token)->toBe($testString);
});

function createMockRequest(\Tests\TestCase $app): ServerRequestInterface
{
    $request = $app->createRequest('POST', '/api/forge-credential');

    $request->getBody()
        ->write(
            json_encode(
                [
                    'uuid' => '914e4c51-a049-4594-ae5c-921bbadf686b'
                ],
                JSON_PRETTY_PRINT
            )
        )
    ;

    $request->getBody()->rewind();

    return $request;
}

function createMockService(): MockInterface|SignerInterface
{
    return mock(SignerInterface::class);
}
