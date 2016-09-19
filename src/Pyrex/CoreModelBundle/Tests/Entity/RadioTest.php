<?php

namespace Pyrex\CoreModelBundle\Tests\Entity;


use Pyrex\CoreModelBundle\Tests\EntityBase;

class RadioTest extends EntityBase
{

    public function  testGenreMethods()
    {
        $radio = self::getRadioInstance();
        $hitUrls = [
            "http://someurl.com/toto/dld?dff=f",
            "http://someurl.com/toto/dld?dff=f",
            "http://someurl.com/toto/dld?dff=f",
        ];
        $radio
            ->setName('NRJ')
            ->setHitPagesUrls($hitUrls)
        ;

        $this->assertContains($hitUrls[0], $radio->getHitPagesUrls());
        $this->assertEquals('NRJ', $radio->getName());
        $radio->getId();
    }
}
