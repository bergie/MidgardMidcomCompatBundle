<?php
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Midgard\ConnectionBundle\Security\User\User;

class midcom_services_auth extends ContainerAware
{
    private $sudo_mode = 0;

    private function map_privilege($privilege)
    {
        switch ($privilege) {
            case 'midgard:update':
            case 'midgard:create':
            case 'midgard:parameters':
            case 'midgard:attachments':
                return 'EDIT';
            case 'midgard:read':
                return 'VIEW';
            case 'midgard:delete':
                return 'DELETE';
            case 'midgard:privileges':
                return 'MASTER';
            case 'midgard:owner':
                return 'OWNER';
            default:
                return $privilege;
        }
    }

    public function require_do($privilege, $object, $message = null)
    {
        if ($this->can_do($privilege, $object)) {
            return;
        }

        if (!$message) {
            $message = $this->container->get('translator')->trans('access denied: privileges required', array(), 'midcom');
        }

        throw new AccessDeniedException($message);
    }

    public function require_user_do($privilege, $message = null, $class = null)
    {
        if ($this->can_user_do($privilege, null, $class))
        {
            return;
        }

        if (!$message)
        {
            $message = $this->container->get('translator')->trans('access denied: privileges required', array(), 'midcom');
        }

        throw new AccessDeniedException($message);
    }

    public function can_do($privilege, $object = null)
    {
        if ($this->sudo_mode > 0) {
            return true;
        }

        try {
            if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
                return true;
            }
            $privilege = $this->map_privilege($privilege);
            return $this->container->get('security.context')->isGranted($privilege, $object);
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    public function is_valid_user()
    {
        $token = $this->container->get('security.context')->getToken();
        if (!$token) {
            return false;
        }
        return true;
    }

    public function require_valid_user($method = 'form')
    {
        if (!$this->is_valid_user()) {
            $message = $this->container->get('translator')->trans('authentication required', array(), 'midcom');
            throw new AccessDeniedException($message);
        }
    }

    public function require_admin_user($message = null)
    {
        if ($this->admin) {
            return;
        }
        if (!$message) {
            $message = $this->container->get('translator')->trans('access denied: admin level privileges required', array(), 'midcom');
        }
        throw new AccessDeniedException($message);
    }

    public function can_user_do($privilege, $user = null, $class = null, $component = null)
    {
        return true;
    }

    public function get_user($id)
    {
    }

    public function get_group($id)
    {
    }

    public function request_sudo($component = null)
    {
        $this->sudo_mode++;
        return true;
    }

    public function drop_sudo()
    {
        $this->sudo_mode--;
    }

    private function get_midgard_user()
    {
        $token = $this->container->get('security.context')->getToken();
        if (!$token) {
            return null;
        }
        return $this->get_person_for_user($token->getUser());
    }

    private function get_person_for_user($user)
    {
        if (!$user instanceof User) {
            // Non-Midgard user
            return null;
        }

        $mgdUser = $user->getMidgardUser();
        if (!$mgdUser) {
            return null;
        }
        $person = $mgdUser->get_person();
        return new midcom_core_user($person->guid);
    }

    public function __get($key)
    {
        if ($key == 'user') {
            return $this->get_midgard_user();
        }

        if ($key == 'admin') {
            return $this->can_do('ROLE_ADMIN');
        }

        if ($key == 'acl') {
            $this->acl = new midcom_services_auth_acl($this);
            return $this->acl;
        }
    }
}
