<?php
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Midgard\MidcomCompatBundle\Config\Loader\MidcomArrayLoader;
use Symfony\Component\Config\FileLocator;

abstract class midcom_baseclasses_components_configuration
{
    public static function get($component, $key)
    {
        if ($key == 'config')
        {
            $path = MIDCOM_ROOT . '/' . str_replace('.', '/', $component) . '/config';
            $loader = new MidcomArrayLoader(new FileLocator($path));
            return new ParameterBag($loader->load('config.inc'));
        }
        return null;
    }
}
