<?php
namespace Midgard\MidcomCompatBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolver implements ControllerResolverInterface
{
    private $parent;

    public function __construct(ControllerResolverInterface $parent)
    {
        $this->parent = $parent;
    }

    public function getController(Request $request)
    {
        if (!$request->attributes->has('midcom_component')) {
            // Not a MidCOM request, pass to parent
            return $this->parent->getController($request);
        }

        $controller_class = $request->attributes->get('midcom_controller');

        $controller = new $controller_class();

        return array($controller, $request->attributes->get('midcom_action'));
    }

    public function getArguments(Request $request, $controller)
    {
        if (!$request->attributes->has('midcom_component')) {
            // Not a MidCOM request, pass to parent
            return $this->parent->getArguments($request, $controller);
        }

        $data = array();
        $request->attributes->set('midcom_data', $data);

        return array(
            'foo',
            $request->attributes->all(),
            $data
        );
    }
}
