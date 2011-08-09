<?php
namespace Midgard\MidcomCompatBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser as BaseTemplateNameParser;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateNameParser extends BaseTemplateNameParser
{
    public function __construct(KernelInterface $kernel, $container)
    {
        $this->kernel = $kernel;
        $this->container = $container;
    }

    public function parse($name)
    {
        $originalReference = parent::parse($name);

        $request = $this->container->get('request');

        return new TemplateReference(
            $originalReference->get('bundle'), 
            $originalReference->get('controller'),
            $originalReference->get('name'),
            $originalReference->get('format'),
            $originalReference->get('engine')
        );
    }
}
