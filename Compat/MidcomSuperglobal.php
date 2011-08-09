<?php
namespace Midgard\MidcomCompatBundle\Compat;

use Symfony\Component\HttpFoundation\Request;

class MidcomSuperglobal
{
    protected $request = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->prepare_context_data();
    }

    private function load_service($service)
    {
        $serviceImplementation = "midcom_services_{$service}";
        $this->$service = new $serviceImplementation();
    }

    public function __get($key)
    {
        $this->load_service($key);
        return $this->$key;
    }

    private function prepare_context_data()
    {
        $context = array();
        $this->request->attributes->set('midcom_context', $context);
    }

    public function get_context_data($param1, $param2 = null)
    {
        $context = $this->request->attributes->get('midcom_context');
        if (!isset($context[$param1]))
        {
            return null;
        }
        return $context[$param1];
    }

    public function set_custom_context_data($key, $value)
    {
        $this->request->attributes->set("midcom_context_custom_{$key}", $value);
    }

    public function get_custom_context_data($key)
    {
        return $this->request->attributes->get("midcom_context_custom_{$key}");
    }

    public function add_link_head($attributes = null)
    {
    }
}
