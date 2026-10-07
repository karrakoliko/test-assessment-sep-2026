<?php

use Psr\Container\ContainerInterface;

require __DIR__ . '/../config/bootstrap.php';

/** @var ContainerInterface $sc */
$sc = (require __DIR__ . '/../config/services.php')();

/** @var \App\App\Service\Seeder\SeederInterface $seeder */
$seeder = $sc->get(\App\App\Service\Seeder\ChunkedSeeder::class);

/** @var \Doctrine\DBAL\Connection $conn */
$conn = $sc->get(\Doctrine\DBAL\Connection::class);

$conn->transactional(function () use ($seeder) {
    $seeder->seedCategories(100);
    $seeder->seedArticlesAmongExistingCategoriesRandomly(300);
});
