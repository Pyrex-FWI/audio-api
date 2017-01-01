<?php

namespace Pyrex\CoreModelBundle\Tests\Entity;

use Pyrex\CoreModelBundle\Tests\EntityBase;

class GenreTest extends EntityBase
{
    public function testGenreMethods()
    {
        $genre = self::getGenreInstance();
        $media = self::getMediaInstance();
        $genre
            ->setName('Pop')
            ->setMedias($media);
        $genre->getId();
        $genre->getMedias();
        $this->assertEquals('Pop', $genre->getName());
        $this->assertEquals($media, $genre->getMedias());
    }
}
