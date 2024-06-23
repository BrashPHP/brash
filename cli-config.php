<?php

// cli-config.php
declare(strict_types=1);


use Core\Http\Factories\ContainerFactory;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface as EntityManager;

require_once __DIR__ . '/vendor/autoload.php';

$containerFactory = new ContainerFactory();

$container = $containerFactory->get();

$config = new PhpFile('./doctrine-migrations.php');

$em = $container->get(EntityManager::class);

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($em));
