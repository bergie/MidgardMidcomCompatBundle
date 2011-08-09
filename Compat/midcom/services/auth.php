<?php
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class midcom_services_auth extends ContainerAware
{
    public function require_do($privilege, $object, $message = null)
    {
        if (!$this->can_do($privilege, $object)) {
            throw new AccessDeniedException($message);
        }
    }

    public function require_valid_user()
    {
        return (null !== $this->user);
    }

    public function can_do($privilege, $object = null)
    {
        try {
            return $this->container->get('security.context')->isGranted($privilege, $object);
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    public function can_user_do($privilege, $user = null, $class = null, $component = null)
    {
        return true;
    }

    public function __get($key)
    {
        if ($key == 'user') {
            $token = $this->container->get('security.context')->getToken();
            if (!$token) {
                return null;
            }
            return $token->getUser();
        }

        if ($key == 'admin') {
            return $this->can_do('ROLE_ADMIN');
        }
    }
}
