<?php

include_once __DIR__ . '/../vendor/autoload.php';

$dotEnv = (new \Symfony\Component\Dotenv\Dotenv());
$dotEnv->bootEnv(
    __DIR__ . '/../.env'
);
