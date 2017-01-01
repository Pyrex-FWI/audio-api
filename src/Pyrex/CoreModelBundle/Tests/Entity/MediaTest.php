<?php

namespace Pyrex\CoreModelBundle\Tests\Entity;

use Pyrex\CoreModelBundle\Tests\EntityBase;
use Doctrine\Common\Collections\ArrayCollection;

class MediaTest extends EntityBase
{
    public function testMediaMethods()
    {
        $media = self::getMediaInstance();
        $genre = self::getGenreInstance('Genre');
        $addGenre = self::getGenreInstance('Additional genre');
        $artist = self::getArtistInstance('artist');
        $addArtist = self::getArtistInstance('Additional artist');

        $md5 = md5('md5');
        $media
            ->setArtist('ArtistName')
            ->setTitle('Title')
            ->setGenres(new ArrayCollection([$genre]))
            ->setBpm(120)
            ->setDeletedAt(new \DateTime('now'))
            ->setDirName('DirName')
            ->setFileName('FileName')
            ->setFullFilePathMd5($md5)
            ->setFullPath(__DIR__)
            ->setReleaseDate(new \DateTime('now'))
            ->setScore(3)
            ->setTagged(true)
            ->setType(2)
            ->setYear(2000)
            ->setVersion('Explicit')
            ->addGenre($addGenre)
            ->removeGenre($addGenre)
            ->addArtist($addArtist)
            ->removeArtist($addArtist)
            ->setArtists(new ArrayCollection([$artist]))
            ->setExist(false)
        ;

        $this->assertEquals($media->getArtist(), 'ArtistName');
        $this->assertEquals($media->getTitle(), 'Title');
        $this->assertEquals($media->getGenres()->get(0), $genre);
        $this->assertEquals($media->getArtists()->get(0), $artist);
        $this->assertEquals($media->getBpm(), 120);
        $this->assertEquals(get_class($media->getDeletedAt()), 'DateTime');
        $this->assertEquals(get_class($media->getReleaseDate()), 'DateTime');
        $this->assertEquals($media->getExist(), false);
        $this->assertEquals($media->getFileName(), 'Entity');
        $this->assertEquals($media->getFullFilePathMd5(), md5(__DIR__));
        $this->assertEquals($media->getFullPath(), __DIR__);
        $this->assertEquals($media->getScore(), 3);
        $this->assertEquals($media->getVersion(), 'Explicit');
        $this->assertEquals($media->getDirName(), 'tests');
        $media->getId();
        $this->assertEquals($media->getType(), 2);
    }

    public function testWrongBpm()
    {
        $this->assertNull(self::getMediaInstance()->setBpm(-100.67)->getBpm());
        $this->assertEquals(self::getMediaInstance()->setBpm(+100.67)->getBpm(), 100.67);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongType()
    {
        self::getMediaInstance()->setType(100);
    }

    public function testTypes()
    {
        $this->assertArrayHasKey('audio', self::getMediaInstance()->getTypes());
        $this->assertArrayHasKey('video', self::getMediaInstance()->getTypes());
    }

    public function testWrongYear()
    {
        $this->assertNull(self::getMediaInstance()->setYear('toto')->getYear());
    }
}
