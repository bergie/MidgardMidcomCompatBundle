<?php
class midcom_services_head
{
    public function add_link_head($attributes = null)
    {
    }

    public function add_stylesheet($url, $media = false)
    {
    }

    public function add_jsfile($url, $prepend = false)
    {
    }

    public function enable_jquery($version = null)
    {
        if (!defined('MIDCOM_JQUERY_UI_URL'))
        {
            define('MIDCOM_JQUERY_UI_URL', MIDCOM_STATIC_URL . "/jQuery/jquery-ui-{$GLOBALS['midcom_config']['jquery_ui_version']}");
        }
    }
}
