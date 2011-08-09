<?php
namespace Midgard\MidcomCompatBundle\Templating;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParser;

class MidcomEngine implements EngineInterface
{
    private $locator;
    private $parser;
    private $filters = array
    (
       'h' => 'html',
       'H' => 'html',
       'p' => 'php',
       'u' => 'rawurlencode',
       'f' => 'nl2br',
       's' => 'unmodified',
    );

    public function __construct(FileLocatorInterface $locator, TemplateNameParser $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
    }

    public function render($name, array $parameters = array())
    {
        $data =& $parameters;
        $template = file_get_contents($this->findTemplate($name));
        ob_start();
        eval('?>' . $this->preparse($template));
        return ob_get_clean();
    }

    private function preparse($code)
    {
        // Get style elements
        $code = preg_replace_callback("/<\\(([a-zA-Z0-9 _-]+)\\)>/", array('midcom_helper_misc', 'includeElement'), $code);
        // Echo variables
        $code = preg_replace_callback("%&\(([^)]*)\);%i", array($this, 'expandVariable'), $code);
        return $code;
    }

    public function expandVariable($variable)
    {
        $variable_parts = explode(':', $variable[1]);
        $variable = '$' . $variable_parts[0];

        if (strpos($variable, '.') !== false)
        {
            $parts = explode('.', $variable);
            $variable = $parts[0] . '->' . $parts[1];
        }

        if (    isset($variable_parts[1])
             && array_key_exists($variable_parts[1], $this->filters))
        {
            switch ($variable_parts[1])
            {
                case 's':
                    //display as-is
                case 'h':
                case 'H':
                    //According to documentation, these two should do something, but actually they don't...
                    $command = 'echo ' . $variable;
                    break;
                case 'p':
                    $command = 'eval(\'?>\' . ' . $variable . ')';
                    break;
                default:
                    $function = $this->filters[$variable_parts[1]];
                    $command = $function . '(' . $variable . ')';
                    break;
            }
        }
        else
        {
            $command = 'echo htmlentities(' . $variable . ', ENT_COMPAT, "utf-8")';
        }

        return "<?php $command; ?>";
    }

    public function includeElement($name)
    {
        return $name;
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
