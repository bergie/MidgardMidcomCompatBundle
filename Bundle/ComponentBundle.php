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

        $this->prepareSuperGlobals();
        
        $this->interface = new $interfaceClass();
        $this->interface->_on_initialize();
    }

    private function prepareSuperGlobals()
    {
        if (!isset($_MIDCOM)) {
           $_MIDCOM = \midcom::get();
        }

        if (!isset($_MIDGARD)) {
            $_MIDGARD = array(
                'argv' => array(),
                'user' => 0,
                'admin' => false,
                'root' => false,
                'auth' => false,
                'cookieauth' => false,
                'page' => 0,
                'debug' => false,
                'host' => 0,
                'style' => 0,
                'author' => 0,
                'config' => array(
	                'prefix' => '',
		            'quota' => false,
		            'unique_host_name' => 'sf2',
		            'auth_cookie_id' => 1,
	            ),
                'schema' => array(
	                'types' => array(),
	            ),
            );
        }

        if (!defined('MIDCOM_STATIC_URL')) {
            define('MIDCOM_STATIC_URL', '/');
        }

        if (!isset($GLOBALS['midcom_config']))
        {
            $GLOBALS['midcom_config'] = new \midcom_config;
        }
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
