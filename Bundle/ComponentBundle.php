<?php
namespace Midgard\MidcomCompatBundle\Bundle;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;
use Midgard\MidcomCompatBundle\Config\Loader\MidcomArrayLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Config\FileLocator;

require __DIR__ . '/../Compat/debug.php';

class ComponentBundle extends ContainerAware implements BundleInterface
{
    private $name = '';
    private $interface = null;
    private $loaded = array();
    private $config = null;

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

        foreach ($this->interface->get_autoload_libraries() as $library) {
            $_MIDCOM->componentloader->load($library);
        }

        foreach ($this->interface->get_autoload_files() as $file) {
            $path = $this->getPath() . "/{$file}";
            if (in_array($path, $this->loaded)) {
                continue;
            }
            require($path);
            $this->loaded[] = $path;
        }

        $this->interface->_on_initialize();
    }

    private function prepareSuperGlobals()
    {
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

        if (!defined('MIDCOM_STATIC_ROOT')) {
            define('MIDCOM_STATIC_ROOT', '/tmp');
        }

        if (!isset($GLOBALS['midcom_config']))
        {
            $GLOBALS['midcom_config'] = new \midcom_config;
        }

        if (!isset($_MIDCOM)) {
            $_MIDCOM = \midcom::get();
            $_MIDCOM->setContainer($this->container);

            // Load main DBA mappings
            $_MIDCOM->dbclassloader->load_classes('midcom', 'core_classes.inc');
            $_MIDCOM->dbclassloader->load_classes('midcom', 'legacy_classes.inc');
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
        return null;
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

    public function getConfig(\midgard_topic $topic = null)
    {
        if ($this->config) {
            return $this->config;
        }

        $loader = new MidcomArrayLoader(new FileLocator($this->getPath() . '/config'));
        $this->config = new ParameterBag($loader->load('config.inc'));
        return $this->config;
    }

    public function resolvePermalink(\midgard_topic $topic, $guid)
    {
        return $this->interface->_on_resolve_permalink($topic, $guid, $this->getConfig());
    }
}
