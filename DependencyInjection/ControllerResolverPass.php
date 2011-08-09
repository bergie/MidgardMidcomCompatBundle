<?php
namespace Midgard\MidcomCompatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ControllerResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $cr = $container->getDefinition('controller_resolver');
        $container->setDefinition('midgard.midcomcompat.controller_resolver.original', $cr);
        $def = new Definition('Midgard\MidcomCompatBundle\Controller\ControllerResolver');
        $def->setArguments(array(new Reference('service_container'), new Reference('midgard.midcomcompat.controller_resolver.original')));
        $container->setDefinition('controller_resolver', $def);
    }
}
