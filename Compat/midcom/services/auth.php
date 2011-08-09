<?php
class midcom_services_auth
{
    public $user = null;

    public function require_do($privilege, $object, $message = null)
    {
        if (!$this->can_do($privilege, $object)) {
            throw new Exception($message);
        }
    }

    public function can_do($privilege, $object)
    {
        return true;
    }
}
