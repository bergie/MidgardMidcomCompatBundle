<?php
namespace Midgard\MidcomCompatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermalinkController extends Controller
{
    public function redirectAction($guid)
    {
        if (class_exists('midcom')) {
            $uri = \midcom::get()->permalinks->resolve_permalink($guid);
            var_dump($uri);
            if ($uri) {
                return $this->redirect($uri);
            }
        }

        throw new NotFoundHttpException("{$guid} not found");
    }
}
