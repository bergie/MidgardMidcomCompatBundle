<?php
namespace Midgard\MidcomCompatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Midgard\MidcomCompatBundle\Config\Loader\MidcomArrayLoader;
use Midgard\MidcomCompatBundle\Compat\MidcomSuperglobal;
use Symfony\Component\Config\FileLocator;

class ControllerResolver extends ContainerAware implements ControllerResolverInterface
{
    private $parent;

    public function __construct(ContainerInterface $container, ControllerResolverInterface $parent)
    {
        $this->parent = $parent;
        $this->setContainer($container);
    }

    protected function getRequestConfig(Request $request)
    {
        $parameter = 'midgard.midcomcompat.' . str_replace('.', '_', $request->attributes->get('midcom_component'));
        $loader = new MidcomArrayLoader(new FileLocator($this->container->getParameter($parameter)));
        return new ParameterBag($loader->load('config.inc'));
    }

    public function getController(Request $request)
    {
        if (!$request->attributes->has('midcom_component')) {
            // Not a MidCOM request, pass to parent
            return $this->parent->getController($request);
        }

        $_MIDCOM = new MidcomSuperglobal($request);

        $viewerClass = str_replace('.', '_', $request->attributes->get('midcom_component') . '_viewer');

        $config = $this->getRequestConfig($request);

        $viewer = new $viewerClass();
        $viewer->initialize($request, $config);

        // TODO: Call can_handle and pass to parent->getController if not

        return array($viewer, 'handle');
    }

    public function getArguments(Request $request, $controller)
    {
        return array(
            $request
        );
    }
}
