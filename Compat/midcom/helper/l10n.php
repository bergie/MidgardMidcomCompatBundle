<?php
class midcom_helper_l10n
{
    public function get($string, $language = null)
    {
        return "!!{$string}";
    }

    public function string_available($string)
    {
        return false;
    }

    public function show($string, $language = null)
    {
        echo $this->get($string, $language);
    }
}
