<?php

namespace Tests\Application\Actions\User;

use App\Domain\Models\User;
use App\Domain\Repositories\UserRepository;
use App\Presentation\Actions\Protocols\ActionPayload;
use DI\Container;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ListUserActionTest extends TestCase
{

    public function testShouldCallActionSuccessfully()
    {
        $app = $this->createAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $user = new User(1, 'bill.gates', 'Bill', 'Gates');

        $userRepositoryProphecy = $this->getMockBuilder(UserRepository::class)->getMock();
        $userRepositoryProphecy->method('findAll')->willReturn([$user]);
        $userRepositoryProphecy->expects($this->once())->method('findAll');

        $container->set(UserRepository::class, $userRepositoryProphecy);

        $request = $this->createRequest('GET', '/users');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, [$user]);
        $serializedPayload = json_encode($expectedPayload);

        $this->assertEquals($serializedPayload, $payload);
    }
}