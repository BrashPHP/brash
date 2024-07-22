#!/usr/bin/env php
<?php

use App\Application\Providers\DependenciesProvider;
use App\Application\Providers\SettingsProvider;
use Core\Data\Doctrine\EntityManagerBuilder;
use Core\Http\Factories\ContainerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

// replace with path to your own project bootstrap file
require_once __DIR__ . '/../vendor/autoload.php';

$containerFactory = new ContainerFactory();
$containerFactory->addProviders(SettingsProvider::class, DependenciesProvider::class);
$container = $containerFactory->get();
$entityManager = $container->get(EntityManagerInterface::class);

$commands = [
    // If you want to add your own custom console commands,
    // you can do so here.
];

ConsoleRunner::run(
    new SingleManagerProvider($entityManager),
    $commands
);