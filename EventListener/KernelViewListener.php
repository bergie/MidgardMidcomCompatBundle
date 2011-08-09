<?php
namespace Midgard\MidcomCompatBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;

class KernelViewListener
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function filterResponse(GetResponseForControllerResultEvent $event)
    {
        if ($event->hasResponse()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->attributes->has('midcom_component')) {
            return;
        }

        if ($request->attributes->has('midcom_response')) {
            return $event->setResponse($request->attributes->get('midcom_response'));
        }

        $viewer = $request->attributes->get('midcom_viewer_instance');
        ob_start();
        $viewer->show($request);
        var_dump(array_keys($request->attributes->get('midcom_request_data')));
        $response = new Response(ob_get_clean());

        $event->setResponse($response);
    }
}
