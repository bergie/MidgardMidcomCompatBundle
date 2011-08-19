<?php
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;

class midcom_services_style extends RequestAware
{
    public function show($name)
    {
        $viewName = sprintf('%s:%s:%s.%s.%s',
            $this->request->attributes->get('midcom_component'),
            $this->request->attributes->get('midcom_controller'),
            $name,
            'html',
            'midcom'
        );

        if (!isset($this->request_data)) {
            $this->request_data = $this->request->attributes->get('midcom_request_data');
        }

        echo $this->container->get('templating')->render($viewName, $this->request_data);
    }
}
