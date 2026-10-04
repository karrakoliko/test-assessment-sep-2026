<?php

namespace App\Core\Di;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds tagged controllers to the controller locator
 */
final class ControllerLocatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $references = [];

        foreach ($container->findTaggedServiceIds('app.controller') as $id => $_) {
            $references[$id] = new Reference($id);
        }

        $container
            ->getDefinition('app.core.kernel.controller_locator')
            ->setArgument('$factories', $references);
    }
}
