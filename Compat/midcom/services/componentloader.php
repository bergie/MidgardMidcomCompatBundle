<?php
class midcom_services_componentloader
{
    public function is_installed($component)
    {
        return true;
    }

    public function path_to_prefix ($path)
    {
        return strtr($path, ".", "_");
    }
}
