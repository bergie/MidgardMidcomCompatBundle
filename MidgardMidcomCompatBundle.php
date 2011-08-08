<?php
namespace Midgard\MidcomCompatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Midgard\MidcomCompatBundle\DependencyInjection\ControllerResolverPass;

class MidgardMidcomCompatBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ControllerResolverPass());
    }

    public function boot()
    {
        define('MIDCOM_ROOT', $this->container->getParameter('midgard.midcomcompat.root'));

        require(__DIR__ . '/Compat/constants.php');

        spl_autoload_register(array($this, 'autoload'));
    }

    public function autoload($className)
    {
        $path = MIDCOM_ROOT . '/' . str_replace('_', '/', $className) . '.php';
        $path = str_replace('//', '/_', $path);

        if (!file_exists($path)) {
            // TODO: Handle DBA classes, main.php
            return false;
        }

        require($path);
    }
}
