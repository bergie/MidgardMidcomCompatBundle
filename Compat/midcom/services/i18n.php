<?php
use Symfony\Component\DependencyInjection\ContainerAware;

class midcom_services_i18n extends ContainerAware
{
    public function get_l10n($component = 'midcom', $database = 'default')
    {
        return new midcom_helper_l10n($this->container->get('translator'), $component);
    }

    public function get_string($string, $component, $database = null)
    {
        return $this->container->get('translator')->trans($string, array(), $component);
    }

    public function get_current_language()
    {
        return 'en';
    }

    public function list_languages()
    {
        return array();
    }

    public function get_language_db()
    {
        return array('en' => array('locale' => 'en_US'));
    }
}
