<?php

namespace Pyrex\CoreModelBundle\Tests\Entity;


use Pyrex\CoreModelBundle\Tests\EntityBase;

class AlbumTest extends EntityBase
{

    public function  testGenreMethods()
    {
        $album = self::getAlbumInstance();
        $media = self::getMediaInstance();
        $album
            ->setName('Album');
        $album->getId();
        $this->assertEquals('Album', $album->getName());
    }
}
