<?php
namespace Midgard\MidcomCompatBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../Compat/show.php';

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
        $viewer->show($request);
        $content = $request->attributes->get('midcom_content');

        if ($this->container->hasParameter('midgard.midcomcompat.layout')) {
            $content = $this->container->get('templating')->render(
                $this->container->getParameter('midgard.midcomcompat.layout'),
                array(
                    'content' => $content,
                )
            );
        }


        $response = new Response($content);

        $request->attributes->set('midcom_request_data', null);

        $event->setResponse($response);
    }
}
