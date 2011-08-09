<?php
namespace Midgard\MidcomCompatBundle\Compat;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;
use Midgard\MidcomCompatBundle\Bundle\ComponentBundle;

class MidcomApplication extends RequestAware
{
    public function setRequest(Request $request)
    {
        parent::setRequest($request);
        $this->prepare_context_data();
    }

    private function load_service($service)
    {
        $serviceImplementation = "midcom_services_{$service}";
        $this->$service = new $serviceImplementation();

        if ($this->$service instanceof RequestAware) {
            $this->$service->setRequest($this->request);
        }

        if ($this->$service instanceof ContainerAware) {
            $this->$service->setContainer($this->container);
        }
    }

    public function load_library($library)
    {
        $library = new ComponentBundle($library); 
        $library->setContainer($this->container);
        $library->boot();
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
        $this->head->add_link_head($attributes);
    }

    public function add_jsfile($url, $prepend = false)
    {
        $this->head->add_jsfile($url, $prepend);
    }

    public function relocate($url)
    {
        $this->request->attributes->set('midcom_response', new RedirectResponse($url));
    }

    public function set_pagetitle($title)
    {
        $this->request->attributes->set('midcom_title', $title);
    }

    public function bind_view_to_object($object)
    {
        $this->request->attributes->set('midcom_object', $object);
    }

    public function set_26_request_metadata()
    {
    }
}
