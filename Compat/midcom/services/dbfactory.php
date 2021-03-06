<?php
class midcom_services_dbfactory
{
    public function new_query_builder($class)
    {
        $qb = new midcom_core_querybuilder($class);
        $qb->initialize();
        return $qb;
    }

    function new_collector($classname, $domain, $value)
    {
        $mc = new midcom_core_collector($classname, $domain, $value);
        $mc->initialize();
        return $mc;
    }

    public function get_object_by_guid($guid)
    {
        try
        {
            $tmp = midgard_object_class::get_object_by_guid($guid);
        }
        catch (midgard_error_exception $e)
        {
            debug_add('Loading object by GUID ' . $guid . ' failed, reason: ' . $e->getMessage(), MIDCOM_LOG_INFO);

            throw new midcom_error_midgard($e, $guid);
        }
        if (   get_class($tmp) == 'midgard_person'
            && $GLOBALS['midcom_config']['person_class'] != 'midgard_person')
        {
            $tmp = new $GLOBALS['midcom_config']['person_class']($guid);
        }

        return $this->convert_midgard_to_midcom($tmp);
    }

    public function convert_midgard_to_midcom(midgard_object $object)
    {
        $classname = $_MIDCOM->dbclassloader->get_midcom_class_name_for_mgdschema_object($object);

        if (!$classname) {
            throw new \RuntimeException("Cannot convert " . get_class($object) . " to MidCOM DBA object");
        }

        return new $classname($object);
    }

    public function convert_midcom_to_midgard($object)
    {
        if ($object instanceof midgard_object) {
            return $object;
        }
        return $object->__object;
    }

    public static function &get_cached($classname, $identifier)
    {
        $object = new $classname($identifier);
        return $object;
    }

    public function is_a($object, $class)
    {
        if ($object instanceof $class) {
            return true;
        }

        if (   isset($object->__object)
            && is_object($object->__object)
            && is_a($object->__object, $class))
        {
            // Decorator whose MgdSchema object matches
            return true;
        }

        if (   isset($object->__mgdschema_class_name__)
            && $object->__mgdschema_class_name__ == $class)
        {
            // Decorator without object instantiated, check class match
            return true;
        }

        return false;
    }

    public function property_exists($object, $property)
    {
        return property_exists($object, $property);
    }
}
