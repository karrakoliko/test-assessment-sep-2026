<?php

use Smarty\Smarty;

require_once '../vendor/autoload.php';

$container = new \Symfony\Component\DependencyInjection\ContainerBuilder();

$container
    ->register('smarty', Smarty::class)
    ->addMethodCall('setTemplateDir', [__DIR__.'/../templates'])
    ->addMethodCall('setConfigDir', [__DIR__.'/../config/smarty'])
    ->addMethodCall('setCompileDir', [sys_get_temp_dir()])
    ->addMethodCall('setCacheDir', [sys_get_temp_dir()])
    ->addMethodCall('setEscapeHtml', [true])
    ->setPublic(true);

$container->compile();

return $container;
