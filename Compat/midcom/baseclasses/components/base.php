<?php
abstract class midcom_baseclasses_components_base
{
    private $_services = array();

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
}
?>