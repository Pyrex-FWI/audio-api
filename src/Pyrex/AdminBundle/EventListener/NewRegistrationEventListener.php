<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan. 
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna. 
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus. 
 * Vestibulum commodo. Ut rhoncus gravida arcu. 
 */

namespace Pyrex\AdminBundle\EventListener;


use AppBundle\Service\SystemEmail;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Pyrex\CoreModelBundle\Entity\Deejay;

/**
 * Class NewRegistrationEventListener
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package Pyrex\AdminBundle\EventListener
 */
class NewRegistrationEventListener
{

    /** @var \Swift_Mailer  */
    private $mailer;
    /** @var SystemEmail  */
    private $systemEmail;
    /**
     * NewRegistrationEventListener constructor.
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