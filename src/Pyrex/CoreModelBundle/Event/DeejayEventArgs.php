<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Pyrex\CoreModelBundle\Event;

use Pyrex\CoreModelBundle\Entity\Deejay;

/**
 * Class DeejayEventArgs.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 */
class DeejayEventArgs
{
    /** @var Deejay */
    private $deejay;

    /**
     * DeejayEventArgs constructor.
     *
     * @param Deejay $deejay
     */
    public function __construct(Deejay $deejay)
    {
        $this->deejay = $deejay;
    }

    /**
     * @return Deejay
     */
    public function getData()
    {
        return $this->deejay;
    }
}
