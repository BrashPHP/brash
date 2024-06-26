<?php

declare(strict_types=1);
use App\Data\Entities\Doctrine\DoctrineMarker;
use App\Data\Entities\Doctrine\DoctrineMarkerAsset;
use App\Data\Entities\Doctrine\DoctrinePlacementObject;
use App\Domain\Models\Assets\PictureAsset;
use App\Domain\Models\Marker\Marker;
use App\Domain\Models\PlacementObject\PlacementObject;
use App\Domain\Repositories\MarkerRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use function PHPUnit\Framework\assertInstanceOf;

$markerTitle = 'Boy with apple.png';
dataset('assetProvider', function () {
    return [
        ['Boy with apple.png', 'The boy with an apple is a famous portrait of a boy with an apple', 'Boy with an apple'],
    ];
});

beforeAll(function () {
    putenv('RR=');
    self::createDatabaseDoctrine();
});

afterAll(function () {
    self::truncateDatabase();
});

beforeEach(function () {
    $this->getAppInstance();
    $container = $this->getContainer();
    $this->repository = $container->get(MarkerRepositoryInterface::class);
    $this->entityManager = $container->get(EntityManager::class);
});

afterEach(function () {
    $entityManager = $this->entityManager;
    $collection = $entityManager->getRepository(DoctrineMarker::class)->findAll();
    foreach ($collection as $c) {
        $entityManager->remove($c);
    }
    $entityManager->flush();
    $entityManager->clear();
});

test('should insert marker', function ($markerTitle, $markerDescription, $markerRealTitle) {
    $marker = new Marker(
        null,
        null,
        $markerTitle,
        $markerDescription,
        $markerRealTitle
    );

    $this->repository->add($marker);
    $total = getTotalCount($this->entityManager);

    expect(1)->toEqual($total);
})->with('assetProvider');

test('should retrieve marker', function ($markerTitle, $markerDescription, $markerRealTitle) {
    $marker = new Marker(
        null,
        null,
        $markerTitle,
        $markerDescription,
        $markerRealTitle
    );

    $this->repository->add($marker);

    $new_marker = $this->entityManager->getRepository(DoctrineMarker::class)->findAll()[0];

    assertInstanceOf(DoctrineMarker::class, $new_marker);
})->with('assetProvider');

test('should insert marker with asset', function ($markerTitle, $markerDescription, $markerRealTitle) {
    $asset = new PictureAsset();
    $asset->setFileName('boyapple.png');
    $asset->setPath('domain/path/boyaple.png');
    $asset->setUrl('www.name.com');
    $asset->setOriginalName('boyapp.png');
    $asset->setMimeType('file/png');
    $marker = new Marker(
        null,
        null,
        $markerTitle,
        $markerDescription,
        $markerRealTitle,
        $asset
    );

    $this->repository->add($marker);

    $new_marker = $this->entityManager->getRepository(DoctrineMarker::class)->findBy([], ['id' => 'DESC'], 1, 0)[0];

    $new_asset = $new_marker->getAsset();

    assertInstanceOf(DoctrineMarker::class, $new_marker);
    assertInstanceOf(DoctrineMarkerAsset::class, $new_asset);
})->with('assetProvider');

test('should insert marker with resources', function ($markerTitle, $markerDescription, $markerRealTitle) {
    $marker = new Marker(
        null,
        null,
        $markerTitle,
        $markerDescription,
        $markerRealTitle
    );

    $placementObject = new PlacementObject(null, 'Object to place over pictyre', null);

    $marker->addResource($placementObject);

    $this->repository->add($marker);

    $new_marker = $this->entityManager->getRepository(DoctrineMarker::class)->findBy([], ['id' => 'DESC'], 1, 0)[0];

    $resources = $new_marker->getResources();

    expect(1)->toEqual($resources->count());
    $resource = $resources->get(0);
    assertInstanceOf(DoctrinePlacementObject::class, $resource);
})->with('assetProvider');

function getTotalCount(EntityManager $entityManager): int
{
    $qb = $entityManager->createQueryBuilder();

    $qb->select($qb->expr()->count('u'))
        ->from(DoctrineMarker::class, 'u')
        // ->where('u.type = ?1')
        // ->setParameter(1, 'employee')
    ;

    $query = $qb->getQuery();

    return (int) $query->getSingleScalarResult();
}
