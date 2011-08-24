<?php
class midcom_error_midgard extends midcom_error
{
    public function __construct(midgard_error_exception $e, $id = null)
    {
        //catch last error which might be from dbaobject
        $last_error = midcom_connection::get_error();

        if (!is_null($id))
        {
            if ($last_error === MGD_ERR_NOT_EXISTS)
            {
                $code = MIDCOM_ERRNOTFOUND;
                $message = "The object with identifier {$id} was not found.";
            }

            else if ($last_error == MGD_ERR_ACCESS_DENIED)
            {
                $code = MIDCOM_ERRFORBIDDEN;
                $message = $_MIDCOM->i18n->get_string('access denied', 'midcom');
            }
            else if ($last_error == MGD_ERR_OBJECT_DELETED)
            {
                $code = MIDCOM_ERRNOTFOUND;
                $message = "The object with identifier {$id} was deleted.";
            }
        }
        //If other options fail, go for the server error
        if (!isset($code))
        {
            $code = MIDCOM_ERRCRIT;
            $message = $e->getMessage();
        }
        parent::__construct($message, $code);
    }
}
