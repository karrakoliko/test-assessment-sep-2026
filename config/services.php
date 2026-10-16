<?php

use App\App\Controller\ArticleController;
use App\App\Controller\CategoryController;
use App\App\Controller\HomeController;
use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\App\Service\HomePage\HomePageService;
use App\Core\Di\ControllerLocatorPass;
use App\Core\Persistence\Database\Resetter\DatabaseResetter;
use App\Core\Persistence\Database\Resetter\DatabaseResetterInterface;
use App\Core\Router\ArgumentResolver\ArgumentResolver;
use App\Core\Router\ArgumentResolver\ArgumentResolverInterface;
use App\Core\Router\ControllerReference;
use App\Core\Router\Route\Collection\RouteCollection;
use App\Core\Router\Router;
use App\Core\Router\Route\Argument\RouteArgument;
use App\Core\Router\Route\Argument\RouteArguments;
use App\Core\Router\Route\Route;
use App\Core\Router\RouterInterface;
use App\Core\Router\UrlGenerator\UrlGenerator;
use App\Core\Router\UrlGenerator\UrlGeneratorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Smarty\Smarty;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
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
        ->register(RouteCollection::class, RouteCollection::class)
        ->setArgument('$routes', [
            new Route(
                mask: '/',
                controllerReference: new ControllerReference(
                    class: HomeController::class,
                    method: 'index'
                ),
                name: 'home'
            ),
            new Route(
                mask: '/category/{id}',
                controllerReference: new ControllerReference(
                    class: CategoryController::class,
                    method: 'show'
                ),
                name: 'category__show',
                arguments: new RouteArguments(
                    new RouteArgument('id', '/^\d+$/')
                )
            ),
            new Route(
                mask: '/article/{id}',
                controllerReference: new ControllerReference(
                    class: ArticleController::class,
                    method: 'show'
                ),
                name: 'article__show',
                arguments: new RouteArguments(
                    new RouteArgument('id', '/^\d+$/')
                )
            )
        ]);

    $container
        ->register(
            RouterInterface::class,
            Router::class
        )
        ->setArgument('$routes', new Reference(RouteCollection::class))
        ->setPublic(true)
    ;

    $container->autowire(UrlGenerator::class, UrlGenerator::class);
    $container->setAlias(UrlGeneratorInterface::class, UrlGenerator::class);

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
        ->autowire(ArticleController::class)
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
    $container->autowire(ArticleGateway::class);

    $container->autowire(
        HomePageService::class
    );

    if ($_ENV['APP_ENV'] !== 'prod') {
        $container
            ->autowire(\App\App\Service\Seeder\ChunkedSeeder::class)
            ->setArgument('$environment', $_ENV['APP_ENV'])
            ->setPublic(true);

        $container
            ->autowire(DatabaseResetterInterface::class, DatabaseResetter::class)
            ->setArgument('$initSqlFilePath', __DIR__ . '/../db.sql')
            ->setArgument('$dbUrl', $_ENV['DATABASE_URL'])
            ->setPublic(true);
    }

    $container->compile();

    return $container;

};
