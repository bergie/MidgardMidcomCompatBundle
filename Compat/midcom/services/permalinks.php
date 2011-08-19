<?php

use Symfony\Component\DependencyInjection\ContainerAware;
use Midgard\MidcomCompatBundle\Bundle\ComponentBundle;
use midgard_query_builder as QB;

class midcom_services_permalinks extends ContainerAware
{
    public function resolve_permalink($guid)
    {
        $qb = new QB('midgard_topic');
        $topics = $qb->execute();

        $bundles = $this->container->get('kernel')->getBundles();
        foreach ($bundles as $bundle) {
            if (!$bundle instanceof ComponentBundle) {
                continue;
            }

            foreach ($topics as $topic) {
                $uri = $bundle->resolvePermalink($topic, $guid);
                if ($uri) {
                    return $uri;
                }
            }
        }
        return null;
    }

    public function create_permalink($guid)
    {
        return $this->container->get('router')->getGenerator()->generate('_midcom_permalink', array('guid' => $guid));
    }
}
