<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface as EntityManager;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertDirectoryExists;
use function PHPUnit\Framework\assertIsObject;

test('set environment correctly', function () {
    $dir = getcwd();
    assertDirectoryExists($dir.'/src/Domain/Models');
});

test('if setup container works', function () {
    $container = $this->getContainer();
    $doctrine = $container->get('doctrine');

    assertArrayHasKey('connection', $doctrine);
});

test('if entity manager is not null', function () {
    $container = $this->getContainer();
    $em = $container->get(EntityManager::class);

    assertIsObject($em);
});
