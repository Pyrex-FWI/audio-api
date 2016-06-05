<?php

namespace AppBundle\Event;

class DirectoryEvent extends \Symfony\Component\EventDispatcher\Event
{
    private $dirName;
    private $oldRealPathName;
    private $genreName;
    private $albumName;
    private $artist;
    private $year;

    public function __construct(\SplFileInfo $dirName, $genreName = null, $albumName = null, $artist = null, $year = null)
    {
        $this->dirName         = $dirName->getFilename();
        $this->oldRealPathName = $dirName->getRealPath();
        $this->genreName       = $genreName;
        $this->albumName       = $albumName;
        $this->artist          = $artist;
        $this->year            = $year;
    }

    /**
     * @return mixed
     */
    public function getDirName()
    {
        return $this->dirName;
    }

    /**
     */
    public function getGenreName()
    {
        return $this->genreName;
    }

    /**
     */
    public function getAlbumName()
    {
        return $this->albumName;
    }

    /**
     */
    public function getArtist()
    {
        return $this->artist;
    }
}
