<?php

namespace Pyrex\CoreModelBundle\Tests;

use Pyrex\CoreModelBundle\Entity\Album;
use Pyrex\CoreModelBundle\Entity\Artist;
use Pyrex\CoreModelBundle\Entity\DeletedRelease;
use Pyrex\CoreModelBundle\Entity\Genre;
use Pyrex\CoreModelBundle\Entity\Media;
use Pyrex\CoreModelBundle\Entity\Radio;
use Pyrex\CoreModelBundle\Entity\RadioHit;

abstract class EntityBase extends \PHPUnit_Framework_TestCase
{

    /**
     * @param null $name
     * @return Genre
     */
    static public function getGenreInstance($name = null)
    {
        return new Genre($name);
    }

    static public function getMediaInstance()
    {
        return new Media();
    }

    /**
     * @param null $name
     * @return Genre
     */    static public function getArtistInstance($name = null)
    {
        return new Artist($name);
    }

    /**
     * @param null $name
     * @return Album
     */
    static public function getAlbumInstance($name = null)
    {
        return new Album($name);
    }
    /**
     * @return DeletedRelease
     */
    static public function getDeletedReleaseInstance()
    {
        return new DeletedRelease();
    }
    /**
     * @return Radio
     */
    static public function getRadioInstance()
    {
        return new Radio();
    }
    /**
     * @return RadioHit
     */
    static public function getRadioHitInstance()
    {
        return new RadioHit();
    }
}