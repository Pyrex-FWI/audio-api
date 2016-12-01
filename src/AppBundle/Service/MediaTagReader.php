<?php

namespace AppBundle\Service;

use AppBundle\Id3\Id3Manager;
use Sapar\Id3\Metadata\Id3Metadata;

/**
 * Class MediaTagReader
 * @package AppBundle\Service
 */
class MediaTagReader
{

    /** @var  Id3Manager */
    private $id3Manager;

    /**
     * MediaTagReader constructor.
     * @param Id3Manager $id3Manager
     */
    public function __construct(Id3Manager $id3Manager)
    {
        /** @var \AppBundle\Id3\Id3Manager $id3Tool */
        $this->id3Manager  = $id3Manager;
    }

    /**
     * @param string $file
     * @return Id3Metadata|null
     */
    public function read($file)
    {
        $id3Meta = new Id3Metadata($file);
        if ($this->id3Manager->read($id3Meta)) {
            return $id3Meta;
        }
    }

}