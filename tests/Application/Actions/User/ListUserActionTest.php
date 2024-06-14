<?php

use App\Domain\Models\User;
use App\Domain\Repositories\UserRepository;
use App\Presentation\Actions\Protocols\ActionPayload;
use DI\Container;
use Tests\Traits\App\InstanceManager;
use Tests\Traits\App\RequestManager;

beforeEach(function () {
    $instanceApp = new InstanceManager();
    $this->app = $instanceApp->createAppInstance();
});

test('should call action successfully', function () {
    /** @var Container $container */
    $container = $this->app->getContainer();

    $user = new User(1, 'bill.gates', 'Bill', 'Gates');

    /** @var \Mockery\MockInterface|UserRepository */
    $userRepositoryProphecy = mock(UserRepository::class);
    $userRepositoryProphecy->shouldReceive('findAll')->once()->andReturn([$user]);

    $container->set(UserRepository::class, $userRepositoryProphecy);
    $request = new RequestManager();
    $request = $request->createRequest('GET', '/users');
    $response = $this->app->handle($request);

    $payload = (string) $response->getBody();
    $expectedPayload = new ActionPayload(200, [$user]);
    $serializedPayload = json_encode($expectedPayload);

    expect($payload)->toEqual($serializedPayload);
});
