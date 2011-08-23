<?php
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;
use Symfony\Component\HttpFoundation\Request;

class midcom_services_uimessages extends RequestAware
{
    public function setRequest(Request $request)
    {
        parent::setRequest($request);
        $this->initialize();
    }

    private function initialize()
    {
        if ($_MIDCOM->auth->can_do('midcom:ajax')) {
            $_MIDCOM->head->enable_jquery();
            $_MIDCOM->head->add_jsfile(MIDCOM_STATIC_URL . '/midcom.services.uimessages/jquery.midcom_services_uimessages.js');
            $_MIDCOM->head->add_jsfile(MIDCOM_STATIC_URL . '/jQuery/jquery.timers.src.js');
            $_MIDCOM->head->add_jsfile(MIDCOM_JQUERY_UI_URL . '/ui/jquery.effects.core.min.js');
            $_MIDCOM->head->add_jsfile(MIDCOM_JQUERY_UI_URL . '/ui/jquery.effects.pulsate.min.js');

            $_MIDCOM->head->add_stylesheet(MIDCOM_STATIC_URL . '/midcom.services.uimessages/growl.css', 'screen');
        } else {
            $_MIDCOM->head->add_stylesheet(MIDCOM_STATIC_URL . '/midcom.services.uimessages/simple.css', 'screen');
        }
    }

    public function show()
    {
        if (!$_MIDCOM->auth->can_do('midcom:ajax')) {
            return;
        }

        $flashes = $this->container->get('session')->getFlashes();

        echo "<script type=\"text/javascript\">\n";
        echo "jQuery(document).ready(function() {\n";
        echo "  if (jQuery('#midcom_services_uimessages_wrapper').size() === 0) {\n";
        echo "    jQuery('<div></div>').attr({id: 'midcom_services_uimessages_wrapper'}).appendTo('body');\n";
        echo "  }\n";

        foreach ($flashes as $title => $message) {
            $message = str_replace('"', "'", $message);
            $title = str_replace('"', "'", $title);
            echo "    jQuery('#midcom_services_uimessages_wrapper').midcom_services_uimessage({title: \"{$title}\", message: \"{$message}\", type: \"info\"});\n";
        }
        echo "});\n</script>\n";
    }

    public function add($title, $message, $type = 'info')
    {
        $this->container->get('session')->setFlash($title, $message);
    }
}
