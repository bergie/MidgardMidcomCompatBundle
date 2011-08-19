<?php

use Midgard\MidcomCompatBundle\Bundle\ComponentBundle;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class midcom_services_componentloader extends ContainerAware
{
    private $loadedBundles = array();

    public function setContainer(ContainerInterface $container = null)
    {
        foreach ($container->get('kernel')->getBundles() as $name => $bundle) {
            $this->loadedBundles[] = $name;
        }
        parent::setContainer($container);
    }

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
        if (in_array($path, $this->loadedBundles)) {
            return true;
        }
        $bundle = new ComponentBundle($path);
        $bundle->setContainer($this->container);
        $bundle->boot();
        $this->loadedBundles[] = $path;
        return true;
    }

    public function load_graceful($path)
    {
        try {
            return $this->load($path);
        } catch (\Exception $e) {
            return false;
        }
    }

}
