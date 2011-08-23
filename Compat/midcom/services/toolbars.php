<?php
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;
use Symfony\Component\HttpFoundation\Request;

class midcom_services_toolbars extends RequestAware
{
    public function setRequest(Request $request)
    {
        parent::setRequest($request);
        $this->initialize();
    }

    public function initialize()
    {
        if (   !$GLOBALS['midcom_config']['toolbars_enable_centralized']
            || !$_MIDCOM->auth->can_do('midcom:centralized_toolbar'))
        {
            return;
        }

        if (!$_MIDCOM->auth->can_do('midcom:ajax'))
        {
            return;
        }

        $_MIDCOM->head->enable_jquery();
        $_MIDCOM->head->add_jsfile(MIDCOM_STATIC_URL . '/jQuery/jquery.timers.src.js');
        $_MIDCOM->head->add_jsfile(MIDCOM_JQUERY_UI_URL . '/ui/jquery.ui.core.min.js');
        $_MIDCOM->head->add_jsfile(MIDCOM_JQUERY_UI_URL . '/ui/jquery.ui.widget.min.js');
        $_MIDCOM->head->add_jsfile(MIDCOM_JQUERY_UI_URL . '/ui/jquery.ui.mouse.min.js');
        $_MIDCOM->head->add_jsfile(MIDCOM_JQUERY_UI_URL . '/ui/jquery.ui.draggable.min.js');
        $_MIDCOM->head->add_jsfile(MIDCOM_STATIC_URL . '/midcom.services.toolbars/jquery.midcom_services_toolbars.js');
        $_MIDCOM->head->add_stylesheet(MIDCOM_STATIC_URL . '/midcom.services.toolbars/fancy.css', 'screen');

        $script = "jQuery('body div.midcom_services_toolbars_fancy').midcom_services_toolbar({});";
        $_MIDCOM->head->add_jquery_state_script($script);
    }

    public function get_host_toolbar()
    {
        return new midcom_helper_toolbar('', '', $this->container->get('midgard.toolbar.provider')->get('host'));
    }

    public function get_node_toolbar()
    {
        return new midcom_helper_toolbar('', '', $this->container->get('midgard.toolbar.provider')->get('node'));
    }

    public function get_view_toolbar()
    {
        return new midcom_helper_toolbar('', '', $this->container->get('midgard.toolbar.provider')->get('view'));
    }

    public function get_help_toolbar()
    {
        return new midcom_helper_toolbar('', '', $this->container->get('midgard.toolbar.provider')->get('help'));
    }

    public function render_host_toolbar()
    {
        try {
            return $this->container->get('midgard.toolbar.provider')->render('host');
        } catch (\InvalidArgumentException $e) {
            return '';
        }
    }

    public function render_node_toolbar()
    {
        return $this->container->get('midgard.toolbar.provider')->render('node');
    }

    public function render_help_toolbar()
    {
        try {
            return $this->container->get('midgard.toolbar.provider')->render('help');
        } catch (\InvalidArgumentException $e) {
            return '';
        }
    }

    public function show_view_toolbar()
    {
        $this->container->get('midgard.toolbar.provider')->show('view');
    }
}
