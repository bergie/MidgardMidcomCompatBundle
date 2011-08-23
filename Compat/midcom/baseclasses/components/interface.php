<?php
abstract class midcom_baseclasses_components_interface
{
    protected $_autoload_files = array();
    protected $_autoload_libraries = array();

    public $_component;

    public function get_autoload_files()
    {
        return $this->_autoload_files;
    }

    public function get_autoload_libraries()
    {
        return $this->_autoload_libraries;
    }

    public function _on_initialize()
    {
        return true;
    }
}
