<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\DataFixtures\Processor;


use Nelmio\Alice\ProcessorInterface;
use Pyrex\CoreModelBundle\Entity\Deejay;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Class UserProcessor
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\DataFixtures\ORM\Processor
 */
class UserProcessor implements ProcessorInterface
{
    protected $encoder;

    /**
     * UserProcessor constructor.
     * @param $encoder
     */
    public function __construct(UserPasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param object $object
     */
    public function preProcess($object)
    {
        if (!$object instanceof Deejay) {
            return;
        }

        $password = $this->encoder->encodePassword($object, $object->getPassword());
        $object->setPassword($password);
    }

    /**
     * @param object $object
     */
    public function postProcess($object)
    {
        if (!$object instanceof Deejay) {
            return;
        }

        //$object->eraseCredentials();
    }
}