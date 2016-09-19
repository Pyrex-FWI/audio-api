<?php

namespace Pyrex\CoreModelBundle\Tests\Entity;


use Pyrex\CoreModelBundle\Tests\EntityBase;

class ArtistTest extends EntityBase
{

    public function  testGenreMethods()
    {
        $artist = self::getArtistInstance();
        $media = self::getMediaInstance();
        $artist
            ->setName('Artist')
            ->setMedias($media);
        $artist->getId();
        $artist->getMedias();
        $this->assertEquals('Artist', $artist->getName());
        $this->assertEquals($media, $artist->getMedias());
    }
}
