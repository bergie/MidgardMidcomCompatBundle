<?php

namespace Midgard\MidcomCompatBundle\Router\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Yaml\Yaml;

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
        $path = "{$this->rootDir}/" . str_replace('.', '/', $resource) . "/config/routes.inc";
        eval('$routes = array(' . file_get_contents($path) . ');');
        $collection = new RouteCollection();
        $collection->addResource(new FileResource($path));

        foreach ($routes as $route_id => $route)
        {
            $defaults = array(
                'midcom_component' => $resource,
                'midcom_controller' => $route['handler'][0],
                'midcom_action' => "_handler_{$route['handler'][1]}",
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
                    $path .= '/{' . $i . '}';
                    $route['variable_args']--;
                }
            }

            $reqs = array();
            $options = array();

            $route = new Route($path, $defaults, $reqs, $options);
            $collection->add($route_id, $route);
        }
        return $collection;
    }
}
