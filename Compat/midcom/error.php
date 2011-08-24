<?php
class midcom_error extends Exception
{


    public function log($loglevel = MIDCOM_LOG_ERROR)
    {
        debug_add($this->getMessage(), $loglevel);
    }
}
