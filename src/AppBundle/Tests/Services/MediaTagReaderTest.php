<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Service;

use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class MediaTagReaderTest.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 */
class MediaTagReaderTest extends KernelTestCase
{
    /** @var Prophet */
    private $prophet;

    protected function setUp()
    {
        parent::setUp();
        $this->prophet = new \Prophecy\Prophet();
    }

    /**
     * @test
     */
    public function createReferenceIfNotExist()
    {
    }
}
