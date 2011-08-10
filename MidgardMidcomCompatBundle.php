<?php
namespace Midgard\MidcomCompatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Midgard\MidcomCompatBundle\DependencyInjection\ControllerResolverPass;

class MidgardMidcomCompatBundle extends Bundle
{
    private $compatPrefixes = array(
        'midcom_baseclasses',
        'midcom_helper_configuration',
        'midcom_helper_toolbar',
        'midcom_helper_l10n',
        'midcom_helper_nav',
        'midcom_services',
        'midcom_error',
        'midcom_config',
    );

    private $compatClasses = array(
        'midcom',
    );

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ControllerResolverPass());
    }

    public function boot()
    {
        if (!ini_get('midgard.superglobals_compat'))
        {
            throw new \Exception('You need to set midgard.superglobals_compat=On in your php.ini to run MidCOM compatibility bundle with Symfony2');
        }

        define('MIDCOM_ROOT', $this->container->getParameter('midgard.midcomcompat.root'));

        require(__DIR__ . '/Compat/constants.php');

        spl_autoload_register(array($this, 'autoload'));
    }

    public function autoload($className)
    {
        $path = MIDCOM_ROOT . '/' . str_replace('_', '/', $className) . '.php';
        foreach ($this->compatPrefixes as $compatPrefix) {
            if (substr($className, 0, strlen($compatPrefix)) == $compatPrefix) {
                $path = str_replace(MIDCOM_ROOT, __DIR__ . '/Compat', $path);
            }
        }

        foreach ($this->compatClasses as $compatClass) {
            if ($className == $compatClass) {
                $path = str_replace(MIDCOM_ROOT, __DIR__ . '/Compat', $path);
            }
        }

        $path = str_replace('//', '/_', $path);

        if (basename($path) == 'dba.php' || basename($path) == 'db.php') {
            $path = dirname($path) . '.php';
        }


        if (!file_exists($path))
        {
            $alternative_path = str_replace('.php', '/main.php', $path);

            if (!file_exists($alternative_path))
            {
                return false;
            }
            $path = $alternative_path;
        }

        require($path);
    }
}
