<?php

namespace App\Core\Kernel\Http;

use App\Core\Kernel\Http\Exception\HttpStatusCodeException;
use App\Core\Router\ArgumentResolver\ArgumentResolver;
use App\Core\Router\RouterInterface;
use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HttpKernel implements
    RequestHandlerInterface
{
    public function __construct(
        protected string $environment,
        protected ContainerInterface $controllerLocator,
        protected RouterInterface $router,
        protected ArgumentResolver $argumentResolver
    ) {
    }

    public function handle(
        ServerRequestInterface $request
    ): ResponseInterface {
        $route = $this->router->match(
            $request
        );

        try {
            if ($route === null) {
                throw new HttpStatusCodeException(
                    statusCode: 404,
                    message: 'No route matches the given URL'
                );
            }

            $controllerClass = $route->getControllerReference()->getClass();
            $controllerMethod = $route->getControllerReference()->getMethod();

            $controller = $this->controllerLocator->get($controllerClass);

            $arguments = $this->argumentResolver->resolveArguments(
                request: $request,
                route: $route
            );

            $request = $request->withAttribute(
                'arguments',
                $arguments
            );

            $response = $controller->$controllerMethod(
                $request
            );
        } catch (\Throwable $e) {

            if ($_ENV['APP_ENV'] === 'prod') {

                $statusCode = $e instanceof HttpStatusCodeException ? $e->getStatusCode() : 500;

                $message = $statusCode === 404 ? 'Page not found' : ($e instanceof HttpStatusCodeException ? $e->getMessage() : 'Internal Server Error');

                return new Response(
                    status: $statusCode,
                    body: $message
                );
            } else {
                // @todo: log, handle properly
                throw $e;
            }
        }

        return $response;
    }

}
