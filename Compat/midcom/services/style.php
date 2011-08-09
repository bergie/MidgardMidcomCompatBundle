<?php
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;

class midcom_services_style extends RequestAware
{
    public function show($name)
    {
        $content = '';
        if ($this->request->attributes->has('midcom_content'))
        {
            $content = $this->request->attributes->get('midcom_content');
        }

        $viewName = sprintf('%s:%s:%s.%s.%s',
            $this->request->attributes->get('midcom_component'),
            $this->request->attributes->get('midcom_controller'),
            $name,
            'html',
            'midcom'
        );

        var_dump(array_keys($this->request->attributes->get('midcom_request_data')));
        die();

        $content .= $this->container->get('templating')->render($viewName, $this->request->attributes->get('midcom_request_data'));

        $this->request->attributes->set('midcom_content', $content);
    }
}
