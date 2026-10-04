<?php

namespace App\Core\Router\Route;

use App\Core\Router\ControllerReferenceInterface;
use App\Core\Router\Route\Argument\RouteArguments;
use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    public function getMask(): string;

    public function getControllerReference(): ControllerReferenceInterface;
    public function matches(ServerRequestInterface $request): bool;
    public function getArguments(): RouteArguments;
}
