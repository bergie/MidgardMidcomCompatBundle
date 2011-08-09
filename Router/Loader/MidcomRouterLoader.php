<?php

namespace Midgard\MidcomCompatBundle\Router\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Midgard\MidcomCompatBundle\Config\Loader\MidcomArrayLoader;
use Symfony\Component\Config\FileLocator;

class MidcomRouterLoader extends Loader
{
    private $rootDir = '';

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function supports($resource, $type = null)
    {
        if ($type != 'midcom') {
            return false;
        }

        if (!is_string($resource)) {
            return false;
        }

        $path = "{$this->rootDir}/" . str_replace('.', '/', $resource) . "/config/routes.inc"; 
        if (!file_exists($path)) {
            return false;
        }

        return true;
    }

    public function load($resource, $type = null)
    {
        $locator = new FileLocator("{$this->rootDir}/" . str_replace('.', '/', $resource) . '/config');
        $loader = new MidcomArrayLoader($locator);
        $routes = $loader->load('routes.inc');
        $collection = new RouteCollection();
        $collection->addResource(new FileResource($locator->locate('routes.inc')));

        foreach ($routes as $route_id => $route)
        {
            $defaults = array(
                'midcom_route_id' => $route_id,
                'midcom_component' => $resource,
                'midcom_controller' => $route['handler'][0],
                'midcom_action' => $route['handler'][1],
            );

            if (!isset($route['fixed_args'])) {
                $route['fixed_args'] = array();
            }
            if (!is_array($route['fixed_args'])) {
                $route['fixed_args'] = array($route['fixed_args']);
            }

            $path = '/' . implode('/', $route['fixed_args']);

            if (isset($route['variable_args'])) {
                $i = 0;
                while ($route['variable_args']) {
                    $path .= '/{midcom_arg_' . $i . '}';
                    $route['variable_args']--;
                }
            }

            $path = str_replace('//', '/', $path);

            $reqs = array();
            $options = array();

            $route = new Route($path, $defaults, $reqs, $options);
            $collection->add($route_id, $route);
        }
        return $collection;
    }
}
