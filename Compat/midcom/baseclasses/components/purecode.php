<?php
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Midgard\MidcomCompatBundle\Config\Loader\MidcomArrayLoader;
use Symfony\Component\Config\FileLocator;

abstract class midcom_baseclasses_components_purecode
{
    public $_component;

    public function __construct()
    {
        if ($this->_component == '')
        {
            $this->_component = preg_replace('/^(.+?)_(.+?)_(.+?)_.+/', '$1.$2.$3', get_class($this));
        }
    }

    private function loadConfig()
    {
        $path = MIDCOM_ROOT . '/' . str_replace('.', '/', $this->_component) . '/config';
        $loader = new MidcomArrayLoader(new FileLocator($path));
        return new ParameterBag($loader->load('config.inc'));
    }

    public function __get($key)
    {
        if ($key == '_config') {
            $this->_config = $this->loadConfig();
        }
        if ($key == '_l10n') {
            return $_MIDCOM->i18n->get_l10n($this->_component);
        }
        if ($key == '_l10n_midcom') {
            return $_MIDCOM->i18n->get_l10n('midcom');
        }
        return $this->$key;
    }

    public function add_stylesheet($url)
    {
    }
}
