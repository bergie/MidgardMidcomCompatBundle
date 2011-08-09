<?php
class midcom_services_dbfactory
{
    public function new_query_builder($class)
    {
        $qb = new midcom_core_querybuilder($class);
        $qb->initialize();
        return $qb;
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

    public static function get_cached($classname, $identifier)
    {
        return new $classname($identifier);
    }
}
