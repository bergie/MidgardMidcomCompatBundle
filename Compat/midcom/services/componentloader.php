<?php

use Midgard\MidcomCompatBundle\Bundle\ComponentBundle;

class midcom_services_componentloader
{
    public function is_installed($component)
    {
        return true;
    }

    public function path_to_prefix($path)
    {
        return strtr($path, ".", "_");
    }

    public function load($path)
    {
        new ComponentBundle($path);
    }
}
