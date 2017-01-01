<?php

namespace AppBundle\EventListener;

use AppBundle\Service\SystemEmail;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Pyrex\CoreModelBundle\Entity\Deejay;

/**
 * Class NewRegistrationEventListener.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 */
class NewRegistrationEventListener
{
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var SystemEmail */
    private $systemEmail;

    /**
     * NewRegistrationEventListener constructor.
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, SystemEmail $systemEmail)
    {
        $this->mailer = $mailer;
        $this->systemEmail = $systemEmail;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        /** @var Deejay $entity */
        $entity = $args->getObject();

        if (!$entity instanceof Deejay) {
            return;
        }

        $this->systemEmail->newRegistrationMail($entity);
    }
}
