<?php
namespace Midgard\MidcomCompatBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;
use Midgard\MidcomCompatBundle\Templating\TemplateReference; 

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

        ob_start();
        $viewer->show($request);
        $content = ob_get_clean();

        if ($this->container->hasParameter('midgard.midcomcompat.layout') &&  $this->container->getParameter('midgard.midcomcompat.layout') && !$request->attributes->has('midcom_skip_style')) {
            $layoutName = $this->container->getParameter('midgard.midcomcompat.layout');

            // Convert absolute paths to TemplateReference instances
            if (substr($layoutName, 0, 1) == '/') {
                $parts = explode(':', $layoutName);
                $type = explode('.', $parts[2]);
                $layoutName = new TemplateReference(realpath($parts[0]), $parts[1], $type[0], $type[1], $type[2]);
            }

            $content = $this->container->get('templating')->render($layoutName, array('content' => $content));
        }

        $response = new Response($content);

        if ($request->attributes->has('midcom_headers')) {
            $headers = $request->attributes->get('midcom_headers');
            foreach ($headers as $header => $value) {
                $response->headers->set($header, $value);
            }
        }

        // Remove MidCOM data from request to ease debugging
        $request->attributes->set('midcom_request_data', null);
        $request->attributes->set('midcom_context', null);

        $event->setResponse($response);
    }
}
