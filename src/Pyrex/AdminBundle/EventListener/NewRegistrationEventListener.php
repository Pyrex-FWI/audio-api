<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan. 
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna. 
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus. 
 * Vestibulum commodo. Ut rhoncus gravida arcu. 
 */

namespace Pyrex\AdminBundle\EventListener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Pyrex\CoreModelBundle\Entity\Deejay;

class NewRegistrationEventListener
{

    /** @var \Swift_Mailer  */
    private $mailer;

    /**
     * NewRegistrationEventListener constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only act on some "Product" entity
        if (!$entity instanceof Deejay) {
            return;
        }

        $entityManager = $args->getEntityManager();
        //send mail to inform that new user was created
    }
}