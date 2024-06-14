<?php

declare(strict_types=1);
use App\Data\Entities\Doctrine\DoctrineAccount;
use App\Domain\Dto\AccountDto;
use App\Domain\Models\Account;
use App\Domain\Repositories\AccountRepository;
use Doctrine\ORM\EntityManagerInterface as EntityManager;

use function PHPUnit\Framework\assertInstanceOf;

beforeAll(function () {
    putenv('RR=');
    self::createDatabaseDoctrine();
});

afterAll(function () {
    self::truncateDatabaseDoctrine();
});

beforeEach(function () {
    $this->getAppInstance();
    $container = $this->getContainer();
    $this->repository = $container->get(AccountRepository::class);
    $this->entityManager = $container->get(EntityManager::class);
});

afterEach(function () {
    $entityManager = $this->entityManager;
    $collection = $entityManager->getRepository(DoctrineAccount::class)->findAll();
    foreach ($collection as $c) {
        $entityManager->remove($c);
    }
    $entityManager->flush();
    $entityManager->clear();
});

test('should insert account', function () {
    $account = new AccountDto(email: 'mail.com', username: 'user', password: 'pass');
    $this->repository->insert($account);
    $total = getTotalCount($this->entityManager);

    expect(1)->toEqual($total);
})->group('roadrunner');

test('should retrieve account', function () {
    $account = new AccountDto(email: 'mail.com', username: 'user', password: 'pass');
    $this->repository->insert($account);

    $account = $this->repository->findByMail('mail.com');

    assertInstanceOf(Account::class, $account);
})->group('roadrunner');

function getTotalCount(EntityManager $entityManager): int
{
    $qb = $entityManager->createQueryBuilder();

    $qb->select($qb->expr()->count('u'))
        ->from(DoctrineAccount::class, 'u')
        // ->where('u.type = ?1')
        // ->setParameter(1, 'employee')
    ;

    $query = $qb->getQuery();

    return (int) $query->getSingleScalarResult();
}
