<?php
class midcom_services_i18n
{
    public function get_l10n($component = 'midcom', $database = 'default')
    {
        return new midcom_helper_l10n();
    }
}
