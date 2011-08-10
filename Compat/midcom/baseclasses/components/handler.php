<?php
abstract class midcom_baseclasses_components_handler
{
    public $_component = '';
    public $_config;
    public $_request_data = array();

    private $_services = array();

    public function add_stylesheet($url, $media = false)
    {
    }

    public function add_breadcrumb($url, $title)
    {
    }

    public function __get($field)
    {
        if (array_key_exists($field, $this->_services))
        {
            return $this->_services[$field];
        }

        $instance = null;
        switch ($field)
        {
            case '_i18n':
                $instance = $_MIDCOM->get_service('i18n');
                break;
            case '_l10n':
                $instance = $_MIDCOM->i18n->get_l10n($this->_component);
                break;
            case '_l10n_midcom':
                $instance = $_MIDCOM->i18n->get_l10n('midcom');
                break;
            default:
                debug_add('Component ' . $this->_component . ' tried to access nonexistant service "' . $field . '"', MIDCOM_LOG_ERROR);
                debug_print_function_stack('Called from here:');
                return false;
        }
        $this->_services[$field] = $instance;
        return $this->_services[$field];
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
