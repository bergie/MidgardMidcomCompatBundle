<?php
class midcom_helper_l10n
{
    public function get($string)
    {
        return "!!{$string}";
    }

    public function string_available($string)
    {
        return false;
    }
}
