<?php
namespace Midgard\MidcomCompatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;

class RequestAware extends ContainerAware
{
    protected $request = null;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
