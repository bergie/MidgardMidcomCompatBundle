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

    public static function read_array_from_file($filename)
    {
        if (!file_exists($filename))
        {
            return array();
        }

        try
        {
            $data = file_get_contents($filename);
        }
        catch (Exception $e)
        {
            return false;
        }

        return midcom_helper_misc::parse_config($data);
    }

}
