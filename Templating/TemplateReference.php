<?php
namespace Midgard\MidcomCompatBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference as BaseTemplateReference;

class TemplateReference extends BaseTemplateReference
{
    public function getPath()
    {
        $path = sprintf('%s/style/%s.php', $this->get('bundle'), $this->get('name'));
 
        if (substr($path, 0, 1) == '/') {
            return $path;
        }

        return '@' . $path;
    }
}
