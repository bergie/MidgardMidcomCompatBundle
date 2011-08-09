<?php
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Midgard\MidcomCompatBundle\Config\Loader\MidcomArrayLoader;
use Symfony\Component\Config\FileLocator;

class midcom_helper_configuration
{
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        $this->parameterBag = null === $parameterBag ? new ParameterBag() : $parameterBag;
    }

    public function get($key)
    {
        return $this->parameterBag->get($key);
    }
}
