<?php

use App\Core\Persistence\Database\Resetter\DatabaseResetterInterface;
use Psr\Container\ContainerInterface;

require __DIR__ . '/../config/bootstrap.php';

/** @var ContainerInterface $sc */
$sc = (require __DIR__ . '/../config/services.php')();

/** @var DatabaseResetterInterface $dbResetter */
$dbResetter = $sc->get(DatabaseResetterInterface::class);
$dbResetter->reset();
