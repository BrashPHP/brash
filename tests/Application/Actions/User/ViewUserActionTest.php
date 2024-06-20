<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Domain\Exceptions\UserNotFoundException;
use App\Domain\Models\User;
use App\Domain\Repositories\UserRepository;
use Core\Http\Domain\ActionPayload;
use Core\Http\Errors\ActionError;
use Core\Http\Errors\ErrorsEnum;
use DI\Container;
use Tests\Traits\App\InstanceManager;

beforeEach(function () {
    $instanceApp = new InstanceManager();
    $this->app = $instanceApp->createAppInstance();
});

test('action', function () {
    $app = $this->app;

    /** @var Container $container */
    $container = $app->getContainer();

    $user = new User(1, 'bill.gates', 'Bill', 'Gates');

    $userRepositoryProphecy = $this->getMockBuilder(UserRepository::class)->getMock();
    $userRepositoryProphecy->method('findUserOfId')->willReturn($user)->with(1);
    $userRepositoryProphecy->expects($this->once())->method('findUserOfId');

    $container->set(UserRepository::class, $userRepositoryProphecy);

    $request = $this->createRequest('GET', '/users/1');
    $response = $app->handle($request);

    $payload = (string) $response->getBody();
    $expectedPayload = new ActionPayload(200, $user);
    $serializedPayload = json_encode($expectedPayload);

    expect($payload)->toEqual($serializedPayload);
});

test('action throws user not found exception', function () {
    $app = $this->createAppInstance();

    $this->setUpErrorHandler($app);

    /** @var Container $container */
    $container = $app->getContainer();

    $userRepositoryProphecy = $this->getMockBuilder(UserRepository::class)->getMock();
    $userRepositoryProphecy->method('findUserOfId')->willThrowException(new UserNotFoundException())->with(1);
    $userRepositoryProphecy->expects($this->once())->method('findUserOfId');

    $container->set(UserRepository::class, $userRepositoryProphecy);

    $request = $this->createRequest('GET', '/users/1');
    $response = $app->handle($request);

    $payload = (string) $response->getBody();
    $expectedError = new ActionError(ErrorsEnum::RESOURCE_NOT_FOUND->value, 'The user you requested does not exist.');
    $expectedPayload = new ActionPayload(404, null, $expectedError);
    $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

    expect($payload)->toEqual($serializedPayload);
});
