<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Service;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class SecuritySwitchUser
{

    private $storage;

    /**
     * SecuritySwitchUser constructor.
     * @param TokenStorage $storage
     */
    public function __construct(TokenStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return mixed
     */
    public function getPrimaryUser()
    {
        $token = $this->storage->getToken();
        foreach ($token->getRoles() as $role) {
            if ($role instanceof  SwitchUserRole) {
                return $role->getSource()->getUser();
            }
        }
    }
}