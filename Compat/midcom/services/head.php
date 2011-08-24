<?php
use Midgard\MidcomCompatBundle\DependencyInjection\RequestAware;

class midcom_services_head extends RequestAware
{
    public function set_pagetitle($title) {
        $this->request->attributes->set('midcom_title', $title);
    }

    private function appendToRequest($key, $value)
    {
        $values = array();
        if ($this->request->attributes->has("midcom_head_{$key}")) {
            $values = $this->request->attributes->get("midcom_head_{$key}");
        }

        if (in_array($value, $values)) {
            return;
        }

        $values[] = $value;
        $this->request->attributes->set("midcom_head_{$key}", $values);
    }

    public function add_link_head(array $attributes = null)
    {
        $this->appendToRequest('links', $attributes);
    }

    public function add_stylesheet($url, $media = false)
    {
        $attributes = array(
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'href' => $url,
        );

        if ($media) {
            $attributes['media'] = $media;
        }

        $this->add_link_head($attributes);
    }

    public function add_jsfile($url, $prepend = false)
    {
        $this->appendToRequest('jsfiles', $url);
    }

    public function add_jscript($script)
    {
        $this->appendToRequest('jscripts', $script);
    }

    public function add_jquery_state_script($script, $state = 'document.ready')
    {
        $values = array();
        if ($this->request->attributes->has('midcom_head_statescripts')) {
            $values = $this->request->attributes->get('midcom_head_statescripts');
        }

        if (!isset($values[$state])) {
            $values[$state] = '';
        }

        $values[$state] .= "\n{$script}\n";
        $this->request->attributes->set('midcom_head_statescripts', $values);
    }

    public function add_meta_head(array $attributes = null)
    {
        $this->appendToRequest('meta', $attributes);
    }

    public function enable_jquery($version = null)
    {
        if (!$version) {
            $version = $GLOBALS['midcom_config']['jquery_version'];
        }

        if (!defined('MIDCOM_JQUERY_UI_URL')) {
            define('MIDCOM_JQUERY_UI_URL', MIDCOM_STATIC_URL . "/jQuery/jquery-ui-{$GLOBALS['midcom_config']['jquery_ui_version']}");
        }

        $this->add_jsfile(MIDCOM_STATIC_URL . "/jQuery/jquery-{$version}.js");
        $this->add_jscript('var MIDCOM_STATIC_URL="' . MIDCOM_STATIC_URL . "\";\n");
        $this->add_jscript("var MIDCOM_PAGE_PREFIX=\"/\";\n");
    }

    public function add_jquery_ui_theme(array $components = array())
    {
        if (!empty($GLOBALS['midcom_config']['jquery_ui_theme']))
        {
            $this->add_stylesheet($GLOBALS['midcom_config']['jquery_ui_theme']);
        }
        else
        {
            $url_prefix = MIDCOM_JQUERY_UI_URL . '/themes/base/minified/jquery.ui.';
            $this->add_stylesheet($url_prefix . 'theme.min.css');
            $this->add_stylesheet($url_prefix . 'core.min.css');
            foreach ($components as $component)
            {
                $this->add_stylesheet($url_prefix . $component . '.min.css');
            }
        }
    }

    public function print_head_elements()
    {
        if ($this->request->attributes->has('midcom_head_links')) {
            $links = $this->request->attributes->get('midcom_head_links');
            foreach ($links as $link) {
                $output = '';
                foreach ($link as $key => $value) {
                    $output .= " {$key}=\"{$value}\" ";
                }
                echo "<link{$output}/>\n";
            }
        }

        if ($this->request->attributes->has('midcom_head_jsfiles')) {
            $jsfiles = $this->request->attributes->get('midcom_head_jsfiles');
            foreach ($jsfiles as $jsfile) {
                echo "<script type=\"text/javascript\" src=\"{$jsfile}\"></script>\n";
            }
        }

        if ($this->request->attributes->has('midcom_head_jscripts')) {
            echo "<script type=\"text/javascript\">\n";
            $jscripts = $this->request->attributes->get('midcom_head_jscripts');
            foreach ($jscripts as $jscript) {
                echo $jscript;
            }
            echo "\n</script>\n";
        }

        if ($this->request->attributes->has('midcom_head_statescripts')) {
            echo "<script type=\"text/javascript\">";
            $statescripts = $this->request->attributes->get('midcom_head_statescripts');
            foreach ($statescripts as $state => $script) {
                $stateParts = explode('.', $state);
                echo "\njQuery({$stateParts[0]}).{$stateParts[1]}(function() { {$script} });\n";
            }
            echo "</script>\n";
        }
    }

    public function print_jsonload()
    {
    }
}
