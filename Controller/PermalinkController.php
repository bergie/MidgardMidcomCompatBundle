<?php
namespace Midgard\MidcomCompatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use midgard_query_builder as QB;
use Midgard\MidcomCompatBundle\Bundle\ComponentBundle;

class PermalinkController extends Controller
{
    public function redirectAction($guid)
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
                    return $this->redirect($uri);
                }
            }
        }

        throw new NotFoundHttpException("{$guid} not found");
    }
}
