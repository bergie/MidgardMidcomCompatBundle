<?php
namespace Midgard\MidcomCompatBundle\Config\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;

class MidcomArrayLoader extends FileLoader
{
    public function __construct(FileLocator $locator) 
    {
        parent::__construct($locator);
    }

    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        eval('$contents = array(' . file_get_contents($path) . ');');

        return $contents;
    }

    public function supports($resource, $type = null)
    {
        if (pathinfo($resource, PATHINFO_EXTENSION) != 'inc') {
            return false;
        }

        return true;
    }
}
