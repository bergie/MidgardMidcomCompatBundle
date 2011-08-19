<?php
namespace Midgard\MidcomCompatBundle\Compat;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Midgard\MidcomCompatBundle\Bundle\ComponentBundle;

class MidcomApplication extends RequestAware
{
    private $services = array();

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        foreach ($this->services as $service) {
            if ($service instanceof ContainerAware) {
                $service->setContainer($container);
            }
        }
    }

    public function setRequest(Request $request)
    {
        parent::setRequest($request);

        foreach ($this->services as $service) {
            if ($service instanceof RequestAware) {
                $service->setRequest($request);
            }
        }

        $this->prepare_context_data();
    }

    private function load_service($service)
    {
        if (isset($this->services[$service])) {
            return;
        }
        $serviceImplementation = "midcom_services_{$service}";
        $this->services[$service] = new $serviceImplementation();
        if ($this->services[$service] instanceof RequestAware) {
            $this->services[$service]->setRequest($this->request);
        }

        if ($this->services[$service] instanceof ContainerAware) {
            $this->services[$service]->setContainer($this->container);
        }
    }

    public function load_library($library)
    {
        return $this->componentloader->load_graceful($library);
    }

    public function __get($key)
    {
        $this->load_service($key);
        if (isset($this->services[$key])) {
            return $this->services[$key];
        }
        return $this->$key;
    }

    public function __set($key, $value)
    {
        if ($key == 'skip_page_style') {
            $this->request->attributes->set('midcom_skip_style', $value);
        }

        $this->$key = $value;
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

    public function header()
    {
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

    public function get_host_name()
    {
        return '';
    }
}
