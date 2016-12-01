<?php

namespace AppBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;

/**
 * SwitvhUserListener.
 */
class SwitchUserListener
{
    /** @var  LoggerInterface */
    private $logger;

    /**
     * SwitchUserListener constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param SwitchUserEvent $event
     */
    public function onSwitchUser(SwitchUserEvent $event)
    {
        $targetUser = $event->getTargetUser();
        $previousUser = null;
        foreach ($targetUser->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                dump($role);
            }
        }
        //$this->logger->info(sprintf('%s switch to %s', $previousUser->name, $targetUser->getUsername()));
    }
}
