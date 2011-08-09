<?php
namespace Midgard\MidcomCompatBundle\Bundle;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

class ComponentBundle extends ContainerAware implements BundleInterface
{
    private $name = '';
    private $interface = null;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function boot()
    {
        $interfaceClass = str_replace('.', '_', $this->name) . "_interface";  
        if (!class_exists($interfaceClass)) {
            $interfaceFile = $this->getPath() . "/midcom/interfaces.php";
            require($interfaceFile);
        }
        
        $this->interface = new $interfaceClass();
        $this->interface->_on_initialize();
    }

    public function shutdown()
    {
    }

    public function build(ContainerBuilder $container)
    {
    }

    public function getContainerExtension()
    {
        return new ComponentExtension($this->name);
    }

    public function getParent()
    {
        return null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return '\\';
    }

    public function getPath()
    {
        return $this->container->getParameter('midgard.midcomcompat.root') . "/" . str_replace('.', '/', $this->name);
    }

    public function registerCommands(Application $application)
    {
    }
}
