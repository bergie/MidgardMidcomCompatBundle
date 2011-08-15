<?php
namespace Midgard\MidcomCompatBundle\EventListener;

use \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class KernelExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('midcom_component')) {
            return;
        }

        $request->attributes->set('midcom_request_data', null);
    }
}
