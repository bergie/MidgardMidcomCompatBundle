<?php
namespace Midgard\MidcomCompatBundle\Bundle;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Midgard\MidcomCompatBundle\Config\Loader\MidcomArrayLoader;
use Symfony\Component\Config\FileLocator;

class ComponentExtension extends Extension
{
    private $name = '';

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $container->setParameter('midgard.midcomcompat.' . $this->getAlias(), $container->getParameter('midgard.midcomcompat.root') . '/' . str_replace('.', '/', $this->name) . '/config');
    }

    public function getAlias()
    {
        return str_replace('.', '_', $this->name);
    }
}
