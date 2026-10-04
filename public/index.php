<?php

use App\Core\Kernel\Http\HttpKernel;
use App\Core\Kernel\Http\ServerRequestFactory;
use App\Core\Router\ArgumentResolver\ArgumentResolverInterface;
use App\Core\Router\RouterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Container\ContainerInterface;

require __DIR__ . '/../config/bootstrap.php';

/** @var ContainerInterface $sc */
$sc = (require __DIR__ . '/../config/services.php')();

$request = ServerRequestFactory::fromGlobals();

$kernel = new HttpKernel(
    environment: $_ENV['APP_ENV'],
    controllerLocator: $sc->get('app.core.kernel.controller_locator'),
    router: $sc->get(RouterInterface::class),
    argumentResolver: $sc->get(ArgumentResolverInterface::class)
);

$response = $kernel->handle($request);

$emitter = new SapiEmitter();
$emitter->emit($response);
