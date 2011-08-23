<?php
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;
use Midgard\MidcomCompatBundle\Templating\TemplateReference; 

class midcom_services_style extends RequestAware
{
    public $data;

    public function show($name)
    {
        $viewName = sprintf('%s:%s:%s.%s.%s',
            $this->request->attributes->get('midcom_component'),
            $this->request->attributes->get('midcom_controller'),
            $name,
            'html',
            'midcom'
        );

        if (!$this->data) {
            $this->data = $this->request->attributes->get('midcom_request_data');
        }

        echo $this->container->get('templating')->render($viewName, $this->data);
    }

    public function show_midcom($name)
    {
        $midcomRoot = $this->container->getParameter('midgard.midcomcompat.root');
        $layout = new TemplateReference(realpath("{$midcomRoot}/midcom"), '', $name, 'html', 'midcom');

        if (!$this->data) {
            $this->data = $this->request->attributes->get('midcom_request_data');
        }

        echo $this->container->get('templating')->render($layout, $this->data);
    }
}
