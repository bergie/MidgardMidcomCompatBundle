<?php
namespace Midgard\MidcomCompatBundle\Templating;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParser;

class MidcomEngine implements EngineInterface
{
    private $locator;
    private $parser;

    public function __construct(FileLocatorInterface $locator, TemplateNameParser $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
    }

    public function render($name, array $parameters = array())
    {
        die(file_get_contents($this->findTemplate($name));
    }

    public function exists($name)
    {
        return (file_exists($this->findTemplate($name)));
    }

    public function supports($name)
    {
        $template = $this->parser->parse($name);
        return $template && 'midcom' === $template->get('engine');
    }

    private function findTemplate($name)
    {
        if (!is_array($name)) {
            $name = $this->parser->parse($name);
        }

        if (false == $file = $this->locator->locate($name)) {
            throw new \RuntimeException(sprintf('Unable to find template "%s".', json_encode($name)));
        }

        return $file;
    }
}
