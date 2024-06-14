<?php

declare(strict_types=1);
use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use \Tests\Domain\UseCases\Markers\Store\SutTypes;
use App\Domain\Dto\Credentials;
use App\Domain\Models\Marker\Marker;
use App\Domain\Models\Museum;
use App\Domain\Repositories\MarkerRepositoryInterface;
use App\Domain\Repositories\MuseumRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use PHPUnit\Framework\MockObject\MockObject;

beforeEach(function () {
    $this->sut = new SutTypes(
        mockMuseumRepository(),
        mockMarkerRepository(),
        mockEntityManager()
    );
});

function makeCredentials()
{
    return new Credentials(access: '@mail.com', password: 'password');
}
function mockEntityManager(): EntityManagerInterface|MockInterface
{
    return mock(EntityManager::class);
}

function mockMuseumRepository(): MuseumRepository|MockInterface
{
    return mock(MuseumRepository::class);
}
/** @return MockObject */
function mockMarkerRepository(): MarkerRepositoryInterface|MockInterface
{
    return mock(MarkerRepositoryInterface::class);
}

test('should pass when service is called', function () {
    $service = $this->sut->service;
    $conn = mock(Connection::class);
    $conn->shouldReceive("beginTransaction")->once()->andReturn(true);
    $conn->shouldReceive("commit")->once()->andReturn(true);
    
    $this->sut->em->expects('getConnection')->twice()->andReturn($conn);

    $this->sut->museumRepository
        ->expects('findByID')
        ->once()
        ->with(13)
        ->andReturn(new Museum(1, email: 'email', name: 'name'));

    /** @var MockInterface */
    $mockMarkerRepository = $this->sut->markerRepositoryInterface;
    $mockMarkerRepository->expects('add')
        ->once();

    $service->insert(
        13,
        new Marker(
            null,
            null,
            "name",
            "text",
            "title",
        )
    );
});
