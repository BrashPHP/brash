<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Domain\Exceptions\UserNotFoundException;
use App\Domain\Models\User;
use App\Domain\Repositories\UserRepository;
use Brash\Framework\Http\Domain\ActionPayload;
use Brash\Framework\Http\Errors\ActionError;
use Brash\Framework\Http\Errors\ErrorsEnum;
use DI\Container;
use Tests\Traits\App\InstanceManager;

test('action', function () {
    $instanceApp = new InstanceManager;
    /** @var Container $container */
    $container = $instanceApp->getContainer(true);

    $user = new User(1, 'bill.gates', 'Bill', 'Gates');

    /** @var \Mockery\MockInterface */
    $repository = mock(UserRepository::class);
    $repository->expects('findUserOfId')->andReturn($user);

    $app = $instanceApp->getAppInstance();

    $container->set(UserRepository::class, $repository);

    $request = $this->createRequest('GET', '/users/1');
    $response = $app->handle($request);

    $payload = (string) $response->getBody();
    $expectedPayload = new ActionPayload(200, $user);
    $serializedPayload = json_encode($expectedPayload);

    expect($payload)->toEqual($serializedPayload);
});

test('action throws user not found exception', function () {
    $instanceApp = new InstanceManager;
    /** @var Container $container */
    $container = $instanceApp->getContainer(true);
    $app = $instanceApp->getAppInstance();

    $userRepositoryProphecy = $this->getMockBuilder(UserRepository::class)->getMock();
    $userRepositoryProphecy->method('findUserOfId')->willThrowException(new UserNotFoundException)->with(1);
    $userRepositoryProphecy->expects($this->once())->method('findUserOfId');

    $container->set(UserRepository::class, $userRepositoryProphecy);

    $request = $this->createRequest('GET', '/users/1');
    $response = $app->handle($request);

    $payload = (string) $response->getBody();
    $expectedError = new ActionError(ErrorsEnum::RESOURCE_NOT_FOUND, 'The user you requested does not exist.');
    $expectedPayload = new ActionPayload(404, null, $expectedError);
    $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

    expect($payload)->toEqual($serializedPayload);
});
