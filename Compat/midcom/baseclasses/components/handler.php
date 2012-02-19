<?php
abstract class midcom_baseclasses_components_handler extends midcom_baseclasses_components_base
{
    public $_component = '';
    public $_config;
    public $_request_data = array();
    protected $_master = null;

    public function set_master(midcom_baseclasses_components_request $master)
    {
        $this->_master = $master;
    }

    public function _on_initialize()
    {
    }

    public function add_stylesheet($url, $media = false)
    {
        $_MIDCOM->add_link_head(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => $url, $media));
    }

    public function add_breadcrumb($url, $title)
    {
    }

    public function set_active_leaf($leaf_id)
    {
    }

    public function get_controller($type, $object = null)
    {
        switch ($type)
        {
            case 'simple':
                return midcom_helper_datamanager2_handler::get_simple_controller($this, $object);
            case 'nullstorage':
                return midcom_helper_datamanager2_handler::get_nullstorage_controller($this);
            case 'create':
                return midcom_helper_datamanager2_handler::get_create_controller($this);
            default:
                throw new midcom_error("Unsupported controller type: {$type}");
        }
    }
}
