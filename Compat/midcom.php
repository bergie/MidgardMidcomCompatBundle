<?php
use Midgard\MidcomCompatBundle\Compat\MidcomApplication;

class midcom
{
    private static $_instance;

    public static function get($service = null)
    {
        if (null === self::$_instance) {
            self::_initialize();
        }
        if (null === $service) {
            return self::$_instance;
        }
        return self::$_instance->$service;
    }

    public static function get_version()
    {
        return '';
    }

    private static function _initialize()
    {
        self::$_instance = new MidcomApplication;
    }
}
?>
