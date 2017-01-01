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
     *
     * @return Genre
     */
    public static function getGenreInstance($name = null)
    {
        return new Genre($name);
    }

    public static function getMediaInstance()
    {
        return new Media();
    }

    /**
     * @param null $name
     *
     * @return Genre
     */
    public static function getArtistInstance($name = null)
    {
        return new Artist($name);
    }

    /**
     * @param null $name
     *
     * @return Album
     */
    public static function getAlbumInstance($name = null)
    {
        return new Album($name);
    }

    /**
     * @return DeletedRelease
     */
    public static function getDeletedReleaseInstance()
    {
        return new DeletedRelease();
    }

    /**
     * @return Radio
     */
    public static function getRadioInstance()
    {
        return new Radio();
    }

    /**
     * @return RadioHit
     */
    public static function getRadioHitInstance()
    {
        return new RadioHit();
    }
}
