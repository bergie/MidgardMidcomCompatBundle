<?php
namespace Midgard\MidcomCompatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerResolver extends ContainerAware implements ControllerResolverInterface
{
    private $parent;

    public function __construct(ContainerInterface $container, ControllerResolverInterface $parent)
    {
        $this->parent = $parent;
        $this->setContainer($container);
    }

    public function getController(Request $request)
    {
        if (!$request->attributes->has('midcom_component')) {
            // Not a MidCOM request, pass to parent
            return $this->parent->getController($request);
        }

        // Register the request with MidCOM
        $_MIDCOM->setRequest($request);

        $viewerClass = str_replace('.', '_', $request->attributes->get('midcom_component') . '_viewer');

        $component = $this->container->get('kernel')->getBundle($request->attributes->get('midcom_component'));

        $viewer = new $viewerClass();
        $viewer->initialize($request, $component->getConfig());

        $request->attributes->set('midcom_viewer_instance', $viewer);

        return array($viewer, 'handle');
    }

    public function getArguments(Request $request, $controller)
    {
        if (!$request->attributes->has('midcom_component')) {
            return $this->parent->getArguments($request, $controller);
        }

        return array(
            $request,
            $this->cleanArguments($request)
        );
    }

    public function cleanArguments(Request $request)
    {
        $args = $request->attributes->all();
        $midcomArgs = array();
        foreach ($args as $key => $value) {
            if (substr($key, 0, 11) != 'midcom_arg_') {
                continue;
            }
            $midcomArgs[substr($key, 11)] = $value;
        }
        return $midcomArgs;
    }
}
