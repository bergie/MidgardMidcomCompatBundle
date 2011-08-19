<?php

use Symfony\Component\DependencyInjection\ContainerAware;

class midcom_services_permalinks extends ContainerAware
{
    public function create_permalink($guid)
    {
        return $this->container->get('router')->getGenerator()->generate('_midcom_permalink', array('guid' => $guid));
    }
}
