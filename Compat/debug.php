<?php
function debug_add($message, $loglevel = MIDCOM_LOG_DEBUG)
{
    midgard_error::debug($message);
}

function debug_print_r($message, $var, $loglevel = MIDCOM_LOG_DEBUG)
{
    midgard_error::debug($message);
}
