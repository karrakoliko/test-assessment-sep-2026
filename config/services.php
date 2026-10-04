<?php

use App\App\Controller\CategoryController;
use App\App\Controller\IndexController;
use App\App\Controller\HomeController;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\Core\Di\ControllerLocatorPass;
use App\Core\Persistence\Database\Resetter\DatabaseResetter;
use App\Core\Persistence\Database\Resetter\DatabaseResetterInterface;
use App\Core\Router\ArgumentResolver\ArgumentResolver;
use App\Core\Router\ArgumentResolver\ArgumentResolverInterface;
use App\Core\Router\ControllerReference;
use App\Core\Router\Router;
use App\Core\Router\Route\Argument\RouteArgument;
use App\Core\Router\Route\Argument\RouteArguments;
use App\Core\Router\Route\Route;
use App\Core\Router\RouterInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Smarty\Smarty;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ServiceLocator;

return static function (): ContainerBuilder {

    $container = new ContainerBuilder();

    // DBAL
    $dsnParser = new DsnParser();
    $connectionParams = $dsnParser->parse($_ENV['DATABASE_URL']);

    $container
        ->register(ArgumentResolverInterface::class, ArgumentResolver::class)
        ->setPublic(true)
    ;

    $container
        ->register(
            RouterInterface::class,
            Router::class
        )
        ->setArgument('$routes', [
            new Route(
                mask: '/',
                controllerReference: new ControllerReference(
                    class: HomeController::class,
                    method: 'index'
                )
            ),
            new Route(
                mask: '/category/{id}',
                controllerReference: new ControllerReference(
                    class: CategoryController::class,
                    method: 'show'
                ),
                arguments: new RouteArguments(
                    new RouteArgument('id', '/^\d+$/')
                )
            )
        ])
        ->setPublic(true)
    ;

    $container
        ->register(Connection::class)
        ->setFactory([DriverManager::class, 'getConnection'])
        ->setArguments([$connectionParams])
        ->setPublic(true);

    $container
        ->register(Smarty::class)
        ->addMethodCall('setTemplateDir', [__DIR__ . '/../templates'])
        ->addMethodCall('setConfigDir', [__DIR__ . '/../config/vendor/smarty'])
        ->addMethodCall('setCompileDir', [sys_get_temp_dir()])
        ->addMethodCall('setCacheDir', [sys_get_temp_dir()])
        ->addMethodCall('setEscapeHtml', [true])
        ->setPublic(true);

    $container
        ->autowire(HomeController::class)
        ->addTag('app.controller')
    ;
    $container
        ->autowire(CategoryController::class)
        ->addTag('app.controller')
    ;

    $container
        ->register(
            'app.core.kernel.controller_locator',
            ServiceLocator::class,
        )
        ->addTag('container.service_locator')
        ->setPublic(true)
    ;

    $container->addCompilerPass(new ControllerLocatorPass());

    $container->autowire(CategoryGateway::class);

    if ($_ENV['APP_ENV'] === 'test') {
        $container
            ->autowire(DatabaseResetterInterface::class, DatabaseResetter::class)
            ->setArgument('$initSqlFilePath', __DIR__ . '/../db.sql')
            ->setArgument('$dbUrl', $_ENV['DATABASE_URL'])
            ->setPublic(true);
    }

    $container->compile();

    return $container;

};
