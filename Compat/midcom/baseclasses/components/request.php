<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

abstract class midcom_baseclasses_components_request
{
    public $_topic = null;
    public $_config = null;
    public $_request_data = array();
    public $_request_switch = array();

    /**
     * Prepares MidCOM-level Request Data and calls the Component's
     * _on_initialize method
     */
    public function initialize(Request $request, ParameterBag $config)
    {
        $this->_request_data['config'] = $config;
        $this->_config =& $this->_request_data['config'];

        if ($request->attributes->has('midcom_topic')) {
            $this->_topic = $request->attributes->get('midcom_topic');
        } else {
            $this->_topic = $this->prepare_topic($request);
        }
        $this->_request_data['topic'] = $this->_topic;

        $this->_node_toolbar = new midcom_helper_toolbar();
        $this->_view_toolbar = new midcom_helper_toolbar();
        $this->_l10n = $_MIDCOM->i18n->get_l10n();
        $this->_request_data['l10n'] = $this->_l10n;

        $this->_on_initialize();
    }

    public function _on_initialize()
    {
    }

    private function prepare_topic(Request $request)
    {
        $topic = new midgard_topic();
        $topic->name = 'midcom';
        $topic->extra = 'MidCOM topic';
        $topic->create();
        return $topic;
    }

    /**
     * Calls handling methods of the viewer, and then handling methods
     * of the controller
     */
    public function handle(Request $request)
    {
        $controllerClass = $request->attributes->get('midcom_controller');
        $controllerMethod = $request->attributes->get('midcom_action');
        $controller = new $controllerClass();

        $controller->_node_toolbar = $this->_node_toolbar;
        $controller->_view_toolbar = $this->_view_toolbar;

        $this->_request_data['handler_id'] = $request->attributes->get('midcom_route_id');

        $this->_on_handle($request->attributes->get('midcom_route_id'), $request->attributes->all());

        $controller->$controllerMethod($request->attributes->get('midcom_route_id'), $request->attributes->all(), $this->_request_data);

        $request->attributes->set('midcom_request_data', $this->_request_data);
    }

    public function _on_handle($handler_id, array $args)
    {
        return true;
    }

    public function add_stylesheet($url)
    {
    }
}
