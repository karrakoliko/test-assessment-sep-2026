<?php

namespace App\Core\Kernel\Http;

use App\Core\Router\ArgumentResolver\ArgumentResolver;
use App\Core\Router\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class HttpKernel implements RequestHandlerInterface
{
    public function __construct(
        protected string $environment,
        protected ContainerInterface $controllerLocator,
        protected RouterInterface $router,
        protected ArgumentResolver $argumentResolver
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $route = $this->router->match($request);

        if ($route === null) {
            throw new \InvalidArgumentException('No route matches the given URL');
        }

        $controllerClass = $route->getControllerReference()->getClass();
        $controllerMethod = $route->getControllerReference()->getMethod();

        try {

            $controller = $this->controllerLocator->get($controllerClass);
        } catch (ServiceNotFoundException $e) {
            throw new \LogicException(message: 'Invalid controller class', previous: $e);
        }

        $arguments = $this->argumentResolver->resolveArguments(request: $request, route: $route);

        $request = $request->withAttribute('arguments', $arguments);

        return $controller->$controllerMethod($request);

    }

}
