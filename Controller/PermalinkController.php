<?php
namespace Midgard\MidcomCompatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermalinkController
{
    public function redirectAction($guid)
    {
        // TODO: Implement permalink resolution
        throw new NotFoundHttpException("{$guid} not found");
    }
}
