<?php

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $sc */
$sc = require '../src/ServiceContainer.php';

$sc->get('smarty')->display('index.tpl');