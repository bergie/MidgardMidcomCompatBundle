<?php
use Midgard\ToolbarBundle\Toolbar\Toolbar;

class midcom_helper_toolbar
{
    private $toolbar = null;

    public function __construct($class = null, $id = null, Toolbar $toolbar = null)
    {
        if (!$toolbar) {
            $toolbar = new Toolbar($id, $class);
        }
        $this->toolbar = $toolbar;
    }

    public function add_item(array $item)
    {
        $this->toolbar->addItem($this->convert_item($item));
    }

    private function convert_item(array $item)
    {
        $newItem = array();

        $newItem['url'] = $item[MIDCOM_TOOLBAR_URL];
        if (isset($item[MIDCOM_TOOLBAR_OPTIONS])) {
            $newItem['options'] = $item[MIDCOM_TOOLBAR_OPTIONS];
        }
        if (isset($item[MIDCOM_TOOLBAR_HIDDEN])) {
            $newItem['hidden'] = $item[MIDCOM_TOOLBAR_HIDDEN];
        }
        if (isset($item[MIDCOM_TOOLBAR_HELPTEXT])) {
            $newItem['helptext'] = $item[MIDCOM_TOOLBAR_HELPTEXT];
        }
        if (isset($item[MIDCOM_TOOLBAR_ICON])) {
            $newItem['icon'] = MIDCOM_STATIC_URL . "/{$item[MIDCOM_TOOLBAR_ICON]}";
        }
        if (isset($item[MIDCOM_TOOLBAR_ENABLED])) {
            $newItem['enabled'] = $item[MIDCOM_TOOLBAR_ENABLED];
        }
        if (isset($item[MIDCOM_TOOLBAR_POST])) {
            $newItem['post'] = $item[MIDCOM_TOOLBAR_POST];
        }
        if (isset($item[MIDCOM_TOOLBAR_POST_HIDDENARGS])) {
            $newItem['hiddenargs'] = $item[MIDCOM_TOOLBAR_POST_HIDDENARGS];
        }
        if (isset($item[MIDCOM_TOOLBAR_ACCESSKEY])) {
            $newItem['accesskey'] = $item[MIDCOM_TOOLBAR_ACCESSKEY];
        }
        if (isset($item[MIDCOM_TOOLBAR_HELPTEXT])) {
            $newItem['helptext'] = $item[MIDCOM_TOOLBAR_HELPTEXT];
        }
        if (isset($item[MIDCOM_TOOLBAR_LABEL])) {
            $newItem['label'] = $item[MIDCOM_TOOLBAR_LABEL];
        }
        if (isset($item[MIDCOM_TOOLBAR_OPTIONS])) {
            $newItem['options'] = $item[MIDCOM_TOOLBAR_OPTIONS];
        }
        return $newItem;
    }

    public function hide_item($index)
    {
        $this->toolbar->hideItem($index);
    }

    public function render()
    {
        return $this->toolbar->render();
    }
}
