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
        $this->_l10n_midcom = $_MIDCOM->i18n->get_l10n();
        $this->_request_data['l10n'] = $this->_l10n;
        $this->_request_data['l10n_midcom'] = $this->_l10n_midcom;

        $this->_on_initialize();
    }

    public function _on_initialize()
    {
    }

    private function prepare_topic(Request $request)
    {
        $qs = new midgard_query_select(new midgard_query_storage('midgard_topic'));
        $qs->set_constraint(
            new midgard_query_constraint(
                new midgard_query_property('name'),
                '=',
                new midgard_query_value('midcom')
            )
        );
        $qs->execute();
        $topics = $qs->list_objects();
        if ($topics)
        {
            return $_MIDCOM->dbfactory->convert_midgard_to_midcom($topics[0]);
        }
        $topic = new midgard_topic();
        $topic->name = 'midcom';
        $topic->extra = 'MidCOM topic';
        $topic->create();
        return $_MIDCOM->dbfactory->convert_midgard_to_midcom($topic);
    }

    /**
     * Calls handling methods of the viewer, and then handling methods
     * of the controller
     */
    public function handle(Request $request, array $args)
    {
        $controllerClass = $request->attributes->get('midcom_controller');
        $controller = new $controllerClass();
        $controller->_config = $this->_config;
        $controller->_node_toolbar = $this->_node_toolbar;
        $controller->_view_toolbar = $this->_view_toolbar;
        $controller->_topic = $this->_topic;
        $controller->_l10n = $this->_l10n;
        $controller->_l10n_midcom = $this->_l10n_midcom;
        $controller->_request_data =& $this->_request_data;

        $this->_request_data['handler_id'] = $request->attributes->get('midcom_route_id');

        $controllerCanMethod = '_can_handle_' . $request->attributes->get('midcom_action');
        if (method_exists($controller, $controllerCanMethod))
        {
            $controller->$controllerCanMethod($request->attributes->get('midcom_route_id'), $args, $this->_request_data);
        }
        $this->_on_handle($request->attributes->get('midcom_route_id'), $args);

        $controllerMethod = '_handler_' . $request->attributes->get('midcom_action');
        $controller->$controllerMethod($request->attributes->get('midcom_route_id'), $args, $this->_request_data);

        $request->attributes->set('midcom_controller_instance', $controller);
        $request->attributes->set('midcom_request_data', &$this->_request_data);
    }

    public function _on_handle($handler_id, $args)
    {
        return true;
    }

    public function show(Request $request)
    {
        $controller = $request->attributes->get('midcom_controller_instance');
        $controllerMethod = '_show_' . $request->attributes->get('midcom_action');

        $controller->$controllerMethod($request->attributes->get('midcom_route_id'), &$this->_request_data);

        $request->attributes->set('midcom_request_data', &$this->_request_data);
    }

    public function _on_show($handler_id)
    {
        return true;
    }

    public function add_stylesheet($url)
    {
    }
}
