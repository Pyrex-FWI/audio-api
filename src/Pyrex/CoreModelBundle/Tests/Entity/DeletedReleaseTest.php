<?php

namespace Pyrex\CoreModelBundle\Tests\Entity;


use Pyrex\CoreModelBundle\Tests\EntityBase;

class DeletedReleaseTest extends EntityBase
{

    public function  testGenreMethods()
    {
        $deletedRelease = self::getDeletedReleaseInstance();
        $now = new \DateTime('now');
        $deletedRelease
            ->setAlbumName('AlbumName')
            ->setArtistName('ArtistName')
            ->setDeletedDate($now)
            ->setGenre('Genre')
            ->setRawName('Raw_Name')
            ;
        $this->assertEquals('AlbumName', $deletedRelease->getAlbumName());
        $this->assertEquals('ArtistName', $deletedRelease->getArtistName());
        $this->assertEquals($now, $deletedRelease->getDeletedDate());
        $this->assertEquals('Genre', $deletedRelease->getGenre());
        $this->assertEquals('Raw_Name', $deletedRelease->getRawName());
        $deletedRelease->getId();
    }
}
