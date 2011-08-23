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

    public function add_meta_head(array $attributes = null)
    {
        $this->appendToRequest('meta', $attributes);
    }

    public function enable_jquery($version = null)
    {
        if (!defined('MIDCOM_JQUERY_UI_URL')) {
            define('MIDCOM_JQUERY_UI_URL', MIDCOM_STATIC_URL . "/jQuery/jquery-ui-{$GLOBALS['midcom_config']['jquery_ui_version']}");
        }
    }

    public function add_jquery_ui_theme(array $components = array())
    {

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
    }

    public function print_jsonload()
    {
    }
}
