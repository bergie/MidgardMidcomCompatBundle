<?php
class midcom_baseclasses_core_dbobject
{
    public static function post_db_load_checks()
    {
        return true;
    }

    public static function create($object)
    {
        return $object->__exec_create();
    }

    public static function get_parameter($object, $domain, $name)
    {
        return $object->__object->parameter($domain, $name);
    }
}
