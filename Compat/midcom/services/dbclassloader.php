<?php
use Midgard\MidcomCompatBundle\Config\Loader\MidcomArrayLoader;
use Midgard\MidcomCompatBundle\Compat\MidcomSuperglobal;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerAware;

class midcom_services_dbclassloader extends ContainerAware
{
    private $mappings = array();

    public function load_classes($component, $filename)
    {
        $path = $this->container->getParameter('midgard.midcomcompat.root') . '/' . str_replace('.', '/', $component) . '/config';
        $loader = new MidcomArrayLoader(new FileLocator($path));
        $definitions = $loader->load($filename);
        foreach ($definitions as $mgdschema => $midcom)
        {
            $this->mappings[$mgdschema] = $midcom;
        }
    }

    public function is_mgdschema_object($object)
    {
        if ($object instanceof midcom_core_dbaobject) {
            return true;
        }
        return $object instanceof midgard_object;
    }

    public function get_midcom_class_name_for_mgdschema_object(midgard_object $object)
    {
        if (!isset($this->mappings[get_class($object)])) {
            return false;
        }

        return $this->mappings[get_class($object)];
    }

    public function is_midcom_db_object($object)
    {
        if (is_object($object))
        {
            return (is_a($object, 'midcom_core_dbaobject') || is_a($object, 'midcom_core_dbaproxy'));
        }
        else if (   is_string($object)
                 && class_exists($object))
        {
            $dummy = new $object();
            return is_a($dummy, 'midcom_core_dbaobject');
        }

        return false;
    }
}
