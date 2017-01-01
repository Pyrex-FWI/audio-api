<?php

namespace Pyrex\CoreModelBundle\Tests\Entity;

use Pyrex\CoreModelBundle\Tests\EntityBase;

class RadioHitTest extends EntityBase
{
    public function testGenreMethods()
    {
        $radioHit = self::getRadioHitInstance();
        $genre = self::getGenreInstance('Genre');
        $genre2 = self::getGenreInstance('Genre2');
        $created = new \DateTime('now');
        $radioHit
            ->setArtist('Artist')
            ->setTitle('Title')
            ->setCreated($created)
            ->addGenre($genre)
            ->addGenre($genre2)
            ->removeGenre($genre)

        ;

        $this->assertContains('Artist', $radioHit->getArtist());
        $this->assertEquals('Title', $radioHit->getTitle());
        $this->assertEquals($created, $radioHit->getCreated());
        $this->assertEquals($genre2, $radioHit->getGenres()->get(1));
        $radioHit->getId();
    }
}
