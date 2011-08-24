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

    public static function update($object)
    {
        return $object->__exec_update();
    }

    public static function get_parent($object)
    {
        return $object->__object->get_parent();
    }

    public static function get_by_path($object, $path)
    {
        $object->__exec_get_by_path((string) $path);
        return true;
    }

    public static function get_parameter($object, $domain, $name)
    {
        return $object->__object->parameter($domain, $name);
    }

    public static function set_parameter($object, $domain, $name, $value)
    {
        return $object->__object->parameter($domain, $name, $value);
    }

    public static function list_parameters($object, $domain)
    {
        return $object->__object->list_parameters($domain);
    }

    public static function delete($object)
    {
        return $object->__exec_delete();
    }
}
