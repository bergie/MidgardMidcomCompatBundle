<?php
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;
use Symfony\Component\HttpFoundation\Request;

class midcom_services_session extends RequestAware
{
    public function __construct()
    {
        $this->setRequest($_MIDCOM->request);
    }

    public function setRequest(Request $request)
    {
        parent::setRequest($request);

        // Ensure request always has session if sessioning service is
        // being used
        if ($this->request->hasSession()) {
            return;
        }

        $this->request->setSession($this->container->get('session'));
    }

    public function get($key)
    {
        return $this->request->getSession()->get($key);
    }

    public function set($key, $value)
    {
        return $this->request->getSession()->set($key, $value);
    }

    public function exists($key)
    {
        return $this->request->getSession()->has($key);
    }

    public function remove($key)
    {
        return $this->request->getSession()->remove($key);
    }

    public function get_session_data()
    {
        return $this->request->getSession()->all();
    }
}
