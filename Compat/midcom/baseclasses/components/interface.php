<?php
abstract class midcom_baseclasses_components_interface
{
    protected $_autoload_files = array();

    public function get_autoload_files()
    {
        return $this->_autoload_files;
    }

    public function _on_initialize()
    {
        return true;
    }
}
